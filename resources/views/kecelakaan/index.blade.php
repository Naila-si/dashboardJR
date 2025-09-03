@extends('sidebar')

@section('title', 'Data Kecelakaan')

@section('content')
<div class="space-y-6 px-4 md:px-6 lg:px-8">
<!-- Header -->
<header class="flex justify-between items-center mb-6 shadow rounded-md p-4 bg-red-800 text-amber-100">
    <h1 class="text-2xl text-white font-bold">Data Kecelakaan</h1>
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

    <!-- Filter -->
    <div class="bg-white shadow-md rounded-lg p-4 flex flex-wrap gap-4">
        <input type="date" class="border rounded-md px-3 py-2">
        <input type="date" class="border rounded-md px-3 py-2">
        <select class="border rounded-md px-3 py-2">
            <option>Jenis Periode</option>
            <option>Kecelakaan</option>
            <option>Laporan</option>
        </select>
        <select class="border rounded-md px-3 py-2">
            <option>Instansi Pembuat</option>
            <option>Kepolisian</option>
            <option>Kepolisian (Selain Satlantas Polres)</option>
            <option>Syahbandar</option>
            <option>Pomdam ABRI/TNI</option>
            <option>Polsuska (Kereta Api)</option>
            <option>Bandara Udara</option>
        </select>
        <button class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
            Cari
        </button>
    </div>

    <!-- Map -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div id="map" style="height: 300px;" class="w-full h-72"></div>
    </div>

    <!-- Data Kecelakaan -->
    <div class="bg-white shadow-lg rounded-xl p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
            <h2 class="text-xl font-bold text-gray-800">Data Kecelakaan</h2>
            <div class="flex gap-2 flex-wrap">
                <!-- Tombol Tambah Pengajuan -->
                <button onclick="openForm()"
                    class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition">
                    + Tambah Pengajuan
                </button>

                <!-- Tombol Upload File -->
                <form action="{{ route('kecelakaan.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 cursor-pointer flex items-center gap-2 transition">
                    <!-- Cloud upload icon modern -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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

        <!-- Modal Form Tambah Pengajuan -->
        <div id="formModal" class="fixed inset-0 w-screen h-screen bg-black bg-opacity-50 flex items-center justify-center z-[9999] hidden">
            <div class="bg-white rounded-2xl w-full max-w-3xl h-[90vh] overflow-y-auto shadow-2xl relative z-[10000] p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">Tambah Pengajuan Kecelakaan</h3>
                    <button type="button" onclick="closeForm()" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
                </div>

            <form action="{{ route('kecelakaan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama -->
                    <div class="relative">
                        <label class="block text-gray-700 font-medium mb-1">Nama</label>
                        <input type="text" name="nama" placeholder="Masukkan nama korban"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:outline-none shadow-sm bg-gray-50 transition">
                    </div>

                    <!-- Lokasi -->
                    <div class="relative">
                        <label class="block text-gray-700 font-medium mb-1">Lokasi</label>
                        <input type="text" name="lokasi" placeholder="Masukkan lokasi"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:outline-none shadow-sm bg-gray-50 transition">
                    </div>

                    <!-- Tanggal & Waktu -->
                    <div class="relative">
                        <label class="block text-gray-700 font-medium mb-1">Tanggal & Waktu</label>
                        <input type="datetime-local" name="tanggal_waktu"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:outline-none shadow-sm bg-gray-50 transition">
                    </div>

                    <!-- Laporan -->
                    <div class="relative">
                        <label class="block text-gray-700 font-medium mb-1">Laporan</label>
                        <input type="text" name="laporan" placeholder="Masukkan laporan"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:outline-none shadow-sm bg-gray-50 transition">
                    </div>

                    <!-- Cidera -->
                    <div class="relative">
                        <label class="block text-gray-700 font-medium mb-1">Cidera</label>
                        <select name="cidera" class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:outline-none shadow-sm bg-gray-50 transition">
                            <option value="LL">LL</option>
                            <option value="MD">MD</option>
                        </select>
                    </div>

                    <!-- Sifat Laka -->
                    <div class="relative">
                        <label class="block text-gray-700 font-medium mb-1">Sifat Laka</label>
                        <select name="sifat_laka" class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:outline-none shadow-sm bg-gray-50 transition">
                            <option value="Normal">Normal</option>
                            <option value="Berat">Berat</option>
                            <option value="Ringan">Ringan</option>
                        </select>
                    </div>

                    <!-- Status LP -->
                    <div class="relative">
                        <label class="block text-gray-700 font-medium mb-1">Status LP</label>
                        <select name="status_lp" class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:outline-none shadow-sm bg-gray-50 transition">
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeForm()"
                        class="bg-gray-400 hover:bg-gray-500 text-white font-semibold px-5 py-2 rounded-lg shadow-md transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg shadow-md transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

        <!-- Table scrollable -->
        <div class="overflow-x-auto">
            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border px-4 py-2">ID</th>
                        <th class="border px-4 py-2">Nama</th>
                        <th class="border px-4 py-2">Lokasi</th>
                        <th class="border px-4 py-2">Tanggal & Waktu</th>
                        <th class="border px-4 py-2">Laporan</th>
                        <th class="border px-4 py-2">Cidera</th>
                        <th class="border px-4 py-2">Sifat Laka</th>
                        <th class="border px-4 py-2">Status LP</th>
                        <th class="border px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $item)
                    <tr>
                        <td class="border px-4 py-2">{{ $item->id }}</td>
                        <td class="border px-4 py-2">{{ $item->nama }}</td>
                        <td class="border px-4 py-2">{{ $item->lokasi }}</td>
                        <td class="border px-4 py-2">{{ $item->tanggal_waktu }}</td>
                        <td class="border px-4 py-2">{{ $item->laporan }}</td>
                        <td class="border px-4 py-2">{{ $item->cidera }}</td>
                        <td class="border px-4 py-2">{{ $item->sifat_laka }}</td>
                        <td class="border px-4 py-2">{{ $item->status_lp }}</td>
                        <td class="border px-4 py-2 space-x-2">
                            <button
                                data-item='@json($item)'
                                onclick="openEditModal(this.dataset.item)"
                                class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">
                                Edit
                            </button>
                            <form action="{{ route('kecelakaan.destroy', $item->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600"
                                        onclick="return confirm('Yakin ingin menghapus data ini?')">
                                    Hapus
                                </button>
                            </form>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="editModal" class="fixed inset-0 w-screen h-screen bg-black bg-opacity-50 flex items-center justify-center hidden z-[9999]">
        <div class="bg-white rounded-2xl w-full max-w-3xl h-[90vh] overflow-y-auto shadow-2xl relative z-[10000] p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Edit Pengajuan Kecelakaan</h3>
                <button type="button" onclick="closeEditModal()" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
            </div>

            <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_id">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="relative">
                        <label class="block text-gray-700 font-medium mb-1">Nama</label>
                        <input type="text" name="nama" id="edit_nama"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:outline-none shadow-sm bg-gray-50 transition">
                    </div>

                    <div class="relative">
                        <label class="block text-gray-700 font-medium mb-1">Lokasi</label>
                        <input type="text" name="lokasi" id="edit_lokasi"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:outline-none shadow-sm bg-gray-50 transition">
                    </div>

                    <div class="relative">
                        <label class="block text-gray-700 font-medium mb-1">Tanggal & Waktu</label>
                        <input type="datetime-local" name="tanggal_waktu" id="edit_tanggal"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:outline-none shadow-sm bg-gray-50 transition">
                    </div>

                    <div class="relative">
                        <label class="block text-gray-700 font-medium mb-1">Laporan</label>
                        <input type="text" name="laporan" id="edit_laporan"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:outline-none shadow-sm bg-gray-50 transition">
                    </div>

                    <div class="relative">
                        <label class="block text-gray-700 font-medium mb-1">Cidera</label>
                        <select name="cidera" id="edit_cidera"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:outline-none shadow-sm bg-gray-50 transition">
                            <option value="LL">LL</option>
                            <option value="MD">MD</option>
                        </select>
                    </div>

                    <div class="relative">
                        <label class="block text-gray-700 font-medium mb-1">Sifat Laka</label>
                        <select name="sifat_laka" id="edit_sifat"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:outline-none shadow-sm bg-gray-50 transition">
                            <option value="Normal">Normal</option>
                            <option value="Ringan">Ringan</option>
                            <option value="Berat">Berat</option>
                        </select>
                    </div>

                    <div class="relative">
                        <label class="block text-gray-700 font-medium mb-1">Status LP</label>
                        <select name="status_lp" id="edit_status"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:outline-none shadow-sm bg-gray-50 transition">
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeEditModal()"
                        class="bg-gray-400 hover:bg-gray-500 text-white font-semibold px-5 py-2 rounded-lg shadow-md transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg shadow-md transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        function openForm() {
            document.getElementById('formModal').classList.remove('hidden');
        }
        function closeForm() {
            document.getElementById('formModal').classList.add('hidden');
        }

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

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Konfirmasi sebelum batal
        function confirmCancel() {
            if(confirm("Anda yakin ingin membatalkan perubahan?")) {
                closeEditModal();
            }
        }

        // Konfirmasi sebelum submit
        function confirmSave() {
            return confirm("Anda yakin ingin menyimpan perubahan?");
        }
        function initLeaflet() {
            // Bikin map
            var map = L.map('map').setView([0.5071, 101.4478], 10);

            // Tile OSM
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            // Data dari backend
            var kecelakaan = @json($data);

            // Tambahin marker
            kecelakaan.forEach(function(item) {
                // Pastikan ada lat & lng di DB
                if (item.lat && item.lng) {
                    let marker = L.marker([parseFloat(item.lat), parseFloat(item.lng)]).addTo(map);
                    marker.bindPopup(`
                        <b style="color:#b22234">${item.nama}</b><br>
                        Lokasi: ${item.lokasi}<br>
                        Waktu: ${item.tanggal_waktu}<br>
                        Cidera: ${item.cidera}<br>
                        Status LP: ${item.status_lp}
                    `);
                }
            });
        }

        // Panggil pas load
        window.onload = initLeaflet;
    </script>

    <!-- Chart Korban -->
    <div class="bg-white shadow-md rounded-lg p-4">
        <h2 class="text-lg font-bold mb-3">Korban berdasarkan Usia & Jenis Kelamin</h2>
        <canvas id="korbanChart" height="100"></canvas>
    </div>

    <!-- Row Chart -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white shadow-md rounded-lg p-4">
            <h2 class="text-lg font-bold mb-3">Jenis Tabrakan</h2>
            <canvas id="tabrakanChart"></canvas>
        </div>
        <div class="bg-white shadow-md rounded-lg p-4 flex flex-col items-center">
            <h2 class="text-lg font-bold mb-3">Waktu Kecelakaan</h2>
            <canvas id="waktuChart" width="150" height="150"></canvas>
        </div>
        <div class="bg-white shadow-md rounded-lg p-4">
            <h2 class="text-lg font-bold mb-3">Jenis Kendaraan</h2>
            <canvas id="kendaraanChart"></canvas>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Chart Korban
    new Chart(document.getElementById('korbanChart'), {
        type: 'line',
        data: {
            labels: ['2019','2020','2021','2022'],
            datasets: [{
                label: 'Jumlah Korban',
                data: [20, 50, 40, 80],
                borderColor: '#b22234',
                fill: false
            }]
        }
    });

    // Jenis Tabrakan
    new Chart(document.getElementById('tabrakanChart'), {
        type: 'bar',
        data: {
            labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
            datasets: [
                { label: 'Depan', data: [10,15,12,8,6,14,9], backgroundColor: '#1d4ed8' },
                { label: 'Belakang', data: [5,10,8,6,5,12,7], backgroundColor: '#10b981' }
            ]
        }
    });

    // Waktu Kecelakaan (Gauge-like pakai doughnut)
    new Chart(document.getElementById('waktuChart'), {
        type: 'doughnut',
        data: {
            labels: ['Siang','Malam'],
            datasets: [{
                data: [60,40],
                backgroundColor: ['#f87171','#3b82f6']
            }]
        },
        options: { cutout: '70%' }
    });

    // Jenis Kendaraan
    new Chart(document.getElementById('kendaraanChart'), {
        type: 'doughnut',
        data: {
            labels: ['Mobil','Motor','Bus','Truck'],
            datasets: [{
                data: [81,22,62,62],
                backgroundColor: ['#ef4444','#10b981','#3b82f6','#f59e0b']
            }]
        }
    });

    function openEditModal(item) {
        // Parse data JSON kalau masih string
        if (typeof item === 'string') {
            item = JSON.parse(item);
        }

        document.getElementById('editModal').classList.remove('hidden');

        // Set action form sesuai id
        document.getElementById('editForm').action = '{{ url("kecelakaan") }}/' + item.id;

        // Isi field
        document.getElementById('edit_id').value = item.id || '';
        document.getElementById('edit_nama').value = item.nama || '';
        document.getElementById('edit_lokasi').value = item.lokasi || '';

        // Tanggal & Waktu
        if(item.tanggal_waktu){
            let dt = new Date(item.tanggal_waktu);
            let formatted = dt.toISOString().slice(0,16); // YYYY-MM-DDTHH:MM
            document.getElementById('edit_tanggal').value = formatted;
        } else {
            document.getElementById('edit_tanggal').value = '';
        }

        document.getElementById('edit_laporan').value = item.laporan || '';
        document.getElementById('edit_cidera').value = item.cidera || ''; // sesuai field migration
        document.getElementById('edit_sifat').value = item.sifat_laka || '';
        document.getElementById('edit_status').value = item.status_lp || '';
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>
@endsection
