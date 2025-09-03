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
        <div class="p-6 rounded-xl shadow hover:shadow-lg transition-all duration-300 flex items-center justify-between bg-white">
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
<div class="bg-white rounded-xl shadow mb-6 h-96 overflow-hidden border border-gray-200" id="mapRiau"></div>

<!-- Perbandingan Tahun -->
<div class="mb-6 bg-white p-4 rounded-xl shadow border border-gray-200">
    <div class="flex items-center gap-4 mb-4">
        <label for="yearStart">Tahun Mulai:</label>
        <input type="number" id="yearStart" min="2000" max="{{ date('Y') }}" value="{{ date('Y')-1 }}" class="border rounded p-1">
        <label for="yearEnd">Tahun Akhir:</label>
        <input type="number" id="yearEnd" min="2000" max="{{ date('Y') }}" value="{{ date('Y') }}" class="border rounded p-1">
        <button onclick="filterYear()" class="bg-red-800 text-white px-3 py-1 rounded">Tampilkan</button>
    </div>
    <canvas id="yearComparison" class="w-full h-48" data-values="{{ json_encode($yearComparison) }}"></canvas>
</div>

<!-- 5 Bar Chart -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    @foreach([
        ['id'=>'chartSantunan','title'=>'Santunan','data'=>$chartSantunan],
        ['id'=>'chartMeninggal','title'=>'Korban Meninggal','data'=>$chartMeninggal],
        ['id'=>'chartKorbanPerkot','title'=>'Korban Per Kota','data'=>$chartKorbanPerkot],
        ['id'=>'chartKorbanPerkec','title'=>'Korban Per Kecamatan','data'=>$chartKorbanPerkecmtn],
        ['id'=>'chartLainnya','title'=>'Lainnya','data'=>$chartLainnya],
    ] as $chart)
        <div class="rounded-xl shadow p-4 bg-white border border-gray-200">
            <h3 class="font-semibold mb-2 text-gray-700">{{ $chart['title'] }}</h3>
            <canvas id="{{ $chart['id'] }}" class="w-full h-48" data-values="{{ json_encode($chart['data']) }}"></canvas>
        </div>
    @endforeach
</div>

<!-- JS Libraries -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyASfKCDURAkmkRiXESOgvW8D7TxyV8jIps&callback=initMap" async defer></script>

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
        new Chart(ctx, {
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

/* ================= Google Maps ================= */
function initMap() {
    const riau = { lat: -0.507, lng: 101.447 };
    const map = new google.maps.Map(document.getElementById('mapRiau'), { zoom: 8, center: riau });
    const kecelakaan = @json($kecelakaan);
    let mapMarkers = [];

    function addMarkers(data) {
        data.forEach(k => {
            const marker = new google.maps.Marker({
                position: { lat: parseFloat(k.lat), lng: parseFloat(k.lng) },
                map: map,
                icon: { path: google.maps.SymbolPath.CIRCLE, scale: 8, fillColor: "#b22234", fillOpacity: 0.9, strokeColor: "#fff", strokeWeight: 2 }
            });
            mapMarkers.push(marker);

            const info = new google.maps.InfoWindow({
                content: `<div style="min-width:220px">
                            <h3 style="margin:0; color:#b22234; font-weight:bold;">${k.name}</h3>
                            <p><b>Jam Kejadian:</b> ${k.jam}</p>
                            <p><b>Korban:</b> ${k.korban} orang</p>
                            <p><b>Luka-luka:</b> ${k.luka}</p>
                            <p><b>Meninggal:</b> ${k.meninggal}</p>
                            <p><b>Action Plan:</b><br>${k.action}</p>
                          </div>`
            });

            marker.addListener("mouseover", () => info.open(map, marker));
            marker.addListener("mouseout", () => info.close());
        });
    }

    addMarkers(kecelakaan);

    window.filterMap = function() {
        const start = document.getElementById('filterStart').value;
        const end = document.getElementById('filterEnd').value;
        const filtered = kecelakaan.filter(k => k.tanggal >= start && k.tanggal <= end);
        mapMarkers.forEach(m => m.setMap(null));
        mapMarkers = [];
        addMarkers(filtered);
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
