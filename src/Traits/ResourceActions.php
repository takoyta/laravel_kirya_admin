<?php declare(strict_types=1);

namespace KiryaDev\Admin\Traits;

use Illuminate\Support\Collection;
use KiryaDev\Admin\Actions\Actionable;
use KiryaDev\Admin\Fields\ActionsField;
use KiryaDev\Admin\Resource\ActionLink;

trait ResourceActions
{
    /**
     * @return Actionable[]
     */
    public function actions(): array
    {
        return [];
    }

    public function getActionLinksForHandleMany(string $abilitySuffix = '', $params = []): Collection
    {
        return $this->getActionLinks('handleMany', $abilitySuffix, $params);
    }

    public function getActionLinksForHandleOneFromDetail(): Collection
    {
        return $this->getActionLinks('handleOneFromDetail');
    }

    public function getIndexActionsField(): ActionsField
    {
        return ActionsField::with(
            $this
                ->getActionLinks('handleOneFromIndex')
                ->add($this->makeActionLink('detail', 'view')->icon('eye')->displayAsLink())
        );
    }

    private function getActionLinks(string $method, string $abilitySuffix = '', $params = []): Collection
    {
        return collect($this->actions())
            ->filter(static function ($action) use ($method) {
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

    public function makeActionLink(string $route, string $ability = null, string $title = null): ActionLink
    {
        return new ActionLink($this, $route, $ability, $title);
    }
}
