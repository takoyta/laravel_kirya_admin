<?php

namespace KiryaDev\Admin\Resource;


use Illuminate\Support\Arr;
use KiryaDev\Admin\Fields\FieldElement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\Relation;

class Paginator
{
    /**
     * Laravel Paginator
     *
     * @var LengthAwarePaginator
     */
    private $lengthAwarePaginator;

    /**
     * Current order column
     *
     * @var array
     */
    private $order;

    /**
     * Current order direction
     *
     * @var array
     */
    private $dir;

    /**
     * Current request path
     *
     * @var string
     */
    private $path;

    /**
     * Current request query
     *
     * @var array
     */
    private $query;

    /**
     * Prefix for Paginator (if any Paginator in request)
     *
     * @var string
     */
    private $prefix;


    /**
     * Paginator constructor.
     *
     * @param Relation|Builder $builder
     * @param int $perPage
     * @param string $prefix
     * @param array $query
     */
    public function __construct($builder, $perPage, $prefix = '', array $query = [])
    {
        $this->path = LengthAwarePaginator::resolveCurrentPath();

        $this->prefix = $prefix;

        $this->query = $query;


        $qb = $this->getQueryBuilder($builder);

        $orders = request()->only([
            $this->prefixed('order'),
            $this->prefixed('dir'),
        ]);

        if (count($orders) == 2) {
            // Clear previous order
            $qb->orders = [];
            // Set order from request
            $qb->orderBy(...array_values($orders));
        }
        if (empty($qb->orders)) {
            // Default set reverse order by ID
            $qb->latest('id');
        }

        [$this->order, $this->dir] = array_values($qb->orders[0]);

        $this->lengthAwarePaginator = $builder->paginate($perPage, ['*'], $this->prefixed('page'));
        $this->lengthAwarePaginator->appends($this->query + $orders);
    }

    /**
     * Resolves Query Builder
     *
     * @param $builder
     * @return \Illuminate\Database\Query\Builder
     */
    private function getQueryBuilder($builder)
    {
        if ($builder instanceof Relation) $builder = $builder->getQuery();

        if ($builder instanceof Builder) return $builder->getQuery();

        throw new \RuntimeException(
            sprintf('Non query class %s.', get_class($builder))
        );
    }

    /**
     * Return prefixed var name.
     *
     * @param  strin  $var
     * @return string
     */
    private function prefixed($var)
    {
        return $this->prefix . $var;
    }

    public function items()
    {
        return $this->lengthAwarePaginator->items();
    }

    public function links()
    {
        return $this->lengthAwarePaginator->links();
    }

    public function total()
    {
        return $this->lengthAwarePaginator->total();
    }

    public function isOrderedBy(FieldElement $field, $dir)
    {
        return $this->order === $field->name
            && $this->dir === $dir;
    }

    public function orderUrl(FieldElement $field, $dir)
    {
        return $this->path
            . '?'
            . Arr::query($this->query + [
                $this->prefixed('order') => $field->name,
                $this->prefixed('dir') => $dir,
            ]);
    }
}
