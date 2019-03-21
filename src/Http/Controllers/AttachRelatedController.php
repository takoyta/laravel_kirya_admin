<?php

namespace KiryaDev\Admin\Http\Controllers;


use KiryaDev\Admin\Core;
use KiryaDev\Admin\Http\Requests\AttachRelatedResourceRequest;

class AttachRelatedController
{
    public function attach(AttachRelatedResourceRequest $request)
    {
        if ($request->isMethod('GET')) {
            return $this->objects($request);
        }

        $this->relation($request)->attach(
            $request->related_id
        );

        return response()->json([], 201);
    }

    public function detach(AttachRelatedResourceRequest $request)
    {
        $this->relation($request)->detach(
            $request->related_id
        );

        return redirect()->to(
            $request->parentResource()->makeUrl('detail', ['id' => $request->id])
        );
    }

    protected function objects(AttachRelatedResourceRequest $request)
    {
        $resource = $request->resource();

        $attachedIds = $this->relation($request)->get()->modelKeys();

        $notAttached = $resource
            ->searchQuery()
            ->whereNotIn('id', $attachedIds)
            ->get();

        $data = $notAttached->mapWithKeys(function ($object) use ($resource) {
            return [
                $object->id => $resource->title($object)
            ];
        });

        return response()->json(
            compact('data')
        );
    }

    /**
     * @param  AttachRelatedResourceRequest  $request
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    protected function relation(AttachRelatedResourceRequest $request)
    {
        $name = $request->resolveParentField()->name;

        return $request->parentObject()->{$name}();
    }
}