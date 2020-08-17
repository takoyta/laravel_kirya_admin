<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;


use Carbon\Carbon;

class Date extends DateTime
{
    public $format = 'Y-m-d';

    public $timepicker = false;
}