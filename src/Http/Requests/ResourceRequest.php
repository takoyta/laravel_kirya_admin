<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Requests;

use KiryaDev\Admin\AdminCore;
use Illuminate\Foundation\Http\FormRequest;
use KiryaDev\Admin\Resource\AbstractResource;

/**
 * @property string resource
 */
abstract class ResourceRequest extends FormRequest
{
    public function resource(): AbstractResource
    {
        return AdminCore::resourceByKey($this->resource);
    }

    public function authorize(): bool
    {
        return $this->resource()->authorizedToViewAny();
    }

    public function rules(): array
    {
        return [];
    }
}
