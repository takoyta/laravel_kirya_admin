<?php

namespace KiryaDev\Admin\Fields;


class HasOne extends BelongsTo
{
    protected function boot()
    {
        parent::boot();

        $this->exceptOnForms(); // Has not editable field as HasMany
    }
}