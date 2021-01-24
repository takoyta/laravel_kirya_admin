<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Requests;

use KiryaDev\Admin\Fields;

class AttachRelatedResourceRequest extends AbstractRelatedResourceRequest
{
    public function authorizeAbilityPrefix(): string
    {
        return 'attachAny';
    }

    public function resolveParentField(): Fields\BelongsToMany
    {
        return $this
            ->parentResource()
            ->resolveField(Fields\BelongsToMany::class, $this->related_resource);
    }
}
