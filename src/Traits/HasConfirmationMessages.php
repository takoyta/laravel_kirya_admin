<?php
namespace KiryaDev\Admin\Traits;


use Illuminate\Support\Arr;

trait HasConfirmationMessages
{
    public $confirmationMessages = [];


    public function getConfirmationMessage($action)
    {
        return Arr::get($this->confirmationMessages, $action);
    }
}
