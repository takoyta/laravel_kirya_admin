<?php declare(strict_types=1);

namespace KiryaDev\Admin\Filters;

use Illuminate\Database\Eloquent\Builder;
use KiryaDev\Admin\Fields\FieldElement;

interface Filterable
{
    /**
     * @param Builder $query
     * @param string $value
     */
    public function apply($query, $value);

    /**
     * @return FieldElement
     */
    public function field();
}
