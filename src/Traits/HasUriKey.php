<?php declare(strict_types=1);

namespace KiryaDev\Admin\Traits;


use Illuminate\Support\Str;

trait HasUriKey
{
    /**
     * Makes uri key for resource or action.
     *
     * @return string
     */
    public static function uriKey()
    {
        return Str::kebab(class_basename(static::class));
    }
}