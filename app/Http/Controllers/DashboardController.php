<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrafficAccident;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Filter input
        $selectedMonth = $request->get('bulan', 'all');
        $yearFilter = $request->get('tahun', 'all');
        $selectedProvince = $request->get('provinsi', 'all');
        $selectedYears = [];

        if ($yearFilter !== 'all') {
            if (str_contains($yearFilter, '-')) {
                [$startYear, $endYear] = explode('-', $yearFilter);
                $selectedYears = range((int)$startYear, (int)$endYear);
            } else {
                $selectedYears = [(int)$yearFilter];
            }
        }

        // 2. Dropdown data
        $bulanNames = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
        ];
        $tahunList = TrafficAccident::distinct()->orderBy('tahun_laka')->pluck('tahun_laka')->toArray();
        $provinsiList = TrafficAccident::distinct()->orderBy('kota')->pluck('kota')->toArray();

        // 3. Query dasar
        $baseQuery = TrafficAccident::query();
        if ($selectedMonth !== 'all') $baseQuery->where('bulan_laka', $selectedMonth);
        if (!empty($selectedYears)) $baseQuery->whereIn('tahun_laka', $selectedYears);
        if ($selectedProvince !== 'all') $baseQuery->where('kota', $selectedProvince);

        $data = $baseQuery->get();

        // 4. Statistik ringkas
        $stats = [
            ['label' => 'Jumlah Kendaraan Laka', 'value' => $data->count(), 'icon' => 'fa-car-crash'],
            ['label' => 'Jumlah Korban', 'value' => $data->sum('korban_total'), 'icon' => 'fa-users'],
            ['label' => 'Jumlah Ahli Waris', 'value' => $data->sum('ahli_waris_total'), 'icon' => 'fa-user-friends'],
            ['label' => 'Rp. Total Santunan', 'value' => $data->sum('santunan'), 'icon' => 'fa-hand-holding-usd'],
        ];

        // 🔹 Format label bulanan pakai YYYY-MM
        $monthKey = fn($v) => $v->tahun_laka . '-' . str_pad($v->bulan_laka, 2, '0', STR_PAD_LEFT);

        // 5. Chart data (bulanan & tahunan)
        $chartSantunan = [
            'labels' => $data->map($monthKey)->unique()->values()->toArray(),
            'data' => $data->groupBy($monthKey)->map(fn($v) => $v->sum('santunan'))->values()->toArray()
        ];

        $chartMeninggal = [
            'labels' => $data->map($monthKey)->unique()->values()->toArray(),
            'data' => $data->groupBy($monthKey)->map(fn($v) => $v->sum('korban_md'))->values()->toArray()
        ];

        $chartKorban = [
            'labels' => $data->map($monthKey)->unique()->values()->toArray(),
            'data' => $data->groupBy($monthKey)->map(fn($v) => $v->sum('korban_total'))->values()->toArray()
        ];

        // 5b. Chart perbandingan tahunan
        $yearComparison = [
            'labels' => $data->pluck('tahun_laka')->unique()->values()->toArray(),
            'data'   => $data->groupBy('tahun_laka')->map(fn($v) => $v->sum('korban_total'))->values()->toArray(),
        ];

        // Chart korban per kota
        $chartKorbanPerkot = [
            'labels' => $data->pluck('kota')->unique()->values()->toArray(),
            'data' => $data->groupBy('kota')->map(fn($v) => $v->sum('korban_total'))->values()->toArray(),
        ];

        // Chart korban per kecamatan
        $chartKorbanPerkecmtn = [
            'labels' => $data->pluck('kecamatan')->unique()->values()->toArray(),
            'data' => $data->groupBy('kecamatan')->map(fn($v) => $v->sum('korban_total'))->values()->toArray(),
        ];

        // Chart tahunan (contoh: santunan per tahun)
        $chartLainnya = [
            'labels' => $data->pluck('tahun_laka')->unique()->values()->toArray(),
            'data' => $data->groupBy('tahun_laka')->map(fn($v) => $v->sum('santunan'))->values()->toArray(),
        ];

        // 6. Data untuk map
        $kecelakaan = $data->map(fn($item) => [
            'name' => $item->lokasi ?? $item->jalan ?? 'Unknown',
            'lat' => $item->latitude ?? -0.507,
            'lng' => $item->longitude ?? 101.447,
            'korban' => $item->korban_total,
            'jam' => $item->waktu ?? '-',
            'tanggal' => $item->tanggal_laka ?? ($item->tahun_laka . '-' . str_pad($item->bulan_laka, 2, '0', STR_PAD_LEFT) . '-01'),
            'action' => $item->action_plan ?? '-'
        ]);

        // 7. Return view
        return view('dashboard', compact(
            'data',
            'bulanNames',
            'selectedMonth',
            'yearFilter',
            'selectedProvince',
            'tahunList',
            'provinsiList',
            'stats',
            'chartSantunan',
            'chartMeninggal',
            'chartKorban',
            'kecelakaan',
            'yearComparison',
            'chartKorbanPerkot',
            'chartKorbanPerkecmtn',
            'chartLainnya'
        ));
    }
}
