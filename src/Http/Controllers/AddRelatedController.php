<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Controllers;


use KiryaDev\Admin\Fields\MorphTo;
use KiryaDev\Admin\Traits\HandlesForm;
use KiryaDev\Admin\Http\Requests\AddRelatedResourceRequest;

class AddRelatedController
{
    use HandlesForm;


    public function handle(AddRelatedResourceRequest $request)
    {
        $resource = $request->relatedResource();

        $object = $resource->newModel();

        // fixme  right use HasMany.. then when saving call $hasManyField->save($object)
        $field = $request->resolveRelatedField();
        $field->disable();
        if ($field instanceof MorphTo) {
            ($field->fillCallback)($object, [$request->resource => $request->id]);
        } else {
            ($field->fillCallback)($object, $request->id);
        }


        return $this->doHandle(
            $request,
            $resource,
            $object
        );
    }
}
