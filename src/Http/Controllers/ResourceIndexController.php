<?php

namespace KiryaDev\Admin\Http\Controllers;


use KiryaDev\Admin\Resource\Paginator;
use KiryaDev\Admin\Fields\ActionsField;
use KiryaDev\Admin\Filters\FilterProvider;
use KiryaDev\Admin\Fields\Text as SearchField;
use KiryaDev\Admin\Http\Requests\IndexResourceRequest;

class ResourceIndexController
{
    public function handle(IndexResourceRequest $request)
    {
        $resource = $request->resource();

        $actions = $resource->actions();

        $fields = $resource
            ->getIndexFields()
            ->add(
                ActionsField::with($resource->getIndexActions())
            );

        $query = $resource->indexQuery();

        // Search & Filter
        $filterProvider = new FilterProvider($query, $resource);

        // Paginate results
        $paginator = new Paginator($query, $resource->perPage, null, $filterProvider->query());

        return view('admin::resource.index', compact(
            'resource',
            'actions',
            'fields',
            'filterProvider',
            'paginator'
        ));
    }
}