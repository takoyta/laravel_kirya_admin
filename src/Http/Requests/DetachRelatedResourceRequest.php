<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Requests;

class DetachRelatedResourceRequest extends AttachRelatedResourceRequest
{
    public function authorizeAbilityPrefix(): string
    {
        return 'detachAny';
    }
}
