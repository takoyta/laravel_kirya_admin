<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields\Validation;


use KiryaDev\Admin\Fields\DateTime;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Rule;

class DateTimeRule implements Rule
{
    private $field;


    public function __construct(DateTime $field)
    {
        $this->field = $field;
    }

    public function passes($attr, $value)
    {
        return ! Validator::make(
            [$attr => $value],
            [$attr => 'nullable|date_format:'.$this->field->format]
        )->fails();
    }

    public function message()
    {
        return __('validation.date_format', ['format' => $this->field->format]);
    }
}