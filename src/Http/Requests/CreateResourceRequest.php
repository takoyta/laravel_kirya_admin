<?php

namespace KiryaDev\Admin\Http\Requests;


class CreateResourceRequest extends ResourceRequest
{
    public function authorize()
    {
        return $this->resource()->authorizedTo('create');
    }
}