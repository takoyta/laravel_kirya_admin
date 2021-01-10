<?php declare(strict_types=1);

namespace KiryaDev\Admin;

use Illuminate\Support\Str;
use KiryaDev\Admin\Resource\AbstractResource;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

final class AdminCore
{
    /** @var AbstractResource[] */
    private static array $resources = [];

    public static function bootResourcesIn(string $directory): void
    {
        $namespace = app()->getNamespace();

        foreach (Finder::create()->in($directory)->files() as $resource) {
            $resource = $namespace . str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($resource->getPathname(), app_path() . DIRECTORY_SEPARATOR)
                );

            if (
                is_subclass_of($resource, AbstractResource::class) &&
                !(new ReflectionClass($resource))->isAbstract()
            ) {
                /** @var AbstractResource $resource */
                $resource = new $resource;

                self::$resources[$resource::uriKey()] = $resource;
            }
        }
    }

    public static function resourceByKey(string $key): AbstractResource
    {
        if (isset(self::$resources[$key])) {
            return self::$resources[$key];
        }

        abort(404, "Resource with key '{$key}' not found.");
    }

    public function getMenu(): array
    {
        return collect(self::$resources)
            ->filter(static function (AbstractResource $resource) {
                return $resource->showInSidebar && $resource->authorizedToViewAny();
            })
            ->map(static function (AbstractResource $resource, $uriKey) {
                return [
                    'label' => $resource::pluralLabel(),
                    'uriKey' => $uriKey,
                    'group' => $resource->group,
                    'order' => $resource->orderInSidebar,
                ];
            })
            ->sortBy('order')
            ->groupBy('group')
            ->toArray();
    }

    public static function resolveActionClassName($action): string
    {
        $namespace = app()->getNamespace();

        return $namespace . 'Admin\\Actions\\' . Str::studly($action);
    }

    public static function getPreviousUrl()
    {
        return request()->post('_previous_url') // Get url from form
            ?? request()->old('_previous_url')  // Get old value url after fail validation
            ?? url()->previous();
    }

    public static function redirectToPrevious()
    {
        return redirect(self::getPreviousUrl());
    }
}
