<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;


class BelongsToMany extends HasMany
{
    /**
     * @param  \KiryaDev\Admin\Resource\Resource  $resource
     * @param  \Illuminate\Database\Eloquent\Model  $object
     * @return object
     */
    protected function actions($resource, $object)
    {
        $abilitySuffix = $this->relatedResource->modelName();
        $attachTitle = $this->relatedResource->actionLabel('Attach');

        $actions = $this->relatedResource
            ->getActionLinksForHandleMany($abilitySuffix, ['resource' => $this->relatedResource->uriKey(), 'from' => $resource->uriKey(), 'relation' => $this->name])
            ->add(
                $resource
                    ->makeActionLink('attachRelated', 'attach' . $abilitySuffix, $attachTitle)
                    ->param('related_resource', $this->relatedResource->uriKey())
                    ->addClass('js-attach-related')
            );

        return $this->wrapActions($actions, $object);
    }

    /**
     * @param  \KiryaDev\Admin\Resource\Resource  $resource
     * @param  \Illuminate\Database\Eloquent\Model  $object
     * @return \Illuminate\Support\Collection
     */
    protected function fields($resource, $object)
    {
        $detachTitle = $this->relatedResource->actionLabel('Detach');

        $ability = 'detach'.$this->relatedResource->modelName();

        return $this
            ->relatedResource
            ->getIndexFields()
            ->add($this->relatedResource
                ->getIndexActionsField()
                ->add($resource
                    ->makeActionLink('detachRelated', $ability, $detachTitle)
                    ->objectKey('related_id')
                    ->param('id', $object->getKey())
                    ->param('related_resource', $this->relatedResource->uriKey())
                    ->displayAsLink()
                    ->icon('thumbtack')
                )
            );
    }
}