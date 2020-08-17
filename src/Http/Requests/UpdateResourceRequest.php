<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Requests;

class UpdateResourceRequest extends DetailResourceRequest
{
    public function authorize(): bool
    {
        return $this->resource()->authorizedTo('update', $this->object());
    }
}
