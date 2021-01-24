<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Requests;

use KiryaDev\Admin\AdminCore;
use KiryaDev\Admin\Fields;
use KiryaDev\Admin\Resource\AbstractResource;

/**
 * @property-read string $id
 * @property-read string $resource
 * @property-read string $related_resource
 */
class AddRelatedResourceRequest extends AbstractRelatedResourceRequest
{
    public function authorizeAbilityPrefix(): string
    {
        return 'addAny';
    }

    public function resolveParentField(): Fields\HasMany
    {
        return $this
            ->parentResource()
            ->resolveField(Fields\HasMany::class, $this->related_resource);
    }

    public function resolveRelatedField(): Fields\BelongsTo
    {
        $parentField = $this->resolveParentField();

        if ($parentField instanceof Fields\MorphMany) {
            return $this
                ->resource()
                ->resolveField(Fields\MorphTo::class, $parentField->reverseName);
        }

        if ($parentField instanceof Fields\HasMany) {
            return $this
                ->resource()
                ->resolveField(Fields\BelongsTo::class, $this->resource);
        }

        throw new \RuntimeException(sprintf('Unknown related type %s.', get_class($parentField)));
    }
}
