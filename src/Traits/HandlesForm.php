<?php

namespace KiryaDev\Admin\Traits;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Validation\ValidatesRequests;

trait HandlesForm
{
    use ValidatesRequests;


    /**
     * @param  \Illuminate\Http\Request             $request
     * @param  \KiryaDev\Admin\Resource\Resource    $resource
     * @param  \Illuminate\Database\Eloquent\Model  $object
     * @return mixed
     */
    protected function doHandle($request, $resource, $object)
    {
        // Resolve panels
        $panels = $resource->getFormPanels($object);

        // Handle GET
        if ($request->isMethod('GET')) {
            return $this->render($resource, $object, $panels);
        }

        // Handle POST
        $fields = $this->handlesFields($panels);

        // Validate data
        $data = $this->validate(
            $request,
            $this->validationRules($fields, $object),
            [],
            $this->validationAttributes($fields)
        );

        try {
            $this->ensureRetrievedAfterLastModified($request, $object);

            DB::transaction(function () use ($fields, $object, &$data) {
                // Run fill callbacks
                foreach ($fields as $field) {
                    /** @var \KiryaDev\Admin\Fields\FieldElement $field */
                    if ($fn = $field->fillCallback) {
                        $fn($object, Arr::pull($data, $field->name));
                    }
                }

                // Fill & Save
                $object->forceFill($data)->save();
            });
        } catch (\Exception $e) {
            return redirect()->refresh()->withInput()->with('error', $e->getMessage());
        }

        return $this->successSaving($resource, $object);
    }

    /**
     * @param  \KiryaDev\Admin\Fields\Panel[]  $panels
     * @return \Illuminate\Support\Collection
     */
    protected function handlesFields($panels)
    {
        return collect($panels)
            ->pluck('fields')
            ->collapse()
            ->whereStrict('disabled', false);
    }

    /**
     * @param  \Illuminate\Support\Collection       $fields
     * @param  \Illuminate\Database\Eloquent\Model  $object
     * @return array
     */
    protected function validationRules($fields, $object)
    {
        if ($object->exists && ($id = $object->getKey())) {
            return $fields
                ->pluck('updateRules', 'name')->map(function ($rule) use ($id) {
                    return str_replace('{id}', $id, $rule);
                })
                ->all();
        }

        return $fields->pluck('creationRules', 'name')->all();
    }

    /**
     * @param  \Illuminate\Support\Collection       $fields
     * @return array
     */
    protected function validationAttributes($fields)
    {
        return $fields->pluck('title', 'name')->all();
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
     * @param  \KiryaDev\Admin\Resource\Resource    $request
     * @param  \Illuminate\Database\Eloquent\Model  $object
     * @return mixed
     */
    protected function successSaving($resource, $object)
    {
        $backUrl = $resource->makeUrl('detail', [
            'id' => $object->getKey(),
        ]);

        return redirect($backUrl)->with('success', __($object->wasRecentlyCreated ? 'Resource created!' : 'Resource updated!'));
    }

    /**
     * @param  \KiryaDev\Admin\Resource\Resource    $resource
     * @param  \Illuminate\Database\Eloquent\Model  $object
     * @param  \KiryaDev\Admin\Fields\Panel[]       $panels
     * @return mixed
     */
    protected function render($resource, $object, $panels)
    {
        $retrivedAt = ['_retrived_at', old('_retrived_at', now())];

        return view('admin::resource.form', compact('resource', 'panels', 'object', 'retrivedAt'));
    }
}
