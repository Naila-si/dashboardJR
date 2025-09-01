<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TrafficAccident;
use App\Services\GeminiService; // Tambahkan import service Gemini

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /* ---------- 1. Filter ---------- */
        $selectedMonth    = $request->get('bulan', 'all');
        $yearFilter       = $request->get('tahun', 'all'); // Bisa 'all', '2023', atau '2020-2023'
        $selectedProvince = $request->get('provinsi', 'all');
        $selectedYears    = [];

        if ($yearFilter !== 'all') {
            if (str_contains($yearFilter, '-')) {
                [$startYear, $endYear] = explode('-', $yearFilter);
                $selectedYears = range((int)$startYear, (int)$endYear);
            } else {
                $selectedYears = [(int)$yearFilter];
            }
        }

        /* ---------- 2. Dropdown Data ---------- */
        $bulanNames   = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $tahunList    = TrafficAccident::distinct()->orderBy('tahun_laka')->pluck('tahun_laka')->toArray();
        $provinsiList = TrafficAccident::distinct()->orderBy('kota')->pluck('kota');

        /* ---------- 3. Query Data Dasar ---------- */
        $base = TrafficAccident::query();
        if ($selectedMonth !== 'all') {
            $base->where('bulan_laka', $selectedMonth);
        }
        if (!empty($selectedYears)) {
            $base->whereIn('tahun_laka', $selectedYears);
        }
        if ($selectedProvince !== 'all') {
            $base->where('kota', $selectedProvince);
        }

        $data = $base->get();

        /* ---------- 4. Analisis dan Simpan Penanganan ---------- */
        foreach ($data as $d) {
            if (!$d->rencana_penanganan) {
                $d->rencana_penanganan = $this->analisisPenanganan($d);
                $d->save();
            }
        }

        /* ---------- 5. Grafik Bulanan Vertikal ---------- */
        $chartData = TrafficAccident::select(
            'tahun_laka',
            'bulan_laka',
            DB::raw('SUM(santunan) as total_santunan'),
            DB::raw('SUM(korban_md) as md'),
            DB::raw('SUM(korban_ll) as ll')
        )
            ->when($selectedMonth !== 'all', fn($q) => $q->where('bulan_laka', $selectedMonth))
            ->when(!empty($selectedYears), fn($q) => $q->whereIn('tahun_laka', $selectedYears))
            ->when($selectedProvince !== 'all', fn($q) => $q->where('kota', $selectedProvince))
            ->groupBy('tahun_laka', 'bulan_laka')
            ->orderBy('tahun_laka')
            ->orderBy('bulan_laka')
            ->get()
            ->groupBy('tahun_laka')
            ->map(fn($c) => $c->values())
            ->toArray();

        /* ---------- 6. Statistik Ringkas ---------- */
        $stat = [
            'total_kecelakaan' => $data->count(),
            'total_korban'     => $data->sum('korban_total'),
            'total_md'         => $data->sum('korban_md'),
            'total_ll'         => $data->sum('korban_ll'),
            'total_berkas'     => $data->sum('jumlah_berkas'),
            'total_santunan'   => $data->sum('santunan'),
        ];

        /* ---------- 7. Grafik Horizontal ---------- */
        $victimByCity = (clone $base)->select('kota as label', 'tahun_laka as tahun', DB::raw('SUM(korban_total) as total'))
            ->groupBy('kota', 'tahun_laka')->get();

        $victimByDistrict = (clone $base)->select('kecamatan as label', 'tahun_laka as tahun', DB::raw('SUM(korban_total) as total'))
            ->groupBy('kecamatan', 'tahun_laka')->get();

        /* ---------- 8. Grafik Usia & Gender ---------- */
        $victimByAge = (clone $base)->select(
            DB::raw('FLOOR(usia_korban/10)*10 as label'),
            'tahun_laka as tahun',
            DB::raw('SUM(korban_total) as total')
        )
            ->groupBy('label', 'tahun_laka')
            ->get()
            ->map(function ($row) {
                $row->label = $row->label . '‑' . ($row->label + 9);
                return $row;
            });

        $victimByGender = (clone $base)->select(
            'jenis_kelamin as label',
            'tahun_laka as tahun',
            DB::raw('SUM(korban_total) as total')
        )
            ->groupBy('jenis_kelamin', 'tahun_laka')->get();

        /* ---------- 9. Grafik Berdasarkan Data Lain ---------- */
        $accidentTypeData = (clone $base)->select('jenis_tabrakan as label', DB::raw('SUM(korban_total) as total'))
            ->groupBy('jenis_tabrakan')->get();

        $accidentTimeData = (clone $base)->select('waktu_kecelakaan as label', DB::raw('SUM(korban_total) as total'))
            ->groupBy('waktu_kecelakaan')->get();

        $vehicleTypeData = (clone $base)->select('jenis_kendaraan as label', DB::raw('SUM(korban_total) as total'))
            ->groupBy('jenis_kendaraan')->get();

        $occupationData = (clone $base)->select('jenis_pekerjaan as label', DB::raw('SUM(korban_total) as total'))
            ->groupBy('jenis_pekerjaan')->get();

        /* ---------- 10. Return ke View ---------- */
        return view('dashboard.index', compact(
            'data',
            'bulanNames',
            'selectedMonth',
            'yearFilter',
            'selectedProvince',
            'tahunList',
            'provinsiList',
            'chartData',
            'stat',
            'victimByCity',
            'victimByDistrict',
            'victimByAge',
            'victimByGender',
            'accidentTypeData',
            'accidentTimeData',
            'vehicleTypeData',
            'occupationData'
        ));
    }

    /**
     * Analisis otomatis rencana penanganan berdasarkan data kecelakaan
     */
 
     protected function analisisPenanganan()
     {
         $service = new GeminiService();
         
         $accidents = TrafficAccident::whereNull('rencana_penanganan')->get();
     
         foreach ($accidents as $accident) {
             try {
                 $kota = $accident->kota;
                 $kecamatan = $accident->kecamatan;
     
                 $relatedAccidents = TrafficAccident::where('kota', $kota)
                     ->where('kecamatan', $kecamatan)
                     ->get();
     
                 if ($relatedAccidents->isEmpty()) {
                     $rencana = "Belum ada data kecelakaan di {$kota} - {$kecamatan}.";
                 } else {
                     $jenisTabrakan = $relatedAccidents->whereNotNull('jenis_tabrakan')->groupBy('jenis_tabrakan')->sortByDesc(fn($g) => $g->count())->keys()->first() ?? 'tidak diketahui';
                     $jenisKendaraan = $relatedAccidents->whereNotNull('jenis_kendaraan')->groupBy('jenis_kendaraan')->sortByDesc(fn($g) => $g->count())->keys()->first() ?? 'tidak diketahui';
                     $jenisPekerjaan = $relatedAccidents->whereNotNull('jenis_pekerjaan')->groupBy('jenis_pekerjaan')->sortByDesc(fn($g) => $g->count())->keys()->first() ?? 'tidak diketahui';
                     $waktuKecelakaan = $relatedAccidents->whereNotNull('waktu_kecelakaan')->groupBy('waktu_kecelakaan')->sortByDesc(fn($g) => $g->count())->keys()->first() ?? 'tidak diketahui';
     
                     // Safety fallback
                     $jenisTabrakan = $jenisTabrakan ?: 'tidak diketahui';
                     $jenisKendaraan = $jenisKendaraan ?: 'tidak diketahui';
                     $jenisPekerjaan = $jenisPekerjaan ?: 'tidak diketahui';
                     $waktuKecelakaan = $waktuKecelakaan ?: 'tidak diketahui';
     
                     $prompt = "Berdasarkan data berikut: 
     - Lokasi: {$kota}, Kecamatan {$kecamatan}
     - Jenis Tabrakan: {$jenisTabrakan}
     - Jenis Kendaraan: {$jenisKendaraan}
     - Mayoritas Pekerjaan Korban: {$jenisPekerjaan}
     - Waktu Kecelakaan Terbanyak: {$waktuKecelakaan}
     
     Tuliskan rencana penanganan kecelakaan lalu lintas yang profesional dalam bentuk paragraf . 
     Formatkan dalam bahasa Indonesia formal, bukan menggunakan list atau bullet points, dan jangan menggunakan tanda bold (**). 
     Susun dengan alur atau ngk kalimaat paragraph biasa saja bebas jika butuh ada alat buatkan langsung jika dibutuhkan alat atau apapun untuk mengatasi masalahnya dan jangan panjang seklai teks nya to the point aja:
     1. Identifikasi masalah utama di daerah tersebut.
     2. Analisis faktor penyebab kecelakaan.
     3. Susun solusi teknis, edukatif, hukum, dan teknologi secara jelas dan mengalir.
     4. Akhiri dengan kesimpulan pentingnya kolaborasi stakeholder dan monitoring rutin.
     
     Jawaban harus mengalir natural, seperti artikel profesional.";
     
                     $response = $service->ask($prompt);
     
                     if (
                         stripos($response, 'error') !== false ||
                         stripos($response, 'Sorry') !== false ||
                         stripos($response, 'Gagal ambil rencana') !== false ||
                         stripos($response, 'Coba lagi nanti') !== false
                     ) {
                         $rencana = "Gagal mendapatkan rencana penanganan yang valid.";
                     } else {
                         $rencana = $response;
                     }
                 }
     
                 $accident->update([
                     'rencana_penanganan' => $rencana,
                 ]);
     
                 sleep(5);
     
             } catch (\Exception $e) {
                 $accident->update([
                     'rencana_penanganan' => 'Gagal ambil rencana penanganan karena kesalahan sistem.',
                 ]);
     
                 sleep(5);
             }
         }
     }
    }