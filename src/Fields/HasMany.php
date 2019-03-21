<?php

namespace KiryaDev\Admin\Fields;


use KiryaDev\Admin\Resource\Paginator;
use KiryaDev\Admin\Fields\ActionsField;
use KiryaDev\Admin\Filters\FilterProvider;

class HasMany extends Panel
{
    protected $perPage = 5;

    public $addAction;

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

        /** @var \Illuminate\Database\Eloquent\Relations\HasMany $relation */
        $relation = $object->{$this->name}();

        $filterProvider = new FilterProvider($relation, $this->relatedResource, $this->name.'_');

        $paginator = new Paginator($relation, $this->perPage, $this->name.'_', $filterProvider->query());

        return view($this->resolveFormView(), compact(
            'resource',
            'title',
            'fields',
            'object',
            'filterProvider',
            'paginator'
        ))
            ->with($this->with($resource, $object));
    }

    /**
     * Set action to add new related resource.
     *
     * @param  \KiryaDev\Admin\Actions\Actionable $action
     * @return $this
     */
    public function addAction($action)
    {
        $this->addAction = $action;

        return $this;
    }

    /**
     * Default add action above list of related objects.
     *
     * @param  \KiryaDev\Admin\Resource\Resource    $resource
     * @param  \Illuminate\Database\Eloquent\Model  $object
     * @return array
     */
    protected function defaultAddAction($resource, $object)
    {
        $ablity = 'add'.class_basename($this->relatedResource->model);

        return $resource
            ->makeActionLink('addRelated', $ablity, $this->relatedResource->actionLabel('Add'))
            ->param('id', $object->getKey())
            ->param('related_resource', $this->relatedResource->uriKey());
    }

    /**
     * @param  \KiryaDev\Admin\Resource\Resource    $resource
     * @param  \Illuminate\Database\Eloquent\Model  $object
     * @return array
     */
    protected function with($resource, $object)
    {
        $addAction = $this->addAction ?? $this->defaultAddAction($resource, $object);

        return compact('addAction');
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
            ->add(
                ActionsField::with($this->relatedResource->getIndexActions())
            )
        ;
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