@extends('sidebar')

@section('title', 'Dashboard')

@section('content')
<!-- Header -->
<header class="flex justify-between items-center mb-6 shadow rounded-md p-4 bg-red-800 text-amber-100">
    <h1 class="text-2xl text-white font-bold">Dashboard</h1>
    <div class="flex items-center space-x-4">
        <!-- Search -->
        <div class="relative">
            <input type="text" placeholder="Search..."
                class="w-64 pl-10 pr-4 py-2 rounded-md border border-white text-white placeholder-white bg-transparent focus:outline-none focus:ring-2 focus:ring-white transition shadow-sm">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-white"></i>
        </div>

        <!-- Profile Dropdown -->
        <div class="relative" id="profileButton">
            <button onclick="toggleDropdown()" class="flex items-center space-x-2 hover:bg-opacity-20 rounded-md p-1 transition">
                <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-gray-300">
                    @if(Auth::user()->foto_profile && file_exists(public_path('profile_photos/' . Auth::user()->foto_profile)))
                        <img src="{{ asset('profile_photos/' . Auth::user()->foto_profile) }}" class="w-full h-full object-cover">
                    @else
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRO3ayNhsQd22NqoupI8bCbu1UUT-3N5Hmxbg&s" class="w-full h-full object-cover">
                    @endif
                </div>
                <p class="font-medium">{{ Auth::user()->name }}</p>
                <i class="fas fa-chevron-down"></i>
            </button>

            <div id="dropdownMenu" class="hidden absolute right-0 mt-3 w-48 bg-white rounded-lg shadow-lg py-2 z-[9999]">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">My Profile</a>
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Settings</a>
                <div class="border-t border-gray-200 my-2"></div>
                <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">@csrf</form>
                <a href="#" onclick="confirmLogout(event)" class="block px-4 py-2 text-red-500 hover:bg-gray-100">Log Out</a>
            </div>
        </div>
    </div>
</header>

<!-- Statistik Ringkas -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    @foreach($stats as $stat)
        <div class="p-6 rounded-xl shadow hover:shadow-lg transition-all duration-300 flex items-center justify-between" style="background-color: #F8EFE2;">
            <div class="w-14 h-14 flex items-center justify-center rounded-full border-2 border-red-800 bg-red-100">
                <i class="fas {{ $stat['icon'] }} text-red-800 text-xl"></i>
            </div>
            <div class="text-right">
                <p class="text-sm font-medium text-gray-600">{{ $stat['label'] }}</p>
                <h2 class="text-2xl font-bold text-red-800" data-count="{{ $stat['value'] }}">0</h2>
            </div>
        </div>
    @endforeach
</div>

<!-- Filter Tanggal Map -->
<div class="flex items-center gap-4 mb-4">
    <label for="filterStart">Dari:</label>
    <input type="date" id="filterStart" class="border rounded p-1">
    <label for="filterEnd">Sampai:</label>
    <input type="date" id="filterEnd" class="border rounded p-1">
    <button onclick="filterMap()" class="bg-red-800 text-white px-3 py-1 rounded">Filter</button>
</div>

<!-- Map -->
<div id="mapRiau"
     class="bg-white rounded-xl shadow mb-6 overflow-hidden border border-gray-200"
     style="height: 400px; width: 100%;">
</div>

<!-- Perbandingan Waktu -->
<div class="mb-6 bg-white p-4 rounded-xl shadow border border-gray-200">
    <!-- Judul -->
    <h2 class="text-lg font-bold text-red-800 mb-4">Perbandingan Berdasarkan Waktu</h2>

    <div class="flex items-center gap-4 mb-4">
        <label for="dateStart">Dari:</label>
        <input type="date" id="dateStart" class="border rounded p-1">
        <label for="dateEnd">Sampai:</label>
        <input type="date" id="dateEnd" class="border rounded p-1">
        <button onclick="filterByDate()" class="bg-red-800 text-white px-3 py-1 rounded">Tampilkan</button>
    </div>

    <canvas id="yearComparison" class="w-full h-64"
        data-values="{{ json_encode($yearComparison) }}">
    </canvas>
</div>

