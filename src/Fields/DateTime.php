<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DateTime extends FieldElement
{
    public string $format = 'Y-m-d H:i'; // using by rule

    public bool $timepicker = true;

    protected function boot(): void
    {
        $this->rules(new Validation\DateTimeRule($this));
    }

    public function format(string $format)
    {
        $this->format = $format;

        return $this;
    }

    public function fill(Model $object, $value): void
    {
        parent::fill($object, $value ? Carbon::createFromFormat($this->format, $value) : null);
    }
}
