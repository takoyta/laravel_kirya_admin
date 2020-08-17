<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Requests;


use KiryaDev\Admin\Core;
use KiryaDev\Admin\Fields\MorphTo;
use KiryaDev\Admin\Fields\HasMany;
use KiryaDev\Admin\Fields\MorphMany;
use KiryaDev\Admin\Fields\BelongsTo;

/**
 * @property-read  string  $id
 * @property-read  string  $resource
 * @property-read  string  $related_resource
 */
class AddRelatedResourceRequest extends CreateResourceRequest
{
    public function authorize()
    {
        return $this
            ->resource()
            ->authorizedTo(
                'add' . $this->relatedResource()->modelName(),
                $this->resource()->findModel($this->id)
            );
    }

    public function relatedResource()
    {
        return Core::resourceByKey($this->related_resource);
    }

    /**
     * @return \KiryaDev\Admin\Fields\BelongsTo
     */
    public function resolveRelatedField()
    {
        $parentField = $this->resource()->resolveField(HasMany::class, $this->related_resource);

        if ($parentField instanceof MorphMany) {
            return $this
                ->relatedResource()
                ->resolveField(MorphTo::class, $parentField->reverseName);
        }

        if ($parentField instanceof HasMany) {
            return $this
                ->relatedResource()
                ->resolveField(BelongsTo::class, $this->resource);
        }

        throw new \RuntimeException(sprintf('Unknown related type %s.', get_class($parentField)));
    }
}
