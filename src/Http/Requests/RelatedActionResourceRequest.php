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
class RelatedActionResourceRequest extends ActionResourceRequest
{
    public function authorize()
    {
        $ability = Str::camel($this->action).'Action';

        return $this
            ->relatedResource()
            ->authorizedTo($ability, $this->forMany() ? null : $this->object());
    }

    public function relatedResource()
    {
        return $this->resolveHasManyField()->relatedResource;
    }

    /**
     * @return HasMany
     */
    public function resolveHasManyField()
    {
        return $this->resource()->resolveField(HasMany::class, $this->field_name);
    }
}
