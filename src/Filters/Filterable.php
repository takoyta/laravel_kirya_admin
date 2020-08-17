<?php declare(strict_types=1);

namespace KiryaDev\Admin\Filters;


interface Filterable
{
    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $value
     */
   public function apply($query, $value);

    /**
     * @return \KiryaDev\Admin\Fields\FieldElement
     */
   public function field();
}
