<?php

namespace KiryaDev\Admin\Http\Requests;


use KiryaDev\Admin\Core;
use Illuminate\Support\Str;
use KiryaDev\Admin\Fields\HasMany;

/**
 * @property-read  string  $action
 * @property-read  string  $field_type
 * @property-read  string  $field_name
 */
class ActionResourceRequest extends DetailResourceRequest
{
    public function authorize()
    {
        $ability = Str::camel($this->action).'Action';

        return $this
            ->resource()
            ->authorizedTo($ability, $this->forMany() ? null : $this->object());
    }

    public function forMany()
    {
        return 'all' === $this->id;
    }

    /**
     * @return \KiryaDev\Admin\Actions\Actionable
     */
    public function resolveAction()
    {
        $class = Core::resolveActionClassName($this->action);

        return new $class;
    }
}