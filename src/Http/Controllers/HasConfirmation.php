<?php

namespace KiryaDev\Admin\Http\Controllers;


use KiryaDev\Admin\Core;

trait HasConfirmation
{
    protected function isConfirmed($request)
    {
        return $request->isMethod('POST');
    }

    protected function renderConfirm($title, $resource, $panels = [])
    {
        $previousUrl = Core::getPreviousUrl();

        return view('admin::resource.confirm-action', compact('title', 'resource', 'panels', 'previousUrl'));
    }
}
