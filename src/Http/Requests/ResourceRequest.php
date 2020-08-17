<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Requests;

use KiryaDev\Admin\Core;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string resource
 */
abstract class ResourceRequest extends FormRequest
{
    public function resource()
    {
        return Core::resourceByKey($this->resource);
    }

    public function authorize()
    {
        return $this->resource()->authorizedToViewAny();
    }

    public function rules()
    {
        return [];
    }
}