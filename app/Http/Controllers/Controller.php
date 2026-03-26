<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    protected function getPerPage(Request $request, int $default = 15, int $max = 100): int
    {
        return min($request->integer('per_page', $default), $max);
    }
}
