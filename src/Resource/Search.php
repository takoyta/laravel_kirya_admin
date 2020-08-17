<?php declare(strict_types=1);

namespace KiryaDev\Admin\Resource;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class Search
{
    /**
     * Search models by many columns and through many tables.
     *
     * @param  Builder|Relation  $query
     * @param  string|array      $columns
     * @param  string            $term
     */
    public static function deepSearch($query, $columns, $term)
    {
        static::treeIterator(
            static::buildSearchTree($columns),
            $query,
            Str::upper($term)
        );
    }

    /**
     * Build a search tree to access the columns through relationship models.
     *
     * @param  string|array  $columns
     * @return array
     */
    private static function buildSearchTree($columns)
    {
        $tree = [];

        foreach ((array)$columns as $qualifiedColumn) {
            Arr::set($tree, $qualifiedColumn, false);
        }

        return $tree;
    }

    /**
     * @param  array             $node
     * @param  Builder|Relation  $query
     * @param  string            $term
     */
    private static function treeIterator($node, $query, $term)
    {
        $query->where(function (Builder $query) use ($node, $term) {
            foreach ($node as $column => $nestedNode) {
                if (is_array($nestedNode)) {
                    $query->orWhereHas($column, function (Builder $nestedQuery) use ($nestedNode, $term) {
                        static::treeIterator($nestedNode, $nestedQuery, $term);
                    });
                } else {
                    $query->orWhereRaw("upper({$column}) like '%{$term}%'");
                }
            }
        });
    }
}
