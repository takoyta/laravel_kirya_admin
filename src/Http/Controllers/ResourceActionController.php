<?php

namespace KiryaDev\Admin\Http\Controllers;


use KiryaDev\Admin\Core;
use KiryaDev\Admin\Traits;
use KiryaDev\Admin\Fields\Panel;
use KiryaDev\Admin\Fields\HasMany;
use KiryaDev\Admin\Http\Requests\ActionResourceRequest;

class ResourceActionController
{
    use HasConfirmation;


    /**
     * @param ActionResourceRequest $request
     * @return mixed
     */
    public function handle(ActionResourceRequest $request)
    {
        $resource = $request->resource();
        $action = $request->resolveAction();

        if ($action->requireConfirmation) {

            if (! $this->isConfirmed($request)) {
                return $this->renderConfirm($action->label(), $resource, $action->getFormPanels(optional()));
            }

            // Just validate, but for get values use request in your needs
            $action->validateOnFields($request, optional());
            // fixme - run fillCallback on each field
            // pass value to handle
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

            $resource->newFilterProvider($request->relation . '_')->apply($query);
        } else {
            $resource->newFilterProvider()->apply($query = $resource->indexQuery());
        }

        if ($request->ids) {
            $query->whereKey(explode(',', $request->ids));
        }

        return $action->handleMany($resource, $query, $request);
    }
}
