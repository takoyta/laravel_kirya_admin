<?php

namespace KiryaDev\Admin\Http\Controllers;


trait HasConfirmation
{
    protected function isConfirmed($request)
    {
        return $request->isMethod('POST');
    }

    protected function renderConfirm($title, $backUrl = null)
    {
        $backUrl = $backUrl ?? url()->previous();

        return view('admin::resource.confirm-action', compact('title', 'backUrl'));
    }
}
