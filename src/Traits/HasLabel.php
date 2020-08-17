<?php declare(strict_types=1);

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

    /**
     * Makes plural label.
     *
     * @return string
     */
    public static function pluralLabel()
    {
        return Str::plural(static::label());
    }
}
