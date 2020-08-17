<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Controllers;

use KiryaDev\Admin\Traits\HandlesForm;
use KiryaDev\Admin\Http\Requests\CreateResourceRequest;

class ResourceCreateController
{
    use HandlesForm;

    public function handle(CreateResourceRequest $request)
    {
        return $this->doHandle(
            $request,
            $resource = $request->resource(),
            $resource->newModel()
        );
    }
}
