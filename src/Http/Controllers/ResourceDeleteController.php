<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Controllers;

use Illuminate\Support\Facades\DB;
use KiryaDev\Admin\AdminCore;
use KiryaDev\Admin\Http\Requests\DeleteResourceRequest;

class ResourceDeleteController
{
    use Traits\ConfirmsAction;

    public function handle(DeleteResourceRequest $request)
    {
        $object = $request->object();
        $resource = $request->resource();

        if (! $this->isConfirmed($request)) {
            return $this->renderConfirm($resource->actionLabel('Delete') . ' ' . $resource->title($object), $resource);
        }

        try {
            DB::transaction(static function () use ($object) {
                // fixme: delete image via Fields\Image

                $object->delete();
            });
        } catch (\Exception $e) {
            if (app()->isLocal()) {
                throw $e;
            }

            return AdminCore::redirectToPrevious()->with('error', $e->getMessage());
        }

        return redirect($resource->makeUrl('list'))
            ->with('success', __(':title deleted!', ['title' => $resource->labeledTitle($object)]));
    }
}
