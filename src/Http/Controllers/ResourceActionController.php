<?php

namespace KiryaDev\Admin\Http\Controllers;


use KiryaDev\Admin\Core;
use KiryaDev\Admin\Fields\HasMany;
use KiryaDev\Admin\Http\Requests\ActionResourceRequest;

class ResourceActionController
{
    use HasConfirmation;


    /**
     * @param  ActionResourceRequest  $request
     * @return mixed
     */
    public function handle(ActionResourceRequest $request)
    {
        $resource = $request->resource();
        $action = $request->resolveAction();

        if ($action->requireConfirmation && ! $this->isConfirmed($request)) {
            Core::setPreviousUrl();

            return $this->renderConfirm($action->label());
        }

        if ($request->forOne()) {
            $object = $request->object();

            if (method_exists($action, 'handleOneFromIndex')) {
                return $action->handleOneFromIndex($resource, $object, $request);
            }

            return $action->handleOneFromDetail($resource, $object, $request);
        }

        if ($request->from) {
            $query = $resource->findModel($request->id)->{$request->relation}();
            $resource = Core::resourceByKey($request->resource);

            $resource->newFilterProvider($request->relation.'_')->apply($query);
        } else {
            $resource->newFilterProvider()->apply($query = $resource->indexQuery());
        }

        if ($request->ids) {
            $query->whereKey(explode(',', $request->ids));
        }

        return $action->handleMany($resource, $query, $request);
    }
}
