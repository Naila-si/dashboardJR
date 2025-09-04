@extends('sidebar')

@section('title', 'Data Ahli Waris')

@section('content')
<div class="space-y-6 px-0 w-full max-w-full">

    <!-- HEADER -->
    <header class="flex justify-between items-center mb-6 shadow rounded-md p-4 bg-red-800 text-amber-100">
        <h1 class="text-2xl text-white font-bold">Data Ahli Waris</h1>
        <div class="flex items-center space-x-4">
            <!-- Search -->
            <div class="relative">
                <input type="text" placeholder="Search..."
                    class="w-64 pl-10 pr-4 py-2 rounded-md border border-white text-white placeholder-white bg-transparent focus:outline-none focus:ring-2 focus:ring-white transition shadow-sm">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-white"></i>
            </div>

            <!-- Profile -->
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

                <!-- dropdownMenu: pastikan z-index besar supaya tidak ketimpa map/chart -->
                <div id="dropdownMenu" class="hidden absolute right-0 mt-3 w-48 bg-white rounded-lg shadow-lg py-2 z-[9999]">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">My Profile</a>
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Settings</a>
                    <div class="border-t border-gray-200 my-2"></div>
                    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>
                    <a href="#" onclick="confirmLogout(event)" class="block px-4 py-2 text-red-500 hover:bg-gray-100">Log Out</a>
                </div>
            </div>
        </div>
    </header>

    <!-- MAP -->
    <div class="mb-6">
        <div id="map" style="height: 400px; width: 100%;" class="rounded shadow"></div>
    </div>

    <!-- TABEL DATA SANTUNAN -->
    <div class="overflow-x-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
            <h2 class="text-lg font-bold">Data Santunan</h2>
            <div class="flex gap-2 flex-wrap">
                <!-- Tambah -->
                <button onclick="openForm()" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition">
                    + Tambah Pengajuan
                </button>
                <!-- Upload -->
                <form action="{{ route('kecelakaan.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 cursor-pointer flex items-center gap-2 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        Upload File
                        <input type="file" name="file" class="hidden" onchange="this.form.submit()">
                    </label>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="min-w-full border border-gray-300 text-sm">
                <thead class="bg-gray-200 text-gray-700">
                    <tr>
                        <th class="p-2 text-center border">ID</th>
                        <th class="p-2 text-center border">Tanggal</th>
                        <th class="p-2 text-center border">No. Berkas</th>
                        <th class="p-2 text-center border">Cedera</th>
                        <th class="p-2 text-center border">Nama Pemohon</th>
                        <th class="p-2 text-center border">Alamat</th>
                        <th class="p-2 text-center border">Penyelesaian</th>
                        <th class="p-2 text-center border">Status</th>
                        <th class="p-2 text-center border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dataSantunan as $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-2 border text-center">{{ $item->id }}</td>
                        <td class="p-2 border text-center">{{ $item->tanggal }}</td>
                        <td class="p-2 border text-center">{{ $item->no_berkas }}</td>
                        <td class="p-2 border text-center">{{ $item->cedera }}</td>
                        <td class="p-2 border">{{ $item->nama_pemohon }}</td>
                        <td class="p-2 border">{{ $item->alamat }}</td>
                        <td class="p-2 border text-center">{{ $item->penyelesaian }}</td>
                        <td class="p-2 border text-center">{{ $item->status }}</td>
                        <td class="p-2 border text-center">
                            <div class="flex justify-center gap-2">
                                <button
                                    data-item='@json($item)'
                                    onclick="openEditModal(this.dataset.item)"
                                    class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">
                                    Edit
                                </button>
                                <form action="{{ route('ahliwaris.destroy', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition"
                                        onclick="return confirm('Yakin ingin menghapus data ini?')">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>

    <!-- MODAL TAMBAH -->
    @include('ahliwaris.partials.form-modal')

    <!-- MODAL EDIT -->
    @include('ahliwaris.partials.edit-modal')

    <!-- CHART -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <div class="bg-white p-4 shadow rounded-lg">
            <canvas id="santunanChart"></canvas>
        </div>
        <div class="bg-white p-4 shadow rounded-lg">
            <canvas id="meninggalChart"></canvas>
        </div>
        <div class="bg-white p-4 shadow rounded-lg">
            <canvas id="lukaChart"></canvas>
        </div>
    </div>
