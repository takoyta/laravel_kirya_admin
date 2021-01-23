<?php declare(strict_types=1);

namespace KiryaDev\Admin\Resource;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use KiryaDev\Admin\AdminCore;
use KiryaDev\Admin\Filters\Filterable;
use KiryaDev\Admin\Filters\FilterProvider;
use KiryaDev\Admin\Traits;

abstract class AbstractResource
{
    use Traits\HasLabel,
        Traits\HasUriKey,
        Traits\HasFields,
        Traits\Authorizable,
        Traits\ResourceActions,
        Traits\HasConfirmationMessages;

    public string $model;

    public string $group = 'Other';

    public string $title = 'id';

    public array $search;

    public int $perPage = 15;

    public bool $showInSidebar = true;

    public int $orderInSidebar = 100;

    /**
     * @return Builder
     */
    public function query()
    {
        return $this->model::query();
    }

    /**
     * @return Builder
     */
    public function indexQuery()
    {
        return $this->query();
    }

    /**
     * @return Builder
     */
    public function searchQuery()
    {
        return $this->query();
    }

    /**
     * @return Model
     */
    public function newModel(): Model
    {
        $className = $this->model;

        return new $className;
    }

    /**
     * @param string|int $id
     * @return Model
     */
    public function findModel($id): Model
    {
        return $this->query()->findOrFail($id);
    }

    /**
     * @return Filterable[]
     */
    public function filters(): array
    {
        return [];
    }

    /**
     * Get title of object.
     *
     * @param Model|string|int $object
     * @return string
     */
    public function title($object): string
    {
        if (!$object) {
            return '';
        }

        if (!$object instanceof Model) {
            $object = $this->findModel($object);
        }

        return (string)$object->{$this->title};
    }

    /**
     * Get title of object with resource name.
     *
     * @param Model|string|int $object
     * @return string
     */
    public function labeledTitle($object): string
    {
        return __(static::label()) . ' ' . $this->title($object);
    }

    /**
     * Generate title for action.
     *
     * @param string $action
     * @return string
     */
    public static function actionLabel(string $action): string
    {
        return __(':action ' . static::label(), ['action' => __($action)]);
    }

    /**
     * Return class base name of model.
     *
     * @return string
     */
    public function modelName(): string
    {
        return class_basename($this->model);
    }

    /**
     * @return static
     */
    public static function instance(): self
    {
        return AdminCore::resourceByKey(static::uriKey());
    }

    /**
     * Generate url to action.
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public static function makeUrl(string $route, array $params = []): string
    {
        $params['resource'] = $params['resource'] ?? static::uriKey();

        return route('admin.' . $route, $params, true);
    }

    /**
     * @param  string  $prefix
     * @return FilterProvider
     */
    public function newFilterProvider($prefix = ''): FilterProvider
    {
        return new FilterProvider($this, $prefix);
    }
}
