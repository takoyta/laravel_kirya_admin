<?php declare(strict_types=1);

namespace KiryaDev\Admin\Traits;

use Illuminate\Support\Str;

trait HasLabel
{
    /**
     * Makes label.
     */
    public static function label(): string
    {
        return Str::title(Str::snake(class_basename(static::class), ' '));
    }

    /**
     * Makes plural label.
     */
    public static function pluralLabel(): string
    {
        return Str::plural(static::label());
    }
}
