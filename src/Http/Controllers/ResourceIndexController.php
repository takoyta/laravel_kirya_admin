<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Controllers;

use KiryaDev\Admin\Http\Controllers\Traits\ViewingObjects;
use KiryaDev\Admin\Http\Requests\IndexResourceRequest;

class ResourceIndexController
{
    use ViewingObjects;

    public function handle(IndexResourceRequest $request)
    {
        $resource = $request->resource();

        return $this->viewObjects(
            $resource,
            $resource->indexQuery(),
            __($resource->pluralLabel()),
            fn($commonActions) => $commonActions[] = $resource->makeActionLink('create'),
            fn($singleActions) => null,
        );
    }
}