</div>

<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    var map = L.map('map').setView([-0.9471, 100.4172], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(map);
    L.marker([-0.9471, 100.4172]).addTo(map).bindPopup('<b>Lokasi Ahli Waris</b><br>Contoh alamat.');
</script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('santunanChart'), {
        type: 'bar',
        data: { labels: ['Jan','Feb','Mar','Apr','May','Jun'], datasets: [{ label: 'Santunan', data: @json($chartData['santunan']), backgroundColor: 'rgba(54, 162, 235, 0.6)' }] },
        options: { responsive: true, maintainAspectRatio: false }
    });
    new Chart(document.getElementById('meninggalChart'), {
        type: 'bar',
        data: { labels: ['Jan','Feb','Mar','Apr','May','Jun'], datasets: [{ label: 'Korban Meninggal', data: @json($chartData['korbanMeninggal']), backgroundColor: 'rgba(255, 99, 132, 0.6)' }] },
        options: { responsive: true, maintainAspectRatio: false }
    });
    new Chart(document.getElementById('lukaChart'), {
        type: 'bar',
        data: { labels: ['Jan','Feb','Mar','Apr','May','Jun'], datasets: [{ label: 'Korban Luka', data: @json($chartData['korbanLuka']), backgroundColor: 'rgba(255, 206, 86, 0.6)' }] },
        options: { responsive: true, maintainAspectRatio: false }
    });

    /* ---------------------------
       Modal & Dropdown functions
       --------------------------- */

    function openForm() {
        const el = document.getElementById('formModal');
        if (el) el.classList.remove('hidden');
    }
    function closeForm() {
        const el = document.getElementById('formModal');
        if (el) el.classList.add('hidden');
    }

    function openEditModal(itemJson) {
        try {
            const item = JSON.parse(itemJson);
            // fill inputs (must match IDs in your edit-modal partial)
            if (document.getElementById('editTanggal')) document.getElementById('editTanggal').value = item.tanggal || '';
            if (document.getElementById('editBerkas')) document.getElementById('editBerkas').value = item.no_berkas || '';
            if (document.getElementById('editCedera')) document.getElementById('editCedera').value = item.cedera || '';
            if (document.getElementById('editPemohon')) document.getElementById('editPemohon').value = item.nama_pemohon || '';
            if (document.getElementById('editAlamat')) document.getElementById('editAlamat').value = item.alamat || '';
            if (document.getElementById('editPenyelesaian')) document.getElementById('editPenyelesaian').value = item.penyelesaian || '';
            if (document.getElementById('editStatus')) document.getElementById('editStatus').value = item.status || '';

            // set form action (resource route expects PUT /ahliwaris/{id})
            const editForm = document.getElementById('editForm');
            if (editForm) editForm.action = "/ahliwaris/" + item.id;

            const editModal = document.getElementById('editModal');
            if (editModal) editModal.classList.remove('hidden');
        } catch (e) {
            console.error('openEditModal error:', e);
        }
    }
    function closeEditModal() {
        const el = document.getElementById('editModal');
        if (el) el.classList.add('hidden');
    }

    // dropdown toggle (header profile)
    function toggleDropdown() {
        const dropdown = document.getElementById('dropdownMenu');
        if (!dropdown) return;
        dropdown.classList.toggle('hidden');
    }

    // close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const profileButton = document.getElementById('profileButton');
        const dropdown = document.getElementById('dropdownMenu');
        if (!profileButton || !dropdown) return;
        // if the click is outside the profileButton container, hide the dropdown
        if (!profileButton.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // confirm logout helper
    function confirmLogout(event) {
        event.preventDefault();
        if (confirm('Are you sure you want to log out?')) {
            document.getElementById('logout-form').submit();
        }
    }
</script>
@endsection
