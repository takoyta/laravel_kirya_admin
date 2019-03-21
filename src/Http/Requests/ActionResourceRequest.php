<?php

namespace KiryaDev\Admin\Http\Requests;


use KiryaDev\Admin\Core;
use Illuminate\Support\Str;

/**
 * @property-read  string  $action
 */
class ActionResourceRequest extends DetailResourceRequest
{
    public function authorize()
    {
        $ability = Str::camel($this->action).'Action';

        return $this
            ->resource()
            ->authorizedTo($ability, 'all' === $this->id ? null : $this->object());
    }

    /**
     * @return \KiryaDev\Admin\Actions\Actionable
     */
    public function resolveAction()
    {
        $class = Core::resolveActionClassName($this->action);

        return new $class($this->resource());
    }
}