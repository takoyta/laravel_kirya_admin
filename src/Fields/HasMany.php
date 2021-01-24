<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use KiryaDev\Admin\Filters\FilterProvider;
use KiryaDev\Admin\Resource\AbstractResource;
use KiryaDev\Admin\Resource\Paginator;

class HasMany extends Panel
{
    protected int $perPage = 5;
    public string $name;
    public AbstractResource $relatedResource;

    protected function __construct($title, $name, $resource)
    {
        parent::__construct($title, []);

        $this->name = $name;
        $this->relatedResource = $resource::instance();
    }

    public function display(AbstractResource $resource, Model $object)
    {
        $title = $this->title;

        $fields = $this->fields($resource, $object);

        // For isolate filter if more than one filter on page.
        $prefix = $this->name . '_';
        $filterProvider = $this->relatedResource->newFilterProvider($prefix);
        $paginator = new Paginator($this->getRelation($object, $filterProvider), $this->perPage, $prefix, $filterProvider->getValues());

        return view($this->resolveFormView(), compact(
            'resource',
            'title',
            'fields',
            'object',
            'filterProvider',
            'paginator'
        ))
            ->with('actions', $this->actions($resource, $object));
    }

    private function getRelation(Model $object, FilterProvider $filter)
    {
        $relation = $object->{$this->name}();
        $filter->apply($relation);

        return $relation;
    }

    protected function actions(AbstractResource $resource, Model $object): Collection
    {
        $abilitySuffix = $this->relatedResource->modelName();
        $addTitle = $this->relatedResource->actionLabel('Add');

        $actions = $this->relatedResource
            ->getActionLinksForHandleMany($abilitySuffix, ['resource' => $this->relatedResource->uriKey(), 'from' => $resource->uriKey(), 'relation' => $this->name])
            ->add(
                $resource
                    ->makeActionLink('addRelated', 'addAny' . $abilitySuffix, $addTitle)
                    ->param('related_resource', $this->relatedResource->uriKey())
            );

        return $this->wrapActions($actions, $object);
    }

    /**
     * Wrap action into anonymous class for displaying from template.
     *
     * @param Collection $actions
     * @param Model $object
     * @return Collection
     */
    protected function wrapActions(Collection $actions, Model $object)
    {
        return $actions->map(function ($action) use ($object) {
            return tap(new class {
                public $action;
                public $object;

                public function display()
                {
                    return $this->action->display($this->object);
                }
            }, static function ($wrapper) use ($action, $object) {
                $wrapper->action = $action;
                $wrapper->object = $object;
            });
        });
    }

    /**
     * Get fields for Panel.
     *  For HasMany requires exclude BelongsTo,
     *  For MorphMany requires exclude MorphTo,
     *  For BelongsToMany requires add actions "Attach" & "Detach".
     *
     * @param AbstractResource $resource
     * @param Model $object
     * @return Collection
     */
    protected function fields(AbstractResource $resource, Model $object): Collection
    {
        return $this
            ->relatedResource
            ->getIndexFields()
            ->filter(static function ($field) use ($resource) {
                return !($field instanceof BelongsTo && $field->relatedResource === $resource); //exclude reverse relation
            })
            ->add($this->relatedResource->getIndexActionsField());
    }

    public function displayForm(Model $object)
    {
        // fixme : no need display this of forms
        return null;
    }

    /**
     * @param int $count
     * @return static
     */
    public function perPage(int $count)
    {
        $this->perPage = $count;

        return $this;
    }
}
