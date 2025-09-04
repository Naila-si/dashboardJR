<!-- Modal Tambah Data -->
<div id="formModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-[9999]">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl p-6 relative max-h-[90vh] overflow-y-auto">
        <!-- Tombol close -->
        <button onclick="closeForm()"
            class="absolute top-3 right-3 text-gray-500 hover:text-red-500 text-2xl font-bold">✕</button>

        <h2 class="text-2xl font-semibold mb-6 text-gray-800">Tambah Pengajuan Ahli Waris</h2>

        <form action="{{ route('ahliwaris.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf

            <!-- Kolom kiri -->
            <div>
                <label class="block text-sm font-medium mb-1">Tanggal</label>
                <input type="date" name="tanggal"
                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">No Berkas</label>
                <input type="text" name="no_berkas"
                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Cedera</label>
                <select name="cedera"
                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
                    <option value="LL">Luka-Luka</option>
                    <option value="MD">Meninggal Dunia</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Status</label>
                <select name="status"
                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
                    <option value="Proses">Proses</option>
                    <option value="Selesai">Selesai</option>
                    <option value="Rejected">Rejected</option>
                </select>
            </div>

            <!-- full width -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">Nama Pemohon</label>
                <input type="text" name="nama_pemohon"
                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">Alamat</label>
                <textarea name="alamat" rows="3"
                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400"></textarea>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">Penyelesaian</label>
                <input type="text" name="penyelesaian"
                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
            </div>

            <!-- tombol -->
            <div class="md:col-span-2 flex justify-end gap-3 mt-4">
                <button type="button" onclick="closeForm()"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-5 py-2 rounded-lg">
                    Batal
                </button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
