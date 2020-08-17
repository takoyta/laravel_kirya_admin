<?php declare(strict_types=1);

namespace KiryaDev\Admin\Traits;

use Illuminate\Support\Arr;

trait HasConfirmationMessages
{
    public array $confirmationMessages = [];

    public function getConfirmationMessage($action)
    {
        return Arr::get($this->confirmationMessages, $action);
    }
}
