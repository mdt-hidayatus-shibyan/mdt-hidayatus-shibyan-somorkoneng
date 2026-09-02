<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('landing.welcome');
    }
    public function profilMadrasah()
    {
        return view('landing.profil-madrasah');
    }
    public function spmb()
    {
        return view('landing.spmb');
    }
    public function spmbForm()
    {
        return view('landing.form-spmb');
    }
    public function feedback()
    {
        return view('landing.feedback');
    }
}
