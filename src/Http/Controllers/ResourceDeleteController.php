<?php

namespace KiryaDev\Admin\Http\Controllers;

use Illuminate\Support\Facades\DB;
use KiryaDev\Admin\Http\Requests\DeleteResourceRequest;

class ResourceDeleteController
{
    public function handle(DeleteResourceRequest $request)
    {
        $object = $request->object();

        if ($request->isMethod('GET')) {
            return $this->render($request, $object);
        }

        try {
            DB::transaction(function () use ($object) {
                // fixme: delete image via Fields\Image

                $object->delete();
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        $listUrl = $request->resource()->makeUrl('list');

        return redirect($listUrl)->with('success', __('Resource deleted!'));
    }

    /**
     * @param  \KiryaDev\Admin\Http\Requests\DeleteResourceRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model                  $object
     * @return mixed
     */
    protected function render($request, $object)
    {
        return view('admin::resource.delete', [
            'resource'  => $request->resource(),
            'object'    => $object,
            'altAction' => $request->resource()->makeActionLink('detail', 'view', 'No'),
        ]);
    }
}