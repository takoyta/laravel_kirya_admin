<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;

class ID extends FieldElement
{
    protected function __construct($title = 'ID', $name = 'id')
    {
        parent::__construct($title, $name);
    }

    protected function boot(): void
    {
        $this->exceptOnForms()->sortable();
    }
}
