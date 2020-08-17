<?php declare(strict_types=1);

namespace KiryaDev\Admin;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\Facades\Route;

use KiryaDev\Admin\Resource\Resource;

final class Core
{
    /**
     * @var \KiryaDev\Admin\Resource\Resource[]
     */
    private static $resources = [];


    /**
     * Bootstrap resources.
     *
     * @param $directory
     */
    public static function bootResourcesIn($directory)
    {
        $namespace = app()->getNamespace();

        foreach (Finder::create()->in($directory)->files() as $resource) {
            $resource = $namespace.str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($resource->getPathname(), app_path().DIRECTORY_SEPARATOR)
                );

            if (
                is_subclass_of($resource, Resource::class) &&
                ! (new \ReflectionClass($resource))->isAbstract()
            ) {
                /** @var \KiryaDev\Admin\Resource\Resource $resource */
                $resource = new $resource;

                self::$resources[$resource::uriKey()] = $resource;
            }
        }
    }

    /**
     * @param  string  $key
     * @return \KiryaDev\Admin\Resource\Resource
     */
    public static function resourceByKey($key)
    {
        return tap(Core::$resources[$key] ?? false, function($r) use ($key) {
            abort_unless($r, 404, "Resource {$key} not found.");
        });
    }

    private static function availableResources()
    {
        return Arr::where(Core::$resources, function ($resource) {
            /** @var \KiryaDev\Admin\Resource\Resource $resource */

            return $resource->authorizedToViewAny();
        });
    }

    public static function menu()
    {
        return collect(Core::availableResources())
            ->map(function ($resource, $uriKey) {
                /** @var \KiryaDev\Admin\Resource\Resource $resource */
                return [
                    'label'  => $resource::pluralLabel(),
                    'uriKey' => $uriKey,
                    'group'  => $resource->group,
                    'order'  => $resource->orderInSidebar,
                ];
            })
            ->where('order', '!==', false)
            ->sortBy('order')
            ->groupBy('group');
    }

    /**
     * Resolve action class.
     *
     * @param $directory
     */
    public static function resolveActionClassName($action)
    {
        $namespace = app()->getNamespace();

        return $namespace.'Admin\\Actions\\'.Str::studly($action);
    }

    public static function getPreviousUrl()
    {
        return request()->post('_previous_url') // Get url from form
            ?? request()->old('_previous_url')  // Get old value url after fail validation
            ?? url()->previous();
    }

    public static function redirectToPrevious()
    {
        return redirect(Core::getPreviousUrl());
    }
}
