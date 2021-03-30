<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;

use Illuminate\Database\Eloquent\Model;
use KiryaDev\Admin\Resource\AbstractResource;

class Panel extends Element implements Panelable
{
    public iterable $fields;


    protected function __construct(string $title, iterable $fields = [])
    {
        parent::__construct($title);

        $this->fields = $fields;
    }

    public function displayValue(Model $object)
    {
        $panel = $this;

        return view('admin::resource.detail-partials.panel', compact('panel', 'object'));
    }

    public function displayForm(Model $object)
    {
        $panel = $this;

        return view($this->resolveFormView(), compact('panel', 'object'));
    }
}
