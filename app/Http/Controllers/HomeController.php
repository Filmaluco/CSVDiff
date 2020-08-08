<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function Index()
    {
        return view("welcome");
    }

    public function Diff(Request $request)
    {
        $validatedData = $request->validate([
            'file1' => 'required|file|mimes:csv,txt',
            'file2' => 'required|file|mimes:csv,txt',
        ]);

        echo 'Uploaded';
    }
}
