<?php

namespace KiryaDev\Admin\Fields;


use Carbon\Carbon;

class Date extends DateTime
{
    public $format = 'Y-m-d';

    public $timepicker = false;
}