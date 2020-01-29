<?php

namespace KiryaDev\Admin\Actions;


use KiryaDev\Admin\Core;
use KiryaDev\Admin\Traits;
use Illuminate\Support\Str;
use KiryaDev\Admin\Resource\ActionLink;

abstract class Actionable
{
    use Traits\HasLabel, Traits\HasUriKey;

    public $requireConfirmation = false;


    /**
     * @param  $link  ActionLink
     * @return ActionLink
     */
    public function configureLink($link)
    {
        return $link;
    }

    public function ability($suffix = '')
    {
        return Str::camel(class_basename(static::class)) . $suffix . 'Action';
    }

    public function successResponse($message)
    {
        return Core::redirectToPrevious()->with('success', $message);
    }

    public function errorResponse($message)
    {
        return Core::redirectToPrevious()->with('error', $message);
    }
}
