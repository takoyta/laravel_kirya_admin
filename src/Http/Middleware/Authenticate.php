<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param Request $request
     * @return string
     */
    protected function redirectTo($request): string
    {
        if ($request->expectsJson()) {
            abort(403, 'This very secure page!');
        }

        return route('admin.login');
    }
}
