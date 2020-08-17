<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Requests;


use KiryaDev\Admin\Core;
use KiryaDev\Admin\Fields\BelongsToMany;

/**
 * @property-read  string  id
 * @property-read  string  related_resource
 * @property-read  string  related_id
 */
class AttachRelatedResourceRequest extends ResourceRequest
{
    public function resource()
    {
        return Core::resourceByKey($this->related_resource);
    }

    public function parentResource()
    {
        return Core::resourceByKey($this->resource);
    }

    public function parentObject()
    {
        return $this->parentResource()->findModel($this->id);
    }

    /**
     * @return \KiryaDev\Admin\Fields\BelongsToMany
     */
    public function resolveParentField()
    {
        return $this
            ->parentResource()
            ->resolveField(BelongsToMany::class, $this->related_resource);
    }
}