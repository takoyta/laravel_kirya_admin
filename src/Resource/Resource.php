<?php
namespace KiryaDev\Admin\Resource;


use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

use KiryaDev\Admin\Core;
use KiryaDev\Admin\Traits;
use KiryaDev\Admin\Filters\FilterProvider;

abstract class Resource
{
    use Traits\HasLabel,
        Traits\HasUriKey,
        Traits\HasFields,
        Traits\Authorizable,
        Traits\ResourceActions,
        Traits\HasConfirmationMessages;

    public $model;

    public $group = 'Other';

    public $title = 'id';

    public $search;

    public $perPage = 15;

    public $orderInSidebar = 100; // If false - resource hiding from sidebar


    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        return $this->model::query();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function indexQuery()
    {
        return $this->query();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function searchQuery()
    {
        return $this->query();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function newModel()
    {
        $className = $this->model;

        return new $className;
    }

    /**
     * @param  string  $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findModel($id)
    {
        return $this->query()->findOrFail($id);
    }

    /**
     * @return \KiryaDev\Admin\Filters\Filterable[]
     */
    public function filters()
    {
        return [];
    }

    /**
     * Get title of object via title field.
     *
     * @param  Model|string  $object
     * @return string
     */
    public function title($object)
    {
        if (! $object) return null;

        if (! $object instanceof Model) $object = $this->findModel($object);

        return $object->{$this->title};
    }

    /**
     * Generate title for action.
     *
     * @return string
     */
    public static function actionLabel($action)
    {
        return __(':action ' . static::label(), ['action' => __($action)]);
    }

    /**
     * Return class base name of model.
     *
     * @return string
     */
    public function modelName()
    {
        return class_basename($this->model);
    }

    /**
     * Retrive instance of current resource.
     *
     * @return self
     */
    public static function instance()
    {
        return Core::resourceByKey(static::uriKey());
    }

    /**
     * Generate url to action.
     *
     * @return string
     */
    public static function makeUrl($route, $params = [])
    {
        $params['resource'] = $params['resource'] ?? static::uriKey();

        return route('admin.' . $route, $params, true);
    }

    /**
     * Make new filter provider
     *
     * @param  string  $prefix
     * @return FilterProvider
     */
    public function newFilterProvider($prefix = '')
    {
        return new FilterProvider($this, $prefix);
    }
}
