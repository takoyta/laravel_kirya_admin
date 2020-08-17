<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Requests;


use KiryaDev\Admin\Core;

/**
 * @property-read  string  $action
 * @property-read  string  $ids
 * @property-read  string  $from
 * @property-read  string  $relation
 */
class ActionResourceRequest extends DetailResourceRequest
{
    public function authorize()
    {
        $ability = $this->resolveAction()->ability($this->from ? $this->resource()->modelName() : '');

        return $this
            ->resource()
            ->authorizedTo($ability, $this->forOne() ? $this->object() : null);
    }

    public function forOne()
    {
        return null !== $this->id && null === $this->from;
    }

    /**
     * @return \KiryaDev\Admin\Actions\Actionable
     */
    public function resolveAction()
    {
        $class = Core::resolveActionClassName($this->action);

        return new $class;
    }

    public function resource()
    {
        if ($this->from)
            return Core::resourceByKey($this->from);

        return parent::resource();
    }
}
