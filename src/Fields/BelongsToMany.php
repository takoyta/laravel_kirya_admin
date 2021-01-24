<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use KiryaDev\Admin\Resource\AbstractResource;

class BelongsToMany extends HasMany
{
    protected function actions(AbstractResource $resource, Model $object): Collection
    {
        $abilitySuffix = $this->relatedResource->modelName();
        $attachTitle = $this->relatedResource->actionLabel('Attach');

        $actions = $this->relatedResource
            ->getActionLinksForHandleMany($abilitySuffix, ['resource' => $this->relatedResource->uriKey(), 'from' => $resource->uriKey(), 'relation' => $this->name])
            ->add(
                $resource
                    ->makeActionLink('attachRelated', 'attachAny' . $abilitySuffix, $attachTitle)
                    ->param('related_resource', $this->relatedResource->uriKey())
            );

        return $this->wrapActions($actions, $object);
    }

    protected function fields(AbstractResource $resource, Model $object): Collection
    {
        $detachTitle = $this->relatedResource->actionLabel('Detach');

        $ability = 'detach' . $this->relatedResource->modelName();

        return $this
            ->relatedResource
            ->getIndexFields()
            ->add($this->relatedResource
                ->getIndexActionsField()
                ->add($resource
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
