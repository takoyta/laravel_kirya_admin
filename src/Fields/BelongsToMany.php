<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use KiryaDev\Admin\Resource\ActionLink;

class BelongsToMany extends HasMany
{
    protected bool $addAction = true; // in real attach action

    protected function buildAddAction(): ActionLink
    {
        $ability = 'attachAny' . $this->relatedResource->modelName();
        $title = $this->relatedResource->actionLabel('Attach');

        return $this->resource
            ->makeActionLink('attachRelated', $ability, $title)
            ->param('related_resource', $this->relatedResource->uriKey());
    }

    protected function fields(Model $object): Collection
    {
        $detachTitle = $this->relatedResource->actionLabel('Detach');

        $ability = 'detach' . $this->relatedResource->modelName();

        return $this
            ->relatedResource
            ->getIndexFields()
            ->add($this->relatedResource
                ->getIndexActionsField()
                ->add($this->resource
                    // TODO: add parent object to check ability
                    ->makeActionLink('detachRelated', $ability, $detachTitle)
                    ->objectKey('related_id')
                    ->param('id', $object->getKey())
                    ->param('related_resource', $this->relatedResource->uriKey())
                    ->displayAsLink()
                    ->icon('thumbtack text-danger')
                )
            );
    }
}
