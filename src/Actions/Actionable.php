<?php

namespace KiryaDev\Admin\Actions;


use KiryaDev\Admin\Traits\HasUriKey;

abstract class Actionable
{
    use HasUriKey;

    /**
     * @param  $resource  \KiryaDev\Admin\Resource\Resource
     * @param  $route  string
     * @return \KiryaDev\Admin\Resource\ActionLink
     */
    public abstract function link($resource, $route);
}
