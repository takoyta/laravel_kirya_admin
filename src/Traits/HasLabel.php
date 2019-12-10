<?php
namespace KiryaDev\Admin\Traits;


use Illuminate\Support\Str;

trait HasLabel
{
    /**
     * Makes label.
     *
     * @return string
     */
    public static function label()
    {
        return Str::title(Str::snake(class_basename(static::class), ' '));
    }
}
