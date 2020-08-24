<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;

use Illuminate\Database\Eloquent\Model;

class Image extends File
{
    public string $accept = 'image/*';

    public string $prefix = 'storage/images';

    protected function boot(): void
    {
        parent::boot();

        $this->displayUsing(static function (Model $object, $value) {
            return "<img src='{$value}' style='max-width: 100px'></img>";
        });
    }
}
