<?php

namespace KiryaDev\Admin\Fields;


class HasOne extends BelongsTo
{
    protected function boot()
    {
        parent::boot();

        $this->fillUsing(function ($object, $value) {
            // fixme: check value is available id

            /** @var \Illuminate\Database\Eloquent\Relations\HasOne $relation */
            $relation = $object->{$this->name}();

            $relation->associate($value);
        });
    }
}