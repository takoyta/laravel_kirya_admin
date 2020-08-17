<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Requests;

class CreateResourceRequest extends ResourceRequest
{
    public function authorize(): bool
    {
        return $this->resource()->authorizedTo('create');
    }
}
