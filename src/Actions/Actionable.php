<?php declare(strict_types=1);

namespace KiryaDev\Admin\Actions;

use Illuminate\Support\Str;
use KiryaDev\Admin\AdminCore;
use KiryaDev\Admin\Resource\ActionLink;
use KiryaDev\Admin\Traits;

abstract class Actionable
{
    use Traits\HasLabel,
        Traits\HasFields,
        Traits\HasUriKey;

    public bool $requireConfirmation = false;

    public function __construct()
    {
        if (\count($this->getFieldsOnce()) > 0) {
            $this->requireConfirmation = true;
        }
    }

    /**
     * @param $link ActionLink
     * @return ActionLink
     */
    public function configureLink($link)
    {
        return $link;
    }

    public function ability($suffix = ''): string
    {
        return Str::camel(class_basename(static::class)) . $suffix . 'Action';
    }

    public function successResponse($message)
    {
        return AdminCore::redirectToPrevious()->with('success', $message);
    }

    public function errorResponse($message)
    {
        return AdminCore::redirectToPrevious()->with('error', $message);
    }
}
