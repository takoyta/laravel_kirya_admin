<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Controllers;


use KiryaDev\Admin\Traits\HandlesForm;
use KiryaDev\Admin\Http\Requests\UpdateResourceRequest;

class ResourceUpdateController
{
    use HandlesForm;


    public function handle(UpdateResourceRequest $request)
    {
        return $this->doHandle(
            $request,
            $resource = $request->resource(),
            $resource->findModel($request->id)
        );
    }
}