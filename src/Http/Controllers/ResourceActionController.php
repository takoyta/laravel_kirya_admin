<?php

namespace KiryaDev\Admin\Http\Controllers;


use KiryaDev\Admin\Http\Requests\ActionResourceRequest;

class ResourceActionController
{
    public function handle(ActionResourceRequest $request)
    {
        return $request->resolveAction()->handle($request);
    }
}