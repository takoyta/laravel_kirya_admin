<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;

use Illuminate\Database\Eloquent\Model;

class Table extends Element implements Panelable
{
    private array $titles = [];
    public array $classes = [];
    public string $noData = 'No data to display.';
    private $dataProvider;


    protected function __construct(string $title, $dataProvider)
    {
        parent::__construct($title);

        $this->dataProvider = $dataProvider;
        if (!is_callable($dataProvider) && !is_string($dataProvider)) {
            throw new \RuntimeException('Data prodiver for Table must be callable or string.');
        }
    }

    public function titles(string ...$titles)
    {
        $this->titles = $titles;

        return $this;
    }

    public function classes(string ...$classes)
    {
        $this->classes = $classes;

        return $this;
    }

    public function noData(string $noData)
    {
        $this->noData = $noData;

        return $this;
    }

    public function displayValue(Model $object)
    {
        $tableBody = is_callable($this->dataProvider)
            ? ($this->dataProvider)($object)
            : $object->{$this->dataProvider};

        $tableTitles = empty($this->titles) && isset($tableBody[0])
            ? array_keys($tableBody[0])
            : $this->titles;

        return view('admin::resource.detail-partials.table', [
            'fieldTitle' => $this->title,
            'tableTitles' => $tableTitles,
            'tableBody' => $tableBody,
            'tableClasses' => $this->classes,
            'noData' => $this->noData,
        ]);
    }
}
