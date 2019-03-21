<?php
namespace KiryaDev\Admin\Resource;

use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\AuthorizationException;

trait Authorizable
{
    public function authorizedToViewAny()
    {
        return $this->authorizedTo('viewAny');
    }

    public function authorizedTo($ability, $object = null)
    {
        $policy = Gate::getPolicyFor($this->model);

        return ($policy && method_exists($policy, $ability))
            ? Gate::check($ability, $object ?? $this->model)
            : true;
    }
}