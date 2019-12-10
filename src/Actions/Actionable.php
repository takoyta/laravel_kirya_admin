<?php

namespace KiryaDev\Admin\Actions;


use KiryaDev\Admin\Traits;
use Illuminate\Support\Str;

abstract class Actionable
{
    use Traits\HasLabel, Traits\HasUriKey;

    public $requireConfirmation = false;


    /**
     * @param  $resource  \KiryaDev\Admin\Resource\Resource
     * @param  $route  string
     * @return \KiryaDev\Admin\Resource\ActionLink
     */
    public function link($resource, $route)
    {
        $basename = class_basename(static::class);

        return $resource->makeActionLink($route, Str::camel($basename), __(static::label()));
    }
}
