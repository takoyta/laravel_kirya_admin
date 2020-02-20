<?php
namespace KiryaDev\Admin\Traits;


use KiryaDev\Admin\Fields\Panel;
use KiryaDev\Admin\Fields\Element;
use KiryaDev\Admin\Fields\HasMany;
use KiryaDev\Admin\Fields\MorphMany;
use KiryaDev\Admin\Fields\BelongsTo;
use KiryaDev\Admin\Fields\FieldElement;

trait HasFields
{
    /**
     * Call getFieldsOnce for get once instances.
     *
     * @return array
     */
    public function fields()
    {
        return [
            // override there your fields and panels
        ];
    }

    /**
     * @return array
     */
    private function getFieldsOnce()
    {
        static $fields = [];

        if (! isset($fields[static::class])) {
            $fields[static::class] = $this->fields();
        }

        return $fields[static::class];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getIndexFields()
    {
        return collect($this->getFieldsOnce())
            ->whereInstanceOf(FieldElement::class)
            ->where('showOnIndex');
    }

    /**
     * @param  \Closure  $filter
     * @return \KiryaDev\Admin\Fields\Panel[]
     */
    protected function getPanelsWithFilter($filter)
    {
        $panels = [];
        $detailFields = [];

        foreach ($this->getFieldsOnce() as $field) {
            if ($field instanceof FieldElement && $filter($field))
                $detailFields[] = $field;

            if ($field instanceof Panel) {
                // fixme: hide panel with empty fields, but show inherited panel
                $field->fields = array_filter($field->fields, $filter);
                $panels[] = $field;
            }
        }

        if ($detailFields) {
            array_unshift($panels, Panel::make('Details', $detailFields));
        }

        return $panels;
    }

    /**
     * @return \KiryaDev\Admin\Fields\Panel[]
     */
    public function getDetailPanels()
    {
        return $this->getPanelsWithFilter(function (Element $element) {
            return $element->showOnDetail;
        });
    }

    /**
     * Return Panels.
     * Also handled disabled fields.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $object
     * @return \KiryaDev\Admin\Fields\Panel[]
     */
    public function getFormPanels($object)
    {
        return $this->getPanelsWithFilter(function (Element $element) use ($object) {
            if ($element instanceof FieldElement) {
                if ($element->computed) {
                    return false;
                }

                // Disable field..
                $resolver = $element->disableResolver;

                switch (true) {
                    case $resolver instanceof \Closure:
                        $element->disabled = $resolver($object);
                        break;

                    case is_bool($resolver):
                        $element->disabled = $resolver;
                        break;

                    default:
                        throw new \RuntimeException('Unsupported resolver type ' . gettype($resolver). '. Supports: Closure, boolean.');
                }
            }

            return $object->exists
                ? $element->showOnUpdate
                : $element->showOnCreation;
        });
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $object
     * @return \Illuminate\Support\Collection
     */
    public function collapseFieldsFromPanels($object)
    {
        return collect($this->getFormPanels($object))
            ->pluck('fields')
            ->collapse()
            ->whereStrict('disabled', false);
    }

    /**
     * @param  \Illuminate\Http\Request             $request
     * @param  \Illuminate\Database\Eloquent\Model  $object
     * @return array
     */
    public function validateOnFields($request, $object)
    {
        return $request->validate(
            $this->validationRules($object),
            [],
            $this->validationAttributes($object)
        );
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $object
     * @return array
     */
    protected function validationRules($object)
    {
        $fields = $this->collapseFieldsFromPanels($object);

        if ($object->exists && ($id = $object->getKey())) {
            return $fields
                ->pluck('updateRules', 'name')->map(function ($rule) use ($id) {
                    return str_replace('{id}', $id, $rule);
                })
                ->all();
        }

        return $fields->pluck('creationRules', 'name')->all();
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $object
     * @return array
     */
    protected function validationAttributes($object)
    {
        return $this
            ->collapseFieldsFromPanels($object)
            ->pluck('title', 'name')
            ->all();
    }

    /**
     * Resolves field.
     *
     * @param  string  $className
     * @param  string  $name
     * @return mixed
     */
    public function resolveField($className, $name)
    {
        foreach ($this->getFieldsOnce() as $field) {
            if (! $field instanceof $className) {
                continue;
            }

            if (($field instanceof BelongsTo || $field instanceof HasMany) && $field->relatedResource->uriKey() === $name) {
                return $field;
            }

            if ($field->name === $name) {
                return $field;
            }
        }

        throw new \RuntimeException(
            sprintf('Not found field %s[%s] in resource %s.', class_basename($className), $name, static::class)
        );
    }
}
