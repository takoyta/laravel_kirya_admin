<?php

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
        $attachAbility = 'attach'.class_basename($this->relatedResource->model);
        $attachTitle = $this->relatedResource->actionLabel('Attach');

        $actions = $this->relatedResource
            ->getActionLinksForHandleMany('relatedAction', ['field_type' => 'many-many', 'field_name' => $this->name])
            ->add(
                $resource
                    ->makeActionLink('attachRelated', $attachAbility, $attachTitle)
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

        $ability = 'detach'.class_basename($this->relatedResource->model);

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