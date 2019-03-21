<?php

namespace KiryaDev\Admin\Http\Requests;


/**
 * @property-read  int  $id
 */
class DetailResourceRequest extends ResourceRequest
{
    public function authorize()
    {
        return $this->resource()->authorizedTo('view', $this->object());
    }

    public function object()
    {
        return $this->resource()->findModel($this->id);
    }
}