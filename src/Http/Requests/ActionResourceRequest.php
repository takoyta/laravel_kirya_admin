<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Requests;

use KiryaDev\Admin\Actions\Actionable;
use KiryaDev\Admin\AdminCore;
use KiryaDev\Admin\Resource\AbstractResource;

/**
 * @property-read string $action
 * @property-read string $ids
 * @property-read string $from
 * @property-read string $relation
 */
class ActionResourceRequest extends DetailResourceRequest
{
    public function authorize(): bool
    {
        $ability = $this->resolveAction()->ability($this->from ? $this->resource()->modelName() : '');

        return $this
            ->resource()
            ->authorizedTo($ability, $this->forOne() ? $this->object() : null);
    }

    public function forOne(): bool
    {
        return null !== $this->id && null === $this->from;
    }

    public function resolveAction(): Actionable
    {
        $class = AdminCore::resolveActionClassName($this->action);

        return new $class;
    }

    public function resource(): AbstractResource
    {
        if ($this->from) {
            return AdminCore::resourceByKey($this->from);
        }

        return parent::resource();
    }
}
