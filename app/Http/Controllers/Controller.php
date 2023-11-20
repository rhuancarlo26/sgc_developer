<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Monolog\Formatter\LogglyFormatter;
use Whoops\Exception\Formatter;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
