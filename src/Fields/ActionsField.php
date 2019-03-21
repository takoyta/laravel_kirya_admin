<?php

namespace KiryaDev\Admin\Fields;


use Illuminate\Support\Arr;

class ActionsField
{
    public $title = '';

    public $sortable = false;

    protected $actions = [];


    private function __construct() {}

    public static function with($actions)
    {
        return (new static)->add($actions);
    }

    public function add($actions)
    {
        $this->actions = array_merge($this->actions, Arr::wrap($actions));

        return $this;
    }

    public function display($object)
    {
        return collect($this->actions)->map->display($object)->implode(' ');
    }
}