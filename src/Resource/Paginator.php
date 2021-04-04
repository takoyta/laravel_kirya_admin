<?php declare(strict_types=1);

namespace KiryaDev\Admin\Resource;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use KiryaDev\Admin\Fields\FieldElement;

class Paginator
{
    /** Laravel Paginator */
    private LengthAwarePaginator $lengthAwarePaginator;

    /** Current order column */
    private string $order;

    /** Current order direction  */
    private string $dir;

    /**Current request path */
    private string $path;

    /** Current request query */
    private array $query;

    /**Prefix for Paginator (if any Paginator in request) */
    private string $prefix;

    /**
     * Paginator constructor.
     */
    public function __construct(Builder $builder, int $perPage, string $prefix = '', array $query = [])
    {
        $this->path = LengthAwarePaginator::resolveCurrentPath();

        $this->prefix = $prefix;

        $this->query = $query;

        $qb = $builder->getQuery(); // There is gets Database\Query\Builder from Eloquent\Query\Builder

        $orders = request()->only([
            $this->prefixed('order'),
            $this->prefixed('dir'),
        ]);

        if (\count($orders) === 2) {
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
     * Return prefixed var name.
     *
     * @param string $var
     * @return string
     */
    private function prefixed(string $var): string
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