<!-- 5 Bar Chart -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <!-- Chart 1-3 normal -->
    @foreach([
        ['id'=>'chartSantunan','title'=>'Santunan','data'=>$chartSantunan],
        ['id'=>'chartMeninggal','title'=>'Korban Meninggal','data'=>$chartMeninggal],
        ['id'=>'chartKorbanPerkot','title'=>'Korban Per Kota','data'=>$chartKorbanPerkot],
    ] as $chart)
        <div class="rounded-xl shadow p-4 bg-white border border-gray-200">
            <h3 class="font-semibold mb-2 text-gray-700">{{ $chart['title'] }}</h3>
            <canvas id="{{ $chart['id'] }}" class="w-full h-48" data-values="{{ json_encode($chart['data']) }}"></canvas>
        </div>
    @endforeach

    <!-- Chart 4 & 5 dibuat besar -->
    @foreach([
        ['id'=>'chartKorbanPerkec','title'=>'Korban Per Kecamatan','data'=>$chartKorbanPerkecmtn],
        ['id'=>'chartLainnya','title'=>'Lainnya','data'=>$chartLainnya],
    ] as $chart)
        <div class="rounded-xl shadow p-4 bg-white border border-gray-200 lg:col-span-2">
            <h3 class="font-semibold mb-2 text-gray-700">{{ $chart['title'] }}</h3>
            <canvas id="{{ $chart['id'] }}" class="w-full h-64" data-values="{{ json_encode($chart['data']) }}"></canvas>
        </div>
    @endforeach
</div>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- JS Scripts -->
<script>
/* ================= Count Up Animation ================= */
document.querySelectorAll('[data-count]').forEach(el => {
    let target = +el.getAttribute('data-count');
    let count = 0;
    let step = Math.ceil(target / 100);
    let interval = setInterval(() => {
        count += step;
        if (count >= target) {
            el.textContent = target.toLocaleString();
            clearInterval(interval);
        } else {
            el.textContent = count.toLocaleString();
        }
    }, 20);
});

