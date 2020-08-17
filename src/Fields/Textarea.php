<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;


class Textarea extends FieldElement
{
    public $rows = 3;


    protected function boot()
    {
        $this->hideFromIndex();
    }

    public function rows($rows)
    {
        $this->rows = $rows;

        return $this;
    }
}
