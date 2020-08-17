<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Requests;

class DeleteResourceRequest extends DetailResourceRequest
{
    public function authorize(): bool
    {
        return $this->resource()->authorizedTo('delete', $this->object());
    }
}
