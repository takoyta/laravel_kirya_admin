<?php

namespace KiryaDev\Admin\Http\Controllers;


use KiryaDev\Admin\Fields\HasMany;
use KiryaDev\Admin\Http\Requests\ActionResourceRequest;
use KiryaDev\Admin\Http\Requests\RelatedActionResourceRequest;

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
            $backUrl = $request->forMany()
                ? $resource->makeUrl('list')
                : $resource->makeUrl('detail', ['id' => $request->id]);

            return $this->renderConfirm($action->label(), $backUrl);
        }

        if ($request->forMany()) {
            $resource->newFilterProvider()->apply($query = $resource->indexQuery());

            return $action->handleMany($resource, $query, $request);
        }

        $object = $request->object();

        if (method_exists($action, 'handleOneFromIndex')) {
            return $action->handleOneFromIndex($resource, $object, $request);
        }

        return $action->handleOneFromDetail($resource, $object, $request);
    }

    /**
     * @param  RelatedActionResourceRequest  $request
     * @return mixed
     */
    public function handleRelated(RelatedActionResourceRequest $request)
    {
        $action = $request->resolveAction();

        $field = $request->resolveHasManyField();
        $object = $request->object();

        return $action->handleMany($field->relatedResource, $field->getRelation($object), $request);
    }
}