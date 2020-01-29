<?php

namespace KiryaDev\Admin\Traits;


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
     * @param string $abilitySuffix
     * @param array $params
     * @return \Illuminate\Support\Collection
     */
    public function getActionLinksForHandleMany($abilitySuffix = '', $params = [])
    {
        return $this->getActionLinks('handleMany', $abilitySuffix, $params);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getActionLinksForHandleOneFromDetail()
    {
        return $this->getActionLinks('handleOneFromDetail');
    }

    /**
     * @return ActionsField
     */
    public function getIndexActionsField()
    {
        return ActionsField::with(
            $this
                ->getActionLinks('handleOneFromIndex')
                ->add($this->makeActionLink('detail', 'view')->icon('eye')->displayAsLink())
            );
    }

    /**
     * @param  $method  string
     * @param  $route   string
     * @param  $params  array
     * @return \Illuminate\Support\Collection
     */
    private function getActionLinks($method, $abilitySuffix = '', $params = [])
    {
        return collect($this->actions())
            ->filter(function ($action) use ($method) {
                return method_exists($action, $method);
            })
            ->map(function (Actionable $action) use ($abilitySuffix, $params) {
                $link = $this
                    ->makeActionLink('action', $action->ability($abilitySuffix), __($action->label()))
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
