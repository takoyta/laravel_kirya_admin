<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;

use Illuminate\Database\Eloquent\Model;

class Password extends FieldElement
{
    protected function boot(): void
    {
        $this->hideFromIndex()->hideFromDetail();

        $this->resolveUsing(static function () {
            return null;
        });

        $this->fillUsing(function (Model $object, $value) {
            if ($value) {
                $object->{$this->name} = bcrypt($value);
            }
        });
    }
}
