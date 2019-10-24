<?php

namespace KiryaDev\Admin\Traits;


use KiryaDev\Admin\Fields\ActionsField;
use KiryaDev\Admin\Resource\ActionLink;

trait ResourceActions
{
    /**
     * @return \KiryaDev\Admin\Actions\Actionable[]
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
            ->map(function ($action) use ($route, $params) {
                return $action->link($this, $route)->param('action', $action->uriKey())->param($params);
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
