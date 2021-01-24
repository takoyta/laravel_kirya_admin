<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany as EloquentBelongsToMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;
use KiryaDev\Admin\Http\Requests\IndexResourceRequest;
use KiryaDev\Admin\Resource\AbstractResource;
use KiryaDev\Admin\Resource\Paginator;

trait ViewingObjects
{
    public function viewObjects(
        AbstractResource $resource,
        Builder $query,
        string $pageTitle,
        callable $commonActionsModifier,
        callable $signleActionsModifier
    ) {
        $commonActionsModifier(
            $actions = $resource->getActionLinksForHandleMany()
        );
        $signleActionsModifier(
            $signleActions = $resource->getIndexActionsField()
        );
        $fields = $resource->getIndexFields()->add($signleActions);

        // Search & Filter
        $filterProvider = $resource->newFilterProvider()->apply($query);

        // Paginate results
        $paginator = new Paginator($query, $resource->perPage, '', $filterProvider->getValues());

        return view('admin::resource.index', compact(
            'pageTitle',
            'resource',
            'actions',
            'fields',
            'filterProvider',
            'paginator'
        ));
    }

    protected function paginateObjects(AbstractResource $resource): JsonResponse
    {
        $resource->newFilterProvider()->apply(
            $query = $resource->searchQuery()
        );

        $paginator = $query->paginate(15);

        /** Build response for Select2. Read the docs here https://select2.org/data-sources/formats */
        $results = $paginator
            ->map(static function (Model $object) use ($resource) {
                return ['id' => $object->getKey(), 'text' => $resource->title($object)];
            });

        $pagination = ['more' => $paginator->hasMorePages()];

        return response()->json(compact('results', 'pagination'));
    }
}
