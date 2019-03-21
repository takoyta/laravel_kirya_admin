<?php
namespace KiryaDev\Admin\Resource;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

use KiryaDev\Admin\Core;
use KiryaDev\Admin\Fields\Panel;
use KiryaDev\Admin\Fields\Element;
use KiryaDev\Admin\Fields\HasMany;
use KiryaDev\Admin\Fields\MorphTo;
use KiryaDev\Admin\Fields\MorphMany;
use KiryaDev\Admin\Fields\BelongsTo;
use KiryaDev\Admin\Traits\HasUriKey;
use KiryaDev\Admin\Fields\FieldElement;
use KiryaDev\Admin\Fields\BelongsToMany;

abstract class Resource
{
    use Authorizable, HasUriKey, HasConfirmationMessages;

    public $model;

    public $group = 'Other';

    public $title = 'id';

    public $search;

    public $perPage = 15;

    public $orderInSidebar = 100; // If false - resource hiding from sidebar


    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        return $this->model::query();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function indexQuery()
    {
        return $this->query();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function searchQuery()
    {
        return $this->query();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function newModel()
    {
        $className = $this->model;

        return new $className;
    }

    /**
     * @param  string  $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findModel($id)
    {
        return $this->query()->findOrFail($id);
    }

    /**
     * Call getFieldsOnce for get once instances.
     *
     * @return array
     */
    abstract public function fields();

    /**
     * @return \KiryaDev\Admin\Actions\Actionable[]
     */
    public function actions()
    {
        return [];
    }

    /**
     * @return \KiryaDev\Admin\Filters\Filterable[]
     */
    public function filters()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getFieldsOnce()
    {
        static $fields;

        return $fields ?? ($fields = $this->fields());
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

        array_unshift($panels, Panel::make('Details', $detailFields));

        return $panels;
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
     * @return \KiryaDev\Admin\Resource\ActionLink[]
     */
    public function getIndexActions()
    {
        return [
            $this->makeActionLink('detail', 'view')->icon('eye')->displayAsLink()
        ];
    }

    /**
     * @return \KiryaDev\Admin\Resource\ActionLink[]
     */
    public function getDetailActions()
    {
        return [
            $this->makeActionLink('edit', 'update')->icon('edit'), // fixme: change altTitle from Update -> Edit
            $this->makeActionLink('delete')->icon('trash'),
        ];
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

    /**
     * Get title of object via title field.
     *
     * @param  Model|string  $object
     * @return string
     */
    public function title($object)
    {
        if (! $object) return null;

        if (! $object instanceof Model) $object = $this->findModel($object);

        return $object->{$this->title};
    }

    /**
     * Convert resource classname to title case.
     *
     * @return string
     */
    public static function label()
    {
        return Str::title(Str::snake(class_basename(static::class), ' '));
    }

    /**
     * Generate title for action.
     *
     * @return string
     */
    public static function actionLabel($action)
    {
        return __(':action ' . static::label(), ['action' => __($action)]);
    }


    /**
     * Retrive instance of current resource.
     *
     * @return self
     */
    public static function instance()
    {
        return Core::resourceByKey(static::uriKey());
    }

    /**
     * Generate url to action.
     *
     * @return string
     */
    public static function makeUrl($action, $params = [])
    {
        $params['resource'] = static::uriKey();

        return route('admin.' . $action, $params, true);
    }

    /**
     * @param  string  $action
     * @param  string  $ability
     * @param  string  $title
     * @return \KiryaDev\Admin\Resource\ActionLink
     */
    public function makeActionLink($action, $ability = null, $title = null)
    {
        return new ActionLink($this, $action, $ability, $title);
    }
}