<?php

namespace KiryaDev\Admin\Filters;


use KiryaDev\Admin\Fields;
use Illuminate\Support\Arr;
use KiryaDev\Admin\Resource\Search;
use KiryaDev\Admin\Resource\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class FilterProvider
{
    /**
     * @var Resource
     */
    private $resource;

    /**
     * @var Fields\Text
     */
    public $searchField;

    /**
     * @var Fields\FieldElement[]
     */
    public $fields = [];

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string[]
     */
    private $values = [];

    /**
     * @var int
     */
    public $appliedFiltersCount = 0;


    /**
     * FilterProvider constructor.

     * @param  Resource          $resource
     * @param  string            $prefix
     * @param  string[]          $values
     */
    public function __construct(Resource $resource, string $prefix = null, array $values = [])
    {
        $this->resource = $resource;
        $this->prefix = $prefix;

        if (! empty($resource->search)) {
            $this->searchField = Fields\Text::make('Search', $this->prefixed('search'));
        }

        $this->values = $values;
    }

    /**
     * Return query value for displaying this fields.
     *
     * @param  strin  $name
     * @return mixed
     */
    public function __get($name)
    {
        return Arr::get($this->values, $name);
    }

    /**
     * Return prefixed var name.
     *
     * @param  strin  $var
     * @return string
     */
    public function prefixed($var)
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
     * @param  Builder|Relation  $builder
     * @return mixed
     */
    public function apply($builder)
    {
        $this->appliedFiltersCount = 0;

        // Retrive search value
        if ($this->searchField && $term = $this->query($name = $this->searchField->name)) {
            // Apply
            Search::deepSearch($builder, $this->resource->search, $term);

            $this->values[$name] = $term;
        }

        // Retrive filters values
        foreach ($this->resource->filters() as $filter) {
            $this->fields[] = $field = $filter->field();

            if ($field instanceof Fields\Select) {
                $field->addNullOption();
            }

            $name = $field->name = $this->prefixed($field->name);

            if ($value = $this->query($name)) {
                if ($fn = $field->fillCallback) {
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
