<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;

class HasOne extends BelongsTo
{
    protected function boot(): void
    {
        $this->exceptOnForms(); // Has not editable field as HasMany
    }
}
