<?php declare(strict_types=1);

namespace KiryaDev\Admin\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use KiryaDev\Admin\Fields\BelongsTo;
use KiryaDev\Admin\Fields\Element;
use KiryaDev\Admin\Fields\FieldElement;
use KiryaDev\Admin\Fields\HasMany;
use KiryaDev\Admin\Fields\Panel;
use KiryaDev\Admin\Fields\Panelable;
use KiryaDev\Admin\Resource\AbstractResource;

trait HasFields
{
    private static array $fieldsCache = [];

    /**
     * Call getFieldsOnce for get once instances.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            // override there your fields and panels
        ];
    }

    private function getFieldsOnce(): array
    {
        if (! isset(static::$fieldsCache[static::class])) {
            static::$fieldsCache[static::class] = $this->validateFields();
        }

        return static::$fieldsCache[static::class];
    }

    private function validateFields(): array
    {
        $fields = array_filter($this->fields(), 'is_object');

        foreach ($fields as $field) {
            if ($field instanceof Element) {
                if ($this instanceof AbstractResource) {
                    $field->setResource($this);
                }
                continue;
            }

            throw new \RuntimeException(sprintf(
                'Invalid field type: %s. Must extends %s',
                is_object($field) ? get_class($field) : gettype($field),
                Element::class,
            ));
        }

        return $fields;
    }

    public function getIndexFields(): Collection
    {
        return collect($this->getFieldsOnce())
            ->whereInstanceOf(FieldElement::class)
            ->where('showOnIndex');
    }

    /**
     * @param  \Closure  $filter
     * @return \KiryaDev\Admin\Fields\Panel[]
     */
    protected function getPanelsWithFilter(\Closure $filter)
    {
        $panels = [];
        $detailFields = [];

        foreach ($this->getFieldsOnce() as $field) {
            if (! $filter($field)) {
                continue;
            }

            if ($field instanceof FieldElement) {
                $detailFields[] = $field;
                continue;
            }

            if ($field instanceof Panelable) {
                if ($field instanceof Panel) {
                    $field->fields = array_filter($field->fields, $filter);
                    if (count($field->fields) === 0) {
                        continue;
                    }
                }

                if ($field instanceof HasMany) {
                    if (! $field->relatedResource->authorizedToViewAny()) {
                        continue;
                    }
                }

                $panels[] = $field;
                continue;
            }

            throw new \InvalidArgumentException('Field must extends FieldElement or implemenets Panelable');
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
        return $this->getPanelsWithFilter(fn (Element $element) => $element->showOnDetail);
    }

    /**
     * Return Panels.
     * Also handled disabled fields.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $object
     * @return \KiryaDev\Admin\Fields\Panel[]
     */
    public function getFormPanels(Model $object)
    {
        return $this->getPanelsWithFilter(static function (Element $element) use ($object) {
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
     * @param Model $object
     * @return Collection|FieldElement[]
     */
    public function getFormFields(Model $object): Collection
    {
        return collect($this->getFormPanels($object))
            ->pluck('fields')
            ->collapse()
            ->whereStrict('disabled', false);
    }

    public function validateOnFields(Request $request, Model $object): array
    {
        return $request->validate(
            $this->validationRules($object),
            [],
            $this->validationAttributes($object)
        );
    }

    protected function validationRules(Model $object): array
    {
        $fields = $this->getFormFields($object);

        if ($object->exists && ($id = $object->getKey())) {
            return $fields
                ->pluck('updateRules', 'name')
                // Fill here {id} template in rules
                ->map(static function(array $rules) use ($id) {
                    foreach ($rules as &$rule) {
                        if (is_string($rule)) {
                            $rule = str_replace('{id}', (string) $id, $rule);
                        }
                    }

                    return $rules;
                })
                ->all();
        }

        return $fields->pluck('creationRules', 'name')->all();
    }

    protected function validationAttributes(Model $object): array
    {
        return $this
            ->getFormFields($object)
            ->pluck('title', 'name')
            ->all();
    }

    public function resolveField(string $className, string $name)
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
