<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Requests;

use KiryaDev\Admin\AdminCore;
use KiryaDev\Admin\Fields\BelongsToMany;
use KiryaDev\Admin\Resource\AbstractResource;

/**
 * @property-read string $id
 * @property-read string $related_resource
 * @property-read string $related_id
 */
class AttachRelatedResourceRequest extends ResourceRequest
{
    public function resource(): AbstractResource
    {
        return AdminCore::resourceByKey($this->related_resource);
    }

    public function parentResource(): AbstractResource
    {
        return AdminCore::resourceByKey($this->resource);
    }

    public function parentObject()
    {
        return $this->parentResource()->findModel($this->id);
    }

    public function resolveParentField(): BelongsToMany
    {
        return $this
            ->parentResource()
            ->resolveField(BelongsToMany::class, $this->related_resource);
    }
}
