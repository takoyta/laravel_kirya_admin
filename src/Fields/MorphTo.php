<?php

namespace KiryaDev\Admin\Fields;


use KiryaDev\Admin\Core;

class MorphTo extends FieldElement
{
    /**
     * @var \KiryaDev\Admin\Resource\Resource[]
     */
    protected $modelToResourceMap;


    protected function boot()
    {
        $this->displayUsing(function ($value) {
            $resource = $this->resolveResourceByObject($value);

            $title = __($resource::label()) . ': ' . $resource->title($value);

            return $resource
                ->makeActionLink('detail', 'view', $title)
                ->displayAsLink()
                ->display($value);
        });

        $this->resolveUsing(function ($value) {
            return [$this->resolveResourceByObject($value)::uriKey() => $value->getKey()];
        });

        $this->fillUsing(function ($oject, $value) {
            // fixme: check value is available id

            /** @var \Illuminate\Database\Eloquent\Relations\MorphTo $relation */
            $relation = $oject->{$this->name}();

            $relation->associate(
                $this->getObjectByValue($value)
            );
        });
    }

    public function types(array $types)
    {
        $this->modelToResourceMap = [];

        foreach ($types as $type) {
            // fixme check resource access
            $resource = Core::resourceByKey($type::uriKey());

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

    protected function resolveResourceByObject($object)
    {
        return $this->modelToResourceMap[get_class($object)];
    }

    public function getObjectByValue($value)
    {
        if (is_array($value)) {
            return Core::resourceByKey($key = key($value))->findModel($value[$key]);
        }

        return null;
    }
}