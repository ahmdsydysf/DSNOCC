<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    //
}

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.home');
    }
}
