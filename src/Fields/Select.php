<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;


class Select extends FieldElement
{
    protected $options = [];

    protected $addNullOption = false;


    protected function boot()
    {
        $this->displayUsing(function ($value) {
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

    public function formInputView($object)
    {
        $options = \is_callable($fn = $this->options) ? $fn($object) : $this->options;

        if ($options instanceof \Illuminate\Support\Collection) {
            $options = $options->all();
        }

        if ($this->addNullOption) {
            $options = [null => '—'] + $options;
        }

        return parent::formInputView($object)->with(compact('options'));
    }
}
