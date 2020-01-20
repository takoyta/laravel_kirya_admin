<?php

namespace KiryaDev\Admin\Actions;


use KiryaDev\Admin\Traits;
use KiryaDev\Admin\Resource\ActionLink;

abstract class Actionable
{
    use Traits\HasLabel, Traits\HasUriKey;

    public $requireConfirmation = false;


    /**
     * @param  $link  ActionLink
     * @return ActionLink
     */
    public function configureLink($link)
    {
        return $link;
    }

    }
}
