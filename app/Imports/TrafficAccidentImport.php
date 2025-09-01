<?php

namespace App\Imports;

use App\Models\TrafficAccident;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class TrafficAccidentImport implements ToModel, WithHeadingRow, WithUpserts
{
    /**
     * Map setiap baris Excel ke model TrafficAccident.
     */
    public function model(array $row)
    {
        // Ambil alamat lengkap lebih detail
        $fullAddress = sprintf(
            "%s, %s, %s, %s, %s, Indonesia",
            $row['nama_jalan'] ?? '',
            $row['kelurahan_desa'] ?? '',
            $row['kecamatan'] ?? '',
            $row['kota'] ?? '',
            $row['provinsi'] ?? ''
        );

        // Cari koordinat berdasarkan alamat lengkap
        $location = $this->getCoordinates($fullAddress);

        // Kalau tidak ketemu, fallback ke kecamatan/kota saja
        if (!$location) {
            $fallbackAddress = sprintf(
                "%s, %s, %s, Indonesia",
                $row['kecamatan'] ?? '',
                $row['kota'] ?? '',
                $row['provinsi'] ?? ''
            );
            $location = $this->getCoordinates($fallbackAddress);
        }

        // Return model baru
        return new TrafficAccident([
            'provinsi'           => $row['provinsi'],
            'kota'               => $row['kota'],
            'kecamatan'          => $row['kecamatan'],
            'kelurahan_desa'     => $row['kelurahan_desa'],
            'nama_jalan'         => $row['nama_jalan'],
            'korban_md'          => $row['korban_md'],
            'korban_ll'          => $row['korban_ll'],
            'korban_total'       => $row['korban_total'],
            'santunan'           => $row['santunan'],
            'bulan_laka'         => $row['bulan_laka'],
            'tahun_laka'         => $row['tahun_laka'],
            'rencana_penanganan' => $row['rencana_penanganan'],
            'jumlah_berkas'      => $row['jumlah_berkas'],
            'jenis_kelamin'      => $row['jenis_kelamin'],
            'usia_korban'        => $row['usia_korban'],
            'jenis_tabrakan'     => $row['jenis_tabrakan'],
            'waktu_kecelakaan'   => $row['waktu_kecelakaan'],
            'jenis_kendaraan'    => $row['jenis_kendaraan'],
            'jenis_pekerjaan'    => $row['jenis_pekerjaan'],
            'latitude'           => $location['lat'] ?? null,
            'longitude'          => $location['lon'] ?? null,
        ]);
    }

    /**
     * Fungsi untuk mencari koordinat dari alamat.
     */
    private function getCoordinates($address)
    {
        if (empty($address)) {
            return null;
        }

        $response = Http::withHeaders([
            'User-Agent' => 'LaravelApp/1.0'
        ])->get('https://nominatim.openstreetmap.org/search', [
            'q'      => $address,
            'format' => 'json',
            'limit'  => 1,
        ]);

        if ($response->successful() && isset($response[0])) {
            return [
                'lat' => $response[0]['lat'],
                'lon' => $response[0]['lon'],
            ];
        }

        return null;
    }

    /**
     * Kolom-kolom unik untuk upsert.
     */
    public function uniqueBy()
    {
        return ['provinsi', 'kota', 'kecamatan', 'kelurahan_desa', 'nama_jalan', 'bulan_laka', 'tahun_laka'];
    }
}
