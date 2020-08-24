<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;

class Date extends DateTime
{
    public string $format = 'Y-m-d';

    public bool $timepicker = false;
}
