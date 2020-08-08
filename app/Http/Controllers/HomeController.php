<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function Index()
    {
        return view("welcome");
    }

    public function Diff()
    {
        echo 'Uploaded';
    }
}
