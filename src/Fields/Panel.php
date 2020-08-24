<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;

use Illuminate\Database\Eloquent\Model;
use KiryaDev\Admin\Resource\AbstractResource;

class Panel extends Element
{
    public $fields;


    protected function __construct(string $title, $fields = [])
    {
        parent::__construct($title);

        $this->fields = $fields;
    }

    public function display(AbstractResource $resource, Model $object)
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
