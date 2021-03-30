<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;

use Illuminate\Database\Eloquent\Model;
use KiryaDev\Admin\Resource\AbstractResource;

class BelongsTo extends FieldElement
{
    public AbstractResource $relatedResource;

    protected function __construct($title, $name, $resource)
    {
        parent::__construct($title, $name);

        $this->relatedResource = $resource::instance();
    }

    public function displayValue(Model $object)
    {
        $relatedObject = $object->{$this->name};
        if (null === $relatedObject) {
            return null;
        }

        $title = $this->relatedResource->title($relatedObject);

        $value = $this
            ->relatedResource
            ->makeActionLink('detail', 'view', $title)
            ->displayAsLink()
            ->display($relatedObject);

        if ($this->displayCallback) {
            $value = ($this->displayCallback)($relatedObject, $value);
        }

        return $value;
    }

    public function displayForm(Model $object)
    {
        return parent::displayForm($object)
            ->with('ajaxSearchUrl', $this->relatedResource->makeUrl('api.getObjects'))
            ->with('filterProvider', $this->relatedResource->newFilterProvider());
    }

    public function resolve(Model $object)
    {
        return optional($object->{$this->name})->getKey();
    }

    public function fill(Model $object, $value): void
    {
        /** @var \Illuminate\Database\Eloquent\Relations\BelongsTo $relation */
        $relation = $object->{$this->name}();

        // fixme: check value is available id
        $relation->associate($value);
    }

    public function getAllowClear(): bool
    {
        return !$this->disabled && !in_array('required', $this->creationRules, true); // fixme: can also updateRules
    }
}
