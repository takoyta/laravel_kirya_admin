<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;

class Textarea extends FieldElement
{
    public int $rows = 3;

    protected function boot(): void
    {
        $this->hideFromIndex();
    }

    public function rows(int $rows)
    {
        $this->rows = $rows;

        return $this;
    }
}
