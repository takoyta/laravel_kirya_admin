<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Requests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use KiryaDev\Admin\AdminCore;
use KiryaDev\Admin\Resource\AbstractResource;

/**
 * @property-read string $id
 * @property-read string $related_resource
 * @property-read ?string $related_id
 */
abstract class AbstractRelatedResourceRequest extends ResourceRequest
{
    abstract public function authorizeAbilityPrefix(): string;

    abstract public function resolveParentField(): object;

    final public function authorize(): bool
    {
        return $this->resource()->authorizedTo(
            $this->authorizeAbilityPrefix() . $this->resource()->modelName(),
            $this->parentObject(),
        );
    }

    final public function resource(): AbstractResource
    {
        return AdminCore::resourceByKey($this->related_resource);
    }

    final public function parentResource(): AbstractResource
    {
        return AdminCore::resourceByKey($this->resource);
    }

    final public function parentObject(): Model
    {
        return $this->parentResource()->findModel($this->id);
    }

    final public function resolveRelation(): EloquentRelation
    {
        $name = $this->resolveParentField()->name;

        return $this->parentObject()->{$name}();
    }
}
