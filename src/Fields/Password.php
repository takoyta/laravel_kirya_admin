<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;


class Password extends FieldElement
{
    protected function boot()
    {
        $this->hideFromIndex()->hideFromDetail();

        $this->resolveUsing(function () {
            return null;
        });

        $this->fillUsing(function ($object, $value) {
            if ($value) {
                $object->{$this->name} = bcrypt($value);
            }
        });
    }
}
