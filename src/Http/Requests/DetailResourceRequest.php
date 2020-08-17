<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Requests;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 */
class DetailResourceRequest extends ResourceRequest
{
    public function authorize(): bool
    {
        return $this->resource()->authorizedTo('view', $this->object());
    }

    public function object(): Model
    {
        return $this->resource()->findModel($this->id);
    }
}
