<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use KiryaDev\Admin\AdminCore;
use KiryaDev\Admin\Http\Requests\ActionResourceRequest;

class ResourceActionController
{
    use Traits\ConfirmsAction;

    /**
     * @param ActionResourceRequest $request
     * @return mixed
     */
    public function handle(ActionResourceRequest $request)
    {
        $resource = $request->resource();
        $action = $request->resolveAction();

        $virtualModel = new class extends Model {
        };

        if ($action->requireConfirmation) {
            if (!$this->isConfirmed($request)) {
                return $this->renderConfirm($action->label(), $resource, $action->getFormPanels($virtualModel));
            }

            // Just validate, but for get values use request in your needs
            $action->validateOnFields($request, $virtualModel);
            // fixme - run fillCallback on each field
            // pass value to handle
        }

        if ($request->forOne()) {
            $object = $request->object();

            if (method_exists($action, 'handleOneFromIndex')) {
                return $action->handleOneFromIndex($resource, $object, $request);
            }

            if (method_exists($action, 'handleOneFromDetail')) {
                return $action->handleOneFromDetail($resource, $object, $request);
            }

            throw new \RuntimeException('No match method: handleOneFromIndex or handleOneFromDetail.');
        }

        if ($request->from) {
            $query = $resource->findModel($request->id)->{$request->relation}();
            $resource = AdminCore::resourceByKey($request->resource);

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
