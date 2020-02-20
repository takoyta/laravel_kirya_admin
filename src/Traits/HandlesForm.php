<?php

namespace KiryaDev\Admin\Traits;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Validation\ValidatesRequests;

trait HandlesForm
{
    /**
     * @param  \Illuminate\Http\Request             $request
     * @param  \KiryaDev\Admin\Resource\Resource    $resource
     * @param  \Illuminate\Database\Eloquent\Model  $object
     * @return mixed
     */
    protected function doHandle($request, $resource, $object)
    {
        // Handle GET
        if ($request->isMethod('GET')) {
            return $this->render($resource, $object);
        }

        // Validate data
        $data = $resource->validateOnFields($request, $object);

        try {
            $this->ensureRetrievedAfterLastModified($request, $object);

            // Run fill callbacks
            foreach ($resource->collapseFieldsFromPanels($object) as $field) {
                /** @var \KiryaDev\Admin\Fields\FieldElement $field */
                if ($fn = $field->fillCallback) {
                    $fn($object, Arr::pull($data, $field->name));
                }
            }

            $object->forceFill($data);

            DB::transaction(function () use ($object) {
                $object->save();
            });
        } catch (\Exception $e) {
            if (app()->isLocal()) throw $e;

            return redirect()->refresh()->withInput()->with('error', $e->getMessage());
        }

        return $this->successSaving($resource, $object);
    }

    /**
     * @param  \Illuminate\Http\Request             $request
     * @param  \Illuminate\Database\Eloquent\Model  $object
     */
    protected function ensureRetrievedAfterLastModified($request, $object)
    {
        if ($object->exists && $object->usesTimestamps()) {
            $retrivedAt = $request->post('_retrived_at');
            $updatedAt = $object->{$object->getUpdatedAtColumn()};

            abort_unless($retrivedAt > $updatedAt , 422, __('Resource retrieved before last modified.'));
        }
    }

    /**
     * @param  \KiryaDev\Admin\Resource\Resource    $resource
     * @param  \Illuminate\Database\Eloquent\Model  $object
     * @return mixed
     */
    protected function successSaving($resource, $object)
    {
        return redirect($resource
            ->makeUrl('detail', ['id' => $object->getKey()]))
            ->with('success', __($object->wasRecentlyCreated ? 'Resource :title created!' : 'Resource :title updated!', ['title' => $resource->title($object)]));
    }

    /**
     * @param  \KiryaDev\Admin\Resource\Resource    $resource
     * @param  \Illuminate\Database\Eloquent\Model  $object
     * @return mixed
     */
    protected function render($resource, $object)
    {
        $panels = $resource->getFormPanels($object);

        $retrivedAt = ['_retrived_at', old('_retrived_at', now())];

        return view('admin::resource.form', compact('resource', 'panels', 'object', 'retrivedAt'));
    }
}
