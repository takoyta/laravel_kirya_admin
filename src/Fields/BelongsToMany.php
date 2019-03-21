<?php

namespace KiryaDev\Admin\Fields;


class BelongsToMany extends HasMany
{
    /**
     * @param  \KiryaDev\Admin\Resource\Resource  $resource
     * @param  \Illuminate\Database\Eloquent\Model  $object
     * @return array
     */
    protected function with($resource, $object)
    {
        $ajaxUrl = null;
        $relatedResource = $this->relatedResource;

        $ability = 'attach'.class_basename($this->relatedResource->model);

        if ($resource->authorizedTo($ability, $object)) {
            $ajaxUrl = $resource
                ->makeUrl('attachRelated', [
                    'id' => $object->getKey(),
                    'related_resource' => $this->relatedResource->uriKey()
                ]);
        }

        return compact('ajaxUrl', 'relatedResource');
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
            ->add(
                ActionsField::with($this->relatedResource->getIndexActions())
                    ->add(
                        $resource
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