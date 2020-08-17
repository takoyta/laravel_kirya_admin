<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;

use KiryaDev\Admin\Resource\AbstractResource;

class BelongsTo extends FieldElement
{
    public AbstractResource $relatedResource;

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
        return parent::formInputView($object)
            ->with('ajaxSearchUrl', $this->relatedResource->makeUrl('api.getObjects'))
            ->with('filterProviderFields', $this->relatedResource->newFilterProvider()->fields)
            ;
    }

    public function resolve($object)
    {
        return optional($object->{$this->name})->getKey();
    }

    public function getAllowClear(): bool
    {
        return ! $this->disabled && !in_array('required', $this->creationRules, true); // fixme: can also updateRules
    }
}
