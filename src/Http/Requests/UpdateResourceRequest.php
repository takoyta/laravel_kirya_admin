<?php

namespace KiryaDev\Admin\Http\Requests;


class UpdateResourceRequest extends DetailResourceRequest
{
    public function authorize()
    {
        return $this->resource()->authorizedTo('update', $this->object());
    }
}