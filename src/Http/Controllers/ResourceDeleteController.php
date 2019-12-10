<?php

namespace KiryaDev\Admin\Http\Controllers;

use Illuminate\Support\Facades\DB;
use KiryaDev\Admin\Http\Requests\DeleteResourceRequest;

class ResourceDeleteController
{
    use HasConfirmation;


    public function handle(DeleteResourceRequest $request)
    {
        $object = $request->object();

        $listUrl = $request->resource()->makeUrl('list');

        if (! $this->isConfirmed($request)) {
            return $this->renderConfirm(
                $request->resource()->actionLabel('Delete')
                . ' '
                . $request->resource()->title($object),
                $listUrl
            );
        }

        try {
            DB::transaction(function () use ($object) {
                // fixme: delete image via Fields\Image

                $object->delete();
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect($listUrl)->with('success', __('Resource deleted!'));
    }
}
