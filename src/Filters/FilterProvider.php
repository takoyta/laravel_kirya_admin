<?php declare(strict_types=1);

namespace KiryaDev\Admin\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use KiryaDev\Admin\Fields;
use KiryaDev\Admin\Resource\AbstractResource;
use KiryaDev\Admin\Resource\Search;

class FilterProvider
{
    private AbstractResource $resource;

    public ?Fields\Text $searchField = null;

    public Model $virtualModel;

    /**  @var Fields\FieldElement[] */
    public array $fields = [];

    /**  @var Filterable[] */
    private array $filters = [];

    private string $prefix;

    public int $appliedFiltersCount = 0;

    public function __construct(AbstractResource $resource, string $prefix)
    {
        $this->resource = $resource;
        $this->prefix = $prefix;

        $this->virtualModel = new class extends Model {
        };

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
     * Return prefixed var name.
     */
    public function prefixed(string $var): string
    {
        return $this->prefix . $var;
    }

    /**
     * Return query values for paginator.
     */
    public function getValues(): array
    {
        return $this->virtualModel->getAttributes();
    }

    /**
     * @param Builder|Relation $builder
     * @return static
     */
    public function apply($builder)
    {
        $this->appliedFiltersCount = 0;

        // Retrieve search value
        if ($this->searchField && $term = request()->query($name = $this->searchField->name)) {
            // Apply
            Search::deepSearch($builder, $this->resource->search, $term);

            $this->searchField->fill($this->virtualModel, $term);
        }

        // Retrieve filters values

        foreach ($this->filters as $name => $filter) {
            if ($value = request()->query($name)) {
                $this->fields[$name]->fill($this->virtualModel, $value);

                // Apply
                $filter->apply($builder, $value);
                $this->appliedFiltersCount++;
            }
        }

        return $this;
    }
}
