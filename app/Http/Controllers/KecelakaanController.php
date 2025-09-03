<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kecelakaan;

class KecelakaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Kecelakaan::all();
        return view('kecelakaan.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kecelakaan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'lokasi' => 'required|string',
            'tanggal_waktu' => 'required|date',
            'laporan' => 'required|string',
            'Cidera' => 'required|string',
            'sifat_laka' => 'required|string',
            'status_lp' => 'required|string',
        ]);

        Kecelakaan::create($request->all());

        return redirect()->route('kecelakaan.index')->with('success', 'Data berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Kecelakaan::findOrFail($id);
        return view('kecelakaan.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kecelakaan $kecelakaan)
    {
        //$kecelakaan otomatis terisi dari database sesuai id
        return view('kecelakaan.edit', compact('kecelakaan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kecelakaan $kecelakaan)
    {
        $kecelakaan->update($request->all());
        return redirect()->route('kecelakaan.index')->with('success', 'Data berhasil diupdate!');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx|max:2048'
        ]);

        // simpan file
        $request->file('file')->store('uploads');

        return redirect()->route('kecelakaan.index')->with('success', 'File berhasil diupload!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Kecelakaan::findOrFail($id);
        $data->delete();

        return redirect()->route('kecelakaan.index')->with('success', 'Data berhasil dihapus!');
    }
}
