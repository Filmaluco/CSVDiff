<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\CSVDiff\CSVDiff;
use Exception;

class HomeController extends Controller
{
    public function Index()
    {
        return view("welcome");
    }

    public function Diff(Request $request)
    {
        $request->validate([
            'file1' => 'required|file|mimes:csv,txt',
            'file2' => 'required|file|mimes:csv,txt',
        ]);

        try {
            $fileDiff = CSVDiff::getDiffFromFiles(
                $request->file('file1')->getRealPath(), 
                $request->file('file2')->getRealPath()
            );
        } catch (Exception $e) {
            echo $e->getMessage(), " - Please try again later\n";
            die();
        }

        return CSVDiff::getHtmlFromDiff($fileDiff);
    }
}
