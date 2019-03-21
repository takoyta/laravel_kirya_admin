<?php

use \KiryaDev\Admin\Http\Middleware\Authenticate;


return [
    'prefix' => 'admin',


    'auth' => [
        'middleware' => ['web', ]
    ],


    'middleware' => ['web', Authenticate::class, ],

];
