<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BackendController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:Dashboard view'])->only(['index']);
    }
    
    public function index()
    {
        if(Auth::user()->hasRole('Driver'))
        {
            return view('driver.layouts.home');
        }
        return view('backend.layouts.home');
    }
}
