<?php

namespace KiryaDev\Admin\Http\Controllers;

use KiryaDev\Admin\Core;
use Illuminate\Support\Facades\DB;
use KiryaDev\Admin\Http\Requests\DeleteResourceRequest;

class ResourceDeleteController
{
    use HasConfirmation;


    public function handle(DeleteResourceRequest $request)
    {
        $object = $request->object();
        $resource = $request->resource();
        $objectTitle = $resource->title($object);

        if (! $this->isConfirmed($request)) {
            return $this->renderConfirm($resource->actionLabel('Delete') . ' ' . $objectTitle, $resource);
        }

        try {
            DB::transaction(function () use ($object) {
                // fixme: delete image via Fields\Image

                $object->delete();
            });
        } catch (\Exception $e) {
            if (app()->isLocal()) throw $e;

            return Core::redirectToPrevious()->with('error', $e->getMessage());
        }

        return redirect($resource->makeUrl('list'))
            ->with('success', __('Resource :title deleted!', ['title' => $objectTitle]));
    }
}
