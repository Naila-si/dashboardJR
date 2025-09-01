<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TrafficAccidentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $jabarCities = [
            ['kota' => 'Bandung', 'kecamatan' => 'Coblong', 'lat' => -6.8903, 'lng' => 107.6107],
            ['kota' => 'Bogor', 'kecamatan' => 'Tanah Sareal', 'lat' => -6.5803, 'lng' => 106.8060],
            ['kota' => 'Depok', 'kecamatan' => 'Beji', 'lat' => -6.3909, 'lng' => 106.8230],
            ['kota' => 'Cimahi', 'kecamatan' => 'Cimahi Tengah', 'lat' => -6.8722, 'lng' => 107.5411],
            ['kota' => 'Cirebon', 'kecamatan' => 'Kejaksan', 'lat' => -6.7063, 'lng' => 108.5523],
            ['kota' => 'Bekasi', 'kecamatan' => 'Bekasi Selatan', 'lat' => -6.2574, 'lng' => 106.9804],
            ['kota' => 'Sukabumi', 'kecamatan' => 'Citamiang', 'lat' => -6.9182, 'lng' => 106.9339],
            ['kota' => 'Tasikmalaya', 'kecamatan' => 'Tawang', 'lat' => -7.3278, 'lng' => 108.2208],
            ['kota' => 'Banjar', 'kecamatan' => 'Purwaharja', 'lat' => -7.3778, 'lng' => 108.5347],
        ];

        $jenisTabrakan = ['Tabrak depan', 'Tabrak belakang', 'Tabrak samping', 'Hindaran'];
        $jenisKendaraan = ['Mobil', 'Motor', 'Angkutan Umum', 'Kereta Api', 'Pesawat'];
        $jenisPekerjaan = ['Pelajar', 'Mahasiswa', 'Karyawan Swasta', 'PNS', 'Petani', 'Pedagang', 'Buruh'];

        for ($i = 0; $i < 1000; $i++) {
            $randomCity = $faker->randomElement($jabarCities);

            $korbanLl = $faker->numberBetween(0, 10);
            $korbanMd = $faker->numberBetween(0, 5);
            $totalKorban = $korbanLl + $korbanMd;

            $tanggal = $faker->dateTimeBetween('2020-01-01', '2025-12-31');
            $waktu = $tanggal->format('H:i:s');

            DB::table('traffic_accidents')->insert([
                'provinsi' => 'Jawa Barat',
                'kota' => $randomCity['kota'],
                'kecamatan' => $randomCity['kecamatan'],
                'korban_md' => $korbanMd,
                'korban_ll' => $korbanLl,
                'korban_total' => $totalKorban,
                'santunan' => $totalKorban * 5000000,
                'bulan_laka' => $tanggal->format('m'),
                'tahun_laka' => $tanggal->format('Y'),
                'rencana_penanganan' => null,
                'latitude' => $randomCity['lat'],
                'longitude' => $randomCity['lng'],
                'jumlah_berkas' => (string) $faker->numberBetween(1, 5),
                'jenis_kelamin' => $faker->randomElement(['Laki-laki', 'Perempuan']),
                'usia_korban' => (string) $faker->numberBetween(10, 75),
                'jenis_tabrakan' => $faker->randomElement($jenisTabrakan),
                'waktu_kecelakaan' => $waktu,
                'jenis_kendaraan' => $faker->randomElement($jenisKendaraan),
                'jenis_pekerjaan' => $faker->randomElement($jenisPekerjaan),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
