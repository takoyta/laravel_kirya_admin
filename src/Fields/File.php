<?php

namespace KiryaDev\Admin\Fields;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class File extends FieldElement
{
    public $accept = '*';

    public $prefix = 'storage/files';

    public $nullable = true;


    protected function boot()
    {
        $this->displayUsing(function ($value) {
            return basename($value);
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

    public function accept($accept)
    {
        $this->accept = $accept;

        return $this;
    }

    public function prefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }
}
