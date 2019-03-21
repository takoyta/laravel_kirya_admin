<?php

namespace KiryaDev\Admin\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if ($request->expectsJson()) {
            abort(403, 'This very secure page!');
        }

        return route('admin.login');
    }
}
