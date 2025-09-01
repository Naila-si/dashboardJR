<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\TrafficAccidentImport;
use Maatwebsite\Excel\Facades\Excel;

class TrafficAccidentImportController extends Controller
{
    public function showImportForm()
    {
        return view('import'); // return view form upload
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new TrafficAccidentImport, $request->file('file'));

        return back()->with('success', 'Data berhasil diimpor!');
    }
}
