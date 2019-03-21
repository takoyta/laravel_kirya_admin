<?php

namespace KiryaDev\Admin\Fields;


use Carbon\Carbon;

class DateTime extends FieldElement
{
    public $format = 'Y-m-d H:i'; // using by rule

    public $timepicker = true;


    protected function boot()
    {
        $this
            ->displayUsing(function (Carbon $date) {
                return $date->format($this->format);
            })

            ->resolveUsing($this->displayCallback)

            ->fillUsing(function ($object, $value) {
                $object->{$this->name} = $value ? Carbon::parse($value) : null;
            })

            ->rules(new Validation\DateTimeRule($this))
        ;
    }

    public function format($fomat)
    {
        $this->format = $fomat;

        return $this;
    }
}