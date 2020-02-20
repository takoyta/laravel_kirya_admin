<?php

namespace KiryaDev\Admin\Fields;


class Panel extends Element
{
    public $fields;


    protected function __construct(string $title, $fields = [])
    {
        parent::__construct($title);

        $this->fields = $fields;
    }

    public function display($resource, $object)
    {
        $panel = $this;

        return view('admin::resource.detail-partials.panel', compact('panel', 'object'));
    }

    public function displayForm($object)
    {
        $panel = $this;

        return view($this->resolveFormView(), compact('panel', 'object'));
    }
}