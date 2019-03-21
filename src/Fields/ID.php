<?php

namespace KiryaDev\Admin\Fields;


class ID extends FieldElement
{
    protected function __construct($title = 'ID', $name = 'id')
    {
        parent::__construct($title, $name);
    }

    protected function boot()
    {
        $this->exceptOnForms()->sortable();
    }
}