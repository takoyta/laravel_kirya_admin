<?php

namespace KiryaDev\Admin\Fields;


use KiryaDev\Admin\Core;

class BelongsTo extends FieldElement
{
    /**
     * @var \KiryaDev\Admin\Resource\Resource
     */
    public $relatedResource;


    protected function __construct($title, $name, $resource)
    {
        parent::__construct($title, $name);

        $this->relatedResource = $resource::instance();
    }

    protected function boot()
    {
        $this->displayUsing(function ($relatedObject) {
            $title = $this->relatedResource->title($relatedObject);

            return $this
                ->relatedResource
                ->makeActionLink('detail', 'view', $title)
                ->displayAsLink()
                ->display($relatedObject);
        });

        $this->fillUsing(function ($object, $value) {
            // fixme: check value is available id

            /** @var \Illuminate\Database\Eloquent\Relations\BelongsTo $relation */
            $relation = $object->{$this->name}();

            $relation->associate($value);
        });
    }

    public function formInputView($object)
    {
        return parent::formInputView($object)->with('ajax_search_url', $this->relatedResource->makeUrl('api.getObjects'));
    }

    public function resolve($object)
    {
        return optional($object->{$this->name})->getKey();
    }

    public function getAllowClear()
    {
        return ! $this->disabled && false === array_search('required', $this->creationRules); // fixme: can also updateRules
    }
}
