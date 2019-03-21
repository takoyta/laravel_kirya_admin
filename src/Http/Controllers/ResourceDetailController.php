<?php

namespace KiryaDev\Admin\Http\Controllers;


use KiryaDev\Admin\Http\Requests\DetailResourceRequest;

class ResourceDetailController
{
    public function handle(DetailResourceRequest $request)
    {
        $resource = $request->resource();

        $actions = $resource->getDetailActions(); //todo

        $panels = $resource->getDetailPanels();

        $object = $request->object();

        return view('admin::resource.detail', compact('resource', 'actions', 'panels', 'object'));
    }
}