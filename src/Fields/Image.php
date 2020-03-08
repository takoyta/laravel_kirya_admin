<?php

namespace KiryaDev\Admin\Fields;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class Image extends File
{
    public $accept = 'image/*';

    public $prefix = 'storage/images';


    protected function boot()
    {
        parent::boot();

        $this->displayUsing(function ($value) {
            return "<img src='{$value}' style='max-width: 100px'></img>";
        });
    }
}