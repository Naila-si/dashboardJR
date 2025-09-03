<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrafficAccident;

class TrafficAccidentSeeder extends Seeder
{
    public function run()
    {
        $dummyData = [
            [
                'tahun_laka' => 2025,
                'bulan_laka' => 9,
                'tanggal_laka' => '2025-09-01',
                'kota' => 'Pekanbaru',
                'kecamatan' => 'Sukajadi',
                'lokasi' => 'Jl. Sudirman',
                'latitude' => -0.5071,
                'longitude' => 101.4478,
                'korban_total' => 3,
                'korban_md' => 1,
                'ahli_waris_total' => 1,
                'santunan' => 5000000,
                'waktu' => '08:30',
                'action_plan' => 'Evakuasi korban'
            ],
            [
                'tahun_laka' => 2025,
                'bulan_laka' => 9,
                'tanggal_laka' => '2025-09-02',
                'kota' => 'Dumai',
                'kecamatan' => 'Bukit Kapur',
                'lokasi' => 'Jl. Sultan Syarif Kasim',
                'latitude' => 1.667,
                'longitude' => 101.45,
                'korban_total' => 2,
                'korban_md' => 0,
                'ahli_waris_total' => 0,
                'santunan' => 2000000,
                'waktu' => '15:20',
                'action_plan' => 'Pengobatan korban'
            ],
            [
                'tahun_laka' => 2025,
                'bulan_laka' => 8,
                'tanggal_laka' => '2025-08-25',
                'kota' => 'Siak',
                'kecamatan' => 'Tualang',
                'lokasi' => 'Jl. Perawang',
                'latitude' => 0.818,
                'longitude' => 101.804,
                'korban_total' => 1,
                'korban_md' => 1,
                'ahli_waris_total' => 1,
                'santunan' => 3000000,
                'waktu' => '22:10',
                'action_plan' => 'Santunan ahli waris'
            ],
        ];

        foreach ($dummyData as $data) {
            TrafficAccident::create($data);
        }
    }
}
