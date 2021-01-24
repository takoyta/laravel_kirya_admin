<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Controllers;

use KiryaDev\Admin\Http\Requests\AttachRelatedResourceRequest;
use KiryaDev\Admin\Http\Requests\DetachRelatedResourceRequest;

class AttachRelatedController
{
    use Traits\ViewingObjects;

    public function attach(AttachRelatedResourceRequest $request)
    {
        $resource = $request->resource();

        // If has related do attach.
        if ($request->related_id) {
            $request->resolveRelation()->attach(
                $request->related_id
            );

            return $this->redirectToParentObject($request, ':title attached!');
        }

        $query = $resource->indexQuery();
        $relation = $request->resolveRelation();

        // Select only yet not attached
        $query->whereNotIn(
            $query->getModel()->getQualifiedKeyName(),
            $relation->newPivotQuery()->select([
                $relation->getQualifiedRelatedPivotKeyName()
            ])
        );

        $parentObject = $request->parentObject();
        $parentResource = $request->parentResource();
        $pageTitle = __('Attach :related to :parent', ['related' => __($resource::pluralLabel()), 'parent' => $parentResource->labeledTitle($parentObject)]);

        return $this->viewObjects(
            $resource,
            $query,
            $pageTitle,
            fn($commonActions) => null,
            fn($singleActions) => $singleActions->add(
                $parentResource
                    // TODO: add parent object to check ability
                    ->makeActionLink('attachRelated', 'attach' . $resource->modelName())
                    ->param('id', $parentObject->getKey())
                    ->param('related_resource', $resource::uriKey())
                    ->objectKey('related_id')
                    ->icon('thumbtack text-success')
                    ->displayAsLink()
            ),
        );
    }

    public function detach(DetachRelatedResourceRequest $request)
    {
        $request->resolveRelation()->detach(
            $request->related_id
        );

        return $this->redirectToParentObject($request, ':title detached!');
    }

    protected function redirectToParentObject(AttachRelatedResourceRequest $request, string $message)
    {
        return redirect()
            ->to(
                $request->parentResource()->makeUrl('detail', ['id' => $request->parentObject()->getKey()])
            )
            ->with(
                'success',
                __($message, ['title' => $request->resource()->labeledTitle($request->related_id)])
            );
    }
}
