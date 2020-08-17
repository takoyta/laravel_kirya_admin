<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Controllers;

use KiryaDev\Admin\Resource\Paginator;
use KiryaDev\Admin\Http\Requests\IndexResourceRequest;

class ResourceIndexController
{
    public function handle(IndexResourceRequest $request)
    {
        $resource = $request->resource();

        $actions = $resource
            ->getActionLinksForHandleMany()
            ->add($resource->makeActionLink('create'));

        $fields = $resource
            ->getIndexFields()
            ->add($resource->getIndexActionsField());

        // Search & Filter
        $filterProvider = $resource->newFilterProvider()->apply(
            $query = $resource->indexQuery()
        );

        // Paginate results
        $paginator = new Paginator($query, $resource->perPage, '', $filterProvider->query());

        return view('admin::resource.index', compact(
            'resource',
            'actions',
            'fields',
            'filterProvider',
            'paginator'
        ));
    }
}
