<?php declare(strict_types=1);

namespace KiryaDev\Admin\Http\Controllers;


class HomeController
{
    public function index()
    {
        return view('admin::home.index');
    }

    public function fallback()
    {
        return view('admin::home.fallback');
    }
}
