<?php

namespace KiryaDev\Admin\Fields;


use KiryaDev\Admin\Http\Requests\AddRelatedResourceRequest;

class MorphMany extends HasMany
{
    /**
     * Name of reverse relation field in Resource.
     *
     * @var string
     */
    public $reverseName;


    public function __construct($title, $related, $resource, $name)
    {
        parent::__construct($title, $related, $resource);

        $this->reverseName = $name;
    }

    /**
     * @param  \KiryaDev\Admin\Resource\Resource    $resource
     * @param  \Illuminate\Database\Eloquent\Model  $object
     * @return \Illuminate\Support\Collection
     */
    protected function fields($resource, $object)
    {
        return $this
            ->relatedResource
            ->getIndexFields()
            ->filter(function ($field) {
                return ! ($field instanceof MorphTo && $field->name === $this->reverseName); //exclude reverse relation
            })
            ->add(
                ActionsField::with($this->relatedResource->getIndexActions())
            );
    }
}
