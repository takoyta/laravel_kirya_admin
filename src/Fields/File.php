<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class File extends FieldElement
{
    public string $accept = '*';

    public string $prefix = 'storage/files';

    public bool $nullable = true;

    protected function boot(): void
    {
        $this->displayUsing(static function (Model $object, $value) {
            return basename($value);
        });

        $this->fillUsing(function (Model $object, $value) {
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

    public function accept(string $accept)
    {
        $this->accept = $accept;

        return $this;
    }

    public function prefix(string $prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }
}
