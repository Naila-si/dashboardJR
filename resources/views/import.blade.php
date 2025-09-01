@extends('Master')

@section('content')


<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Custom Loading SweetAlert -->
<script>
    function showLoading() {
        Swal.fire({
            title: 'Sedang mengimpor...',
            text: 'Mohon tunggu sebentar.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }
</script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        confirmButtonText: 'OK'
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: "{{ session('error') }}",
        confirmButtonText: 'Coba Lagi'
    });
</script>
@endif

<div class="bg-white p-6 rounded-lg shadow-md max-w-lg mx-auto mt-10">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Import Data Kecelakaan dari Excel</h2>

    <form action="{{ route('traffic-accidents.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
        @csrf
        <div class="mb-5">
            <label class="block text-gray-700 font-semibold mb-2">Pilih File Excel (.xlsx)</label>
            <input type="file" name="file" required
                   class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <button type="submit" id="submitBtn"
                class="bg-blue-600 hover:bg-blue-700 w-full text-white font-semibold py-2 rounded-lg transition duration-200">
            Import Sekarang
        </button>
    </form>
</div>

<!-- Script untuk munculin loading saat form submit -->
<script>
    document.getElementById('importForm').addEventListener('submit', function (e) {
        showLoading();
    });
</script>

@endsection
