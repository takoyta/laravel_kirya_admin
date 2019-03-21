<?php

namespace KiryaDev\Admin\Fields;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class Image extends FieldElement
{
    public $prefix = 'storage/images';

    public $nullable = true;


    protected function boot()
    {
        $this->displayUsing(function ($value) {
            return "<img src='{$value}' style='max-width: 100px'></img>";
        });

        $this->fillUsing(function ($object, $value) {
            // delete current
            if (
                ($value === 'unlink' || $value instanceof UploadedFile)
                && ($currentValue = $object->{$this->name})
                && file_exists($path = public_path($currentValue))
            ) {
                unlink($path);

                $object->{$this->name} = null;
            }

            if ($value instanceof UploadedFile) {
                $value->move(
                    public_path($this->prefix),
                    $filename = $this->prepareFilename($value)
                );

                $object->{$this->name} = "/{$this->prefix}/{$filename}";
            }
        });
    }

    protected function prepareFilename(UploadedFile $file)
    {
        [$name, $ext] = [$file->getClientOriginalName(), $file->getClientOriginalExtension()];

        return Str::slug(Str::before($name, '.' . $ext))
            . '-' . Str::random(12)
            . '.' . $ext;
    }

    public function prefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }
}