/* ================= Chart Render ================= */
function renderCharts() {
    document.querySelectorAll('canvas[data-values]').forEach(canvas => {
        const ctx = canvas.getContext('2d');
        const values = JSON.parse(canvas.getAttribute('data-values'));

        // Hancurkan chart lama kalau ada
        if (canvas.chartInstance) {
            canvas.chartInstance.destroy();
        }

        canvas.chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: values.labels,
                datasets: [{
                    label: canvas.id,
                    data: values.data,
                    backgroundColor: "rgba(178,34,52,0.8)",
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    });
}
renderCharts();

/* ================= Year Comparison Chart ================= */
function renderYearComparison() {
    const canvas = document.getElementById('yearComparison');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const values = JSON.parse(canvas.getAttribute('data-values'));

    // kalau sudah ada chart, hancurin dulu
    if (canvas.chartInstance) {
        canvas.chartInstance.destroy();
    }

    canvas.chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: Object.keys(values),
            datasets: [{
                label: "Total Korban",
                data: Object.values(values),
                backgroundColor: "rgba(178,34,52,0.8)",
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
}

renderYearComparison();

/* ================= Simpan Data Original untuk Filter ================= */
document.querySelectorAll('canvas[data-values]').forEach(canvas => {
    canvas.setAttribute("data-original", canvas.getAttribute("data-values"));
});

window.filterByDate = function() {
    const start = document.getElementById('dateStart').value;
    const end = document.getElementById('dateEnd').value;

    if (!start || !end) {
        Swal.fire({
            icon: 'warning',
            title: 'Tanggal Belum Dipilih',
            text: 'Silakan pilih rentang tanggal terlebih dahulu.',
            confirmButtonColor: '#b22234'
        });
        return;
    }

    const startDate = new Date(start);
    const endDate = new Date(end);

    if (startDate > endDate) {
        Swal.fire({
            icon: 'error',
            title: 'Input Tidak Valid',
            text: 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir.',
            confirmButtonColor: '#b22234'
        });
        return;
    }

    // Loop semua chart kecuali map
    document.querySelectorAll('canvas[data-values]').forEach(canvas => {
        const ctx = canvas.getContext('2d');
        const values = JSON.parse(canvas.getAttribute('data-original'));

        let filtered = { labels: [], data: [] };

        values.labels.forEach((label, i) => {
            // Parsing tanggal dari label (format bisa YYYY, YYYY-MM, atau YYYY-MM-DD)
            let labelDate;
            if (/^\d{4}$/.test(label)) {
                labelDate = new Date(label + "-01-01"); // hanya tahun
            } else if (/^\d{4}-\d{2}$/.test(label)) {
                labelDate = new Date(label + "-01"); // tahun-bulan
            } else {
                labelDate = new Date(label); // tanggal lengkap
            }

            if (labelDate >= startDate && labelDate <= endDate) {
                filtered.labels.push(label);
                filtered.data.push(values.data[i]);
            }
        });

        // Hapus chart lama lalu render ulang
        if (canvas.chartInstance) {
            canvas.chartInstance.destroy();
        }

        canvas.chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: filtered.labels,
                datasets: [{
                    label: canvas.id,
                    data: filtered.data,
                    backgroundColor: "rgba(178,34,52,0.8)",
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    });

    Swal.fire({
        icon: 'success',
        title: 'Filter Berhasil',
        text: `Menampilkan data dari ${start} sampai ${end}.`,
        confirmButtonColor: '#b22234',
        timer: 2000,
        showConfirmButton: false
    });
}

/* ================= Filter Semua Chart (Kecuali Map) ================= */
window.filterYear = function () {
    const start = document.getElementById('yearStart').value;
    const end = document.getElementById('yearEnd').value;

    if (!start || !end) {
        Swal.fire({
            icon: 'warning',
            title: 'Tahun Belum Dipilih',
            text: 'Silakan pilih tahun mulai dan tahun akhir.',
            confirmButtonColor: '#b22234'
        });
        return;
    }

    const startYear = parseInt(start);
    const endYear = parseInt(end);

    if (startYear > endYear) {
        Swal.fire({
            icon: 'error',
            title: 'Input Tidak Valid',
            text: 'Tahun mulai tidak boleh lebih besar dari tahun akhir.',
            confirmButtonColor: '#b22234'
        });
        return;
    }

    // Loop semua canvas chart (kecuali map)
    document.querySelectorAll('canvas[data-values]').forEach(canvas => {
        const ctx = canvas.getContext('2d');
        let values = JSON.parse(canvas.getAttribute('data-original'));

        let filtered = { labels: [], data: [] };

        values.labels.forEach((label, i) => {
            // Coba ekstrak tahun dari label (misal 2023, 2023-05, 2023-05-12)
            let tahun = parseInt(label.toString().substring(0, 4));
            if (tahun >= startYear && tahun <= endYear) {
                filtered.labels.push(label);
                filtered.data.push(values.data[i]);
            }
        });

        // Hapus chart lama sebelum render baru
        if (canvas.chartInstance) {
            canvas.chartInstance.destroy();
        }

        canvas.chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: filtered.labels,
                datasets: [{
                    label: canvas.id,
                    data: filtered.data,
                    backgroundColor: "rgba(178,34,52,0.8)",
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    });

    Swal.fire({
        icon: 'success',
        title: 'Filter Berhasil',
        text: `Menampilkan data dari tahun ${startYear} sampai ${endYear}.`,
        confirmButtonColor: '#b22234',
        timer: 2000,
        showConfirmButton: false
    });
};

/* ================= Leaflet Map ================= */
var map = L.map('mapRiau').setView([-0.507, 101.447], 8);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap'
}).addTo(map);

var kecelakaan = @json($kecelakaan);
console.log("kecelakaan:", kecelakaan);
var markers = [];

function addMarkers(data) {
    data.forEach(k => {
        if (!k.lat || !k.lng) return; // skip kalau kosong/null
        let marker = L.marker([parseFloat(k.lat), parseFloat(k.lng)]).addTo(map);
        marker.bindPopup(`
            <b style="color:#b22234">${k.name}</b><br>
            <b>Jam Kejadian:</b> ${k.jam}<br>
            <b>Korban:</b> ${k.korban} orang<br>
            <b>Luka-luka:</b> ${k.luka}<br>
            <b>Meninggal:</b> ${k.meninggal}<br>
            <b>Action Plan:</b> ${k.action}
        `);
        markers.push(marker);
    });
}

addMarkers(kecelakaan);

    window.filterMap = function() {
        const start = document.getElementById('filterStart').value;
        const end = document.getElementById('filterEnd').value;

        if (!start || !end) {
            Swal.fire({
                icon: 'warning',
                title: 'Tanggal Belum Dipilih',
                text: 'Silakan pilih rentang tanggal terlebih dahulu.',
                confirmButtonColor: '#b22234'
            });
            return;
        }

        const startDate = new Date(start);
        const endDate = new Date(end);

        const filtered = kecelakaan.filter(k => {
            const kDate = new Date(k.tanggal);
            return kDate >= startDate && kDate <= endDate;
        });

        markers.forEach(m => map.removeLayer(m));
        markers = [];
        addMarkers(filtered);

        if (filtered.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'Data Tidak Ditemukan',
                text: 'Tidak ada kecelakaan pada rentang tanggal ini.',
                confirmButtonColor: '#b22234'
            });
        } else {
            Swal.fire({
                icon: 'success',
                title: 'Filter Berhasil',
                text: `Menampilkan ${filtered.length} data kecelakaan.`,
                confirmButtonColor: '#b22234',
                timer: 2000,
                showConfirmButton: false
            });
        }
    }

/* ================= Dropdown & Logout ================= */
function toggleDropdown() {
    document.getElementById('dropdownMenu').classList.toggle('hidden');
}

document.addEventListener('click', function(event) {
    const profileButton = document.getElementById('profileButton');
    const dropdown = document.getElementById('dropdownMenu');
    if (!profileButton.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});

function confirmLogout(event) {
    event.preventDefault();
    if (confirm('Are you sure you want to log out?')) {
        document.getElementById('logout-form').submit();
    }
}
</script>
@endsection
