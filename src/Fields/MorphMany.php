<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use KiryaDev\Admin\Resource\AbstractResource;

class MorphMany extends HasMany
{
    /**
     * Name of reverse relation field in Resource.
     */
    public string $reverseName;

    public function __construct($title, $related, $resource, $name)
    {
        parent::__construct($title, $related, $resource);

        $this->reverseName = $name;
    }

    protected function fields(AbstractResource $resource, Model $object): Collection
    {
        return $this
            ->relatedResource
            ->getIndexFields()
            ->filter(function ($field) {
                return ! ($field instanceof MorphTo && $field->name === $this->reverseName); //exclude reverse relation
            })
            ->add($this->relatedResource->getIndexActionsField());
    }
}
