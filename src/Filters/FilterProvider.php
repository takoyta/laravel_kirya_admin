<?php declare(strict_types=1);

namespace KiryaDev\Admin\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use KiryaDev\Admin\Fields;
use KiryaDev\Admin\Resource\AbstractResource;
use KiryaDev\Admin\Resource\Search;

class FilterProvider
{
    private AbstractResource $resource;

    public ?Fields\Text $searchField = null;

    /**  @var Fields\FieldElement[] */
    public array $fields = [];

    /**  @var Filterable[] */
    private array $filters = [];

    private string $prefix;

    /** @var string[] */
    private array $values = [];

    public int $appliedFiltersCount = 0;

    public function __construct(AbstractResource $resource, string $prefix)
    {
        $this->resource = $resource;
        $this->prefix = $prefix;

        if (!empty($resource->search)) {
            $this->searchField = Fields\Text::make('Search', $this->prefixed('search'));
        }

        foreach ($resource->filters() as $filter) {
            $field = $filter->field();
            $name = $field->name = $this->prefixed($field->name);

            $this->filters[$name] = $filter;
            $this->fields[$name] = $field;

            if ($field instanceof Fields\Select) {
                $field->addNullOption();
            }
        }
    }

    /**
     * Return query value for displaying this fields.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return Arr::get($this->values, $name);
    }

    /**
     * Return prefixed var name.
     *
     * @param string $var
     * @return string
     */
    public function prefixed($var): string
    {
        return $this->prefix . $var;
    }

    /**
     * Return query values for paginator.
     *
     * @param  $name
     * @return mixed
     */
    public function query($name = null)
    {
        if ($name) {
            return request()->query($name);
        }

        return Arr::only(request()->query(), array_keys($this->values));
    }

    /**
     * @param Builder|Relation $builder
     * @return static
     */
    public function apply($builder)
    {
        $this->appliedFiltersCount = 0;

        // Retrieve search value
        if ($this->searchField && $term = $this->query($name = $this->searchField->name)) {
            // Apply
            Search::deepSearch($builder, $this->resource->search, $term);

            $this->values[$name] = $term;
        }

        // Retrieve filters values
        foreach ($this->filters as $name => $filter) {
            if ($value = $this->query($name)) {
                if ($fn = $this->fields[$name]->fillCallback) {
                    $fn($object = new \stdClass, $value); // fixme

                    $value = $object->{$name};
                }
                $this->values[$name] = $value;

                // Apply
                $filter->apply($builder, $value);
                $this->appliedFiltersCount++;
            }
        }

        return $this;
    }
}
