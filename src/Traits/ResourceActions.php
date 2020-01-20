<?php

namespace KiryaDev\Admin\Traits;


use Illuminate\Support\Str;

use KiryaDev\Admin\Actions\Actionable;
use KiryaDev\Admin\Fields\ActionsField;
use KiryaDev\Admin\Resource\ActionLink;

trait ResourceActions
{
    /**
     * @return Actionable[]
     */
    public function actions()
    {
        return [];
    }

    /**
     * @param string $route
     * @param array $params
     * @return \Illuminate\Support\Collection
     */
    public function getActionLinksForHandleMany($route, $params = [])
    {
        return $this->getActionLinks('handleMany', $route, $params);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getActionLinksForHandleOneFromDetail()
    {
        return $this->getActionLinks('handleOneFromDetail', 'action');
    }

    /**
     * @return ActionsField
     */
    public function getIndexActionsField()
    {
        return ActionsField::with(
            $this
                ->getActionLinks('handleOneFromIndex', 'action')
                ->add($this->makeActionLink('detail', 'view')->icon('eye')->displayAsLink())
            );
    }

    /**
     * @param  $method  string
     * @param  $route   string
     * @param  $params  array
     * @return \Illuminate\Support\Collection
     */
    private function getActionLinks($method, $route, $params = [])
    {
        return collect($this->actions())
            ->filter(function ($action) use ($method) {
                return method_exists($action, $method);
            })
            ->map(function (Actionable $action) use ($route, $params) {
                $link = $this
                    ->makeActionLink($route,
                        Str::camel(class_basename($action)),
                        __($action->label())
                    )
                    ->param('action', $action->uriKey())
                    ->param($params);

                return $action->configureLink($link);
            });
    }

    /**
     * @param  string  $route
     * @param  string  $ability
     * @param  string  $title
     * @return \KiryaDev\Admin\Resource\ActionLink
     */
    public function makeActionLink($route, $ability = null, $title = null)
    {
        return new ActionLink($this, $route, $ability, $title);
    }
}
