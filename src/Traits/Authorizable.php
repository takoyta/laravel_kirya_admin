<?php
namespace KiryaDev\Admin\Traits;


use Illuminate\Support\Facades\Gate;

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
