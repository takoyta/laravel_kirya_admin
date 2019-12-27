<?php

namespace KiryaDev\Admin\Fields;


use KiryaDev\Admin\Resource\Paginator;
use KiryaDev\Admin\Fields\ActionsField;
use KiryaDev\Admin\Filters\FilterProvider;

class HasMany extends Panel
{
    protected $perPage = 5;

    public $name;

    /**
     * @var \KiryaDev\Admin\Resource\Resource
     */
    public $relatedResource;


    protected function __construct($title, $name, $resource)
    {
        parent::__construct($title, []);

        $this->name = $name;

        $this->relatedResource = $resource::instance();
    }

    /**
     * @param  \KiryaDev\Admin\Resource\Resource    $resource
     * @param  \Illuminate\Database\Eloquent\Model  $object
     * @return string
     */
    public function display($resource, $object)
    {
        $title = $this->title;

        $fields = $this->fields($resource, $object);

        [$relation, $filterProvider] = $this->getRelationAndFilterProvider($object);

        $paginator = new Paginator($relation, $this->perPage, $this->name.'_', $filterProvider->query());

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

    public function getRelationAndFilterProvider($object)
    {
        return [$this->getRelation($object, $filter = $this->getFilterProvider()), $filter];
    }

    public function getRelation($object, FilterProvider $filter = null)
    {
        $filter = $filter ?? $this->getFilterProvider();

        return tap($object->{$this->name}(), function ($query) use ($filter) {
            $filter->apply($query);
        });
    }

    private function getFilterProvider()
    {
        return $this->relatedResource->newFilterProvider($this->name.'_');
    }

    /**
     * @param  \KiryaDev\Admin\Resource\Resource    $resource
     * @param  \Illuminate\Database\Eloquent\Model  $object
     * @return object
     */
    protected function actions($resource, $object)
    {
        $addAbility = 'add'.class_basename($this->relatedResource->model);
        $addTitle = $this->relatedResource->actionLabel('Add');

        $actions = $this->relatedResource
            ->getActionLinksForHandleMany('relatedAction', ['field_type' => 'many', 'field_name' => $this->name, 'resource' => $resource->uriKey()])
            ->add(
                $resource
                    ->makeActionLink('addRelated', $addAbility, $addTitle)
                    ->param('related_resource', $this->relatedResource->uriKey())
            );

        return $this->wrapActions($actions, $object);
    }

    /**
     * Wrap action into anonymous class for displaying from template.
     *
     * @param  array  $actions
     * @param  mixed  $object
     * @return \Illuminate\Support\Collection
     */
    protected function wrapActions($actions, $object)
    {
        return collect($actions)->map(function ($action) use ($object) {
            return tap(new class {
                public $action;
                public $object;
                public function display() {
                    return $this->action->display($this->object);
                }
            }, function ($wrapper) use ($action, $object) {
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
     * @param  \KiryaDev\Admin\Resource\Resource    $resource
     * @param  \Illuminate\Database\Eloquent\Model  $object
     * @return \Illuminate\Support\Collection
     */
    protected function fields($resource, $object)
    {
        return $this
            ->relatedResource
            ->getIndexFields()
            ->filter(function ($field) use ($resource) {
                return ! ($field instanceof BelongsTo && $field->relatedResource === $resource); //exclude reverse relation
            })
            ->add($this->relatedResource->getIndexActionsField());
    }

    public function displayForm($object, $resource)
    {
        // fixme : no need display this of forms
        return null;
    }

    public function perPage($count)
    {
        $this->perPage = $count;

        return $this;
    }
}