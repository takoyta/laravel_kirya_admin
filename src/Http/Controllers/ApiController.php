<?php

namespace KiryaDev\Admin\Http\Controllers;


use KiryaDev\Admin\Core;
use KiryaDev\Admin\Resource\Search;
use KiryaDev\Admin\Http\Requests\IndexResourceRequest;

class ApiController
{
    /**
     * Read docs here https://select2.org/data-sources/formats
     *
     * @param IndexResourceRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getObjects(IndexResourceRequest $request)
    {
        $resource = $request->resource();

        $query = $resource->searchQuery();

        if (($term = $request->query('term')) && $resource->search) {
            Search::deepSearch($query, $resource->search, $term);
        }

        $paginator = $query->paginate(15);

        $results = $paginator
            ->map(function ($object) use ($resource) {
                return ['id' => $object->getKey(), 'text' => $resource->title($object)];
            });

        $pagination = ['more' => $paginator->hasMorePages()];

        return response()->json(compact('results', 'pagination'));
    }
}