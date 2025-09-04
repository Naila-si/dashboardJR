<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AhliWaris;

class AhliWarisController extends Controller
{
    // Tampilkan semua data
    public function index()
    {
        $dataSantunan = AhliWaris::latest()->get();

        $chartData = [
            'santunan' => [10000, 20000, 15000, 30000, 25000, 18000],
            'korbanMeninggal' => [2, 4, 1, 5, 3, 2],
            'korbanLuka' => [5, 7, 6, 8, 4, 3],
        ];

        return view('ahliwaris.index', compact('dataSantunan', 'chartData'));
    }

    // Form tambah data
    public function create()
    {
        return view('ahliwaris.create');
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'no_berkas' => 'required|string|max:255',
            'cedera' => 'required|string',
            'nama_pemohon' => 'required|string|max:100',
            'alamat' => 'required|string',
            'penyelesaian' => 'nullable|string',
            'status' => 'required|string',
        ]);

        AhliWaris::create($request->all());

        return redirect()->route('ahliwaris.index')->with('success', 'Data berhasil ditambahkan!');
    }

    // Form edit data
    public function edit($id)
    {
        // cari data sesuai id (dummy)
        $item = [
            'id' => $id,
            'tanggal' => '12.09.2019 - 12:53 PM',
            'no_berkas' => 'contoh data',
            'cedera' => 'LL',
            'nama_pemohon' => 'Shasa',
            'alamat' => 'Kec. Tampan, Kota Pekanbaru',
            'penyelesaian' => 'Dilaksanakan',
            'status' => 'Rejected',
        ];

        return view('ahliwaris.edit', compact('item'));
    }

    // Update data
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'no_berkas' => 'required|string|max:255',
            'cedera' => 'required|string',
            'nama_pemohon' => 'required|string|max:100',
            'alamat' => 'required|string',
            'penyelesaian' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $item = AhliWaris::findOrFail($id);
        $item->update($request->all());

        return redirect()->route('ahliwaris.index')->with('success', 'Data berhasil diperbarui!');
    }

    // Hapus data
    public function destroy($id)
    {
        $item = AhliWaris::findOrFail($id);
        $item->delete();

        return redirect()->route('ahliwaris.index')->with('success', 'Data berhasil dihapus!');
    }
}
