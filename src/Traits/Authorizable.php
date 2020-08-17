<?php declare(strict_types=1);

namespace KiryaDev\Admin\Traits;

use Illuminate\Support\Facades\Gate;

trait Authorizable
{
    public function authorizedToViewAny(): bool
    {
        return $this->authorizedTo('viewAny');
    }

    public function authorizedTo($ability, $object = null): bool
    {
        // Object can be null or instance of another resource

        // Get the raw result from the authorization callback.
        // true - allow
        // false - deny
        // null - no exits method or policy
        $result = Gate::raw($ability, [$object ?? $this->model]);

        return is_bool($result) ? $result : true;
    }
}
