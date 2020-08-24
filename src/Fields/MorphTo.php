<?php declare(strict_types=1);

namespace KiryaDev\Admin\Fields;

use Illuminate\Database\Eloquent\Model;
use KiryaDev\Admin\AdminCore;
use KiryaDev\Admin\Resource\AbstractResource;

class MorphTo extends FieldElement
{
    /**
     * @var AbstractResource[]
     */
    protected array $modelToResourceMap;

    public function display(Model $object)
    {
        $relatedObject = $object->{$this->name};
        if (null === $relatedObject) {
            return null;
        }

        $resource = $this->resolveResourceByObject($relatedObject);

        $title = __($resource::label()) . ': ' . $resource->title($relatedObject);

        $value = $resource
            ->makeActionLink('detail', 'view', $title)
            ->displayAsLink()
            ->display($relatedObject);

        if ($this->displayCallback) {
            $value = ($this->displayCallback)($relatedObject, $value);
        }

        return $value;
    }

    protected function resolve(Model $object)
    {
        $value = $object->{$this->name};

        return [$this->resolveResourceByObject($value)::uriKey() => $value->getKey()];
    }

    public function fill(Model $object, $value): void
    {
        /** @var \Illuminate\Database\Eloquent\Relations\MorphTo $relation */
        $relation = $object->{$this->name}();

        // fixme: check value is available id
        $relation->associate(
            $this->getObjectByValue($value)
        );
    }

    public function types(array $types)
    {
        $this->modelToResourceMap = [];

        foreach ($types as $type) {
            // fixme check resource access
            $resource = AdminCore::resourceByKey($type::uriKey());

            $this->modelToResourceMap[$resource->model] = $resource;
        }

        return $this;
    }

    public function getTypes()
    {
        $options = [];

        foreach ($this->modelToResourceMap as $resource) {
            $options[$resource::uriKey()] = [
                'label' => __($resource::label()),
                'placeholder' => $resource::actionLabel('Select'),
                'ajaxSearchUrl' => $resource->makeUrl('api.getObjects'),
            ];
        }

        return $options;
    }

    protected function resolveResourceByObject($object): AbstractResource
    {
        return $this->modelToResourceMap[get_class($object)];
    }

    public function getObjectByValue($value)
    {
        if (is_array($value)) {
            return AdminCore::resourceByKey($key = key($value))->findModel($value[$key]);
        }

        return null;
    }
}
