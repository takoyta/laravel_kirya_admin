<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use KiryaDev\Admin\Http\Requests\IndexResourceRequest;

class ApiController
{
    public function getObjects(IndexResourceRequest $request): JsonResponse
    {
        $resource = $request->resource();

        $resource->newFilterProvider()->apply(
            $query = $resource->searchQuery()
        );

        $paginator = $query->paginate(15);

        /** Build response for Select2. Read the docs here https://select2.org/data-sources/formats */
        $results = $paginator
            ->map(static function (Model $object) use ($resource) {
                return [
                    'id' => $object->getKey(),
                    'text' => $resource->title($object),
                ];
            });

        $pagination = ['more' => $paginator->hasMorePages()];

        return response()->json(compact('results', 'pagination'));
    }
}
