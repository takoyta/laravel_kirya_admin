<?php declare(strict_types=1);

namespace KiryaDev\Admin\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use KiryaDev\Admin\Fields\FieldElement;
use KiryaDev\Admin\Resource\AbstractResource;

trait HandlesForm
{
    protected function doHandle(Request $request, AbstractResource $resource, $object)
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
                /** @var FieldElement $field */
                if ($fn = $field->fillCallback) {
                    $fn($object, Arr::pull($data, $field->name));
                }
            }

            $object->forceFill($data);

            DB::transaction(static function () use ($object) {
                $object->save();
            });
        } catch (\Exception $e) {
            if (app()->isLocal()) {
                throw $e;
            }

            return redirect()->refresh()->withInput()->with('error', $e->getMessage());
        }

        return $this->successSaving($resource, $object);
    }

    protected function ensureRetrievedAfterLastModified(Request $request, Model $object): void
    {
        if ($object->exists && $object->usesTimestamps()) {
            $retrievedAt = $request->post('_retrieved_at');
            $updatedAt = $object->{$object->getUpdatedAtColumn()};

            abort_unless($retrievedAt > $updatedAt, 422, __('Resource retrieved before last modified.'));
        }
    }

    protected function successSaving(AbstractResource $resource, Model $object)
    {
        return redirect($resource
            ->makeUrl('detail', ['id' => $object->getKey()]))
            ->with('success', __($object->wasRecentlyCreated ? 'Resource :title created!' : 'Resource :title updated!', ['title' => $resource->title($object)]));
    }

    protected function render(AbstractResource $resource, Model $object)
    {
        $panels = $resource->getFormPanels($object);

        $retrievedAt = ['_retrieved_at', old('_retrieved_at', now())];

        return view('admin::resource.form', compact('resource', 'panels', 'object', 'retrievedAt'));
    }
}
