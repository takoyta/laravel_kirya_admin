<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Controllers;


use KiryaDev\Admin\Http\Requests\DetailResourceRequest;

class ResourceDetailController
{
    public function handle(DetailResourceRequest $request)
    {
        $resource = $request->resource();

        $actions = $resource
            ->getActionLinksForHandleOneFromDetail()
            ->merge([
                $resource->makeActionLink('edit', 'update')->icon('edit'), // fixme: change altTitle from Update -> Edit
                $resource->makeActionLink('delete')->icon('trash'),
            ]);

        $panels = $resource->getDetailPanels();

        $object = $request->object();

        return view('admin::resource.detail', compact('resource', 'actions', 'panels', 'object'));
    }
}