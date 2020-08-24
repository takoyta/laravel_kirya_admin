<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Select extends FieldElement
{
    protected iterable $options = [];

    protected bool $addNullOption = false;

    protected function boot(): void
    {
        $this->displayUsing(function (Model $object, $value) {
            return $this->options[$value] ?? $value;
        });
    }

    public function options($options)
    {
        $this->options = $options;

        return $this;
    }

    public function rangeOptions($from, $to, $step = 1)
    {
        return $this->options(array_combine($a = range($from, $to, $step), $a));
    }

    public function addNullOption()
    {
        $this->addNullOption = true;

        return $this;
    }

    public function formInputView(Model $object)
    {
        $options = \is_callable($fn = $this->options) ? $fn($object) : $this->options;

        if ($options instanceof Collection) {
            $options = $options->all();
        }

        if ($this->addNullOption) {
            $options = [null => '—'] + $options;
        }

        return parent::formInputView($object)->with(compact('options'));
    }
}
