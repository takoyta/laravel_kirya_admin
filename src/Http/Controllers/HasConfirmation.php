<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use KiryaDev\Admin\AdminCore;

trait HasConfirmation
{
    protected function isConfirmed(Request $request): bool
    {
        return $request->isMethod('POST');
    }

    protected function renderConfirm($title, $resource, $panels = [])
    {
        $previousUrl = AdminCore::getPreviousUrl();

        $virtualModel = new class extends Model {
        };

        return view('admin::resource.confirm-action', compact('title', 'resource', 'panels', 'previousUrl', 'virtualModel'));
    }
}
