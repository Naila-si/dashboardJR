<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Admin')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
        }
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
    </style>
</head>

<body class="flex">

<!-- Layout Admin -->
<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside id="sidebar"
           class="md:flex hidden fixed group bg-white shadow-lg w-16 hover:w-64 transition-all duration-300 flex flex-col h-full z-50">
        <!-- Logo -->
        <div class="flex justify-center items-center h-16 border-b border-gray-200">
            <img src="{{ asset('build/assets/images/logo.png') }}" alt="Logo"
                 class="w-10 group-hover:w-32 transition-all duration-300">
        </div>

        <!-- Label -->
        <div class="text-xs text-gray-400 px-4 mt-6 group-hover:opacity-100 opacity-0 transition-opacity duration-300">
            Menu Utama
        </div>

        <!-- Navigation -->
        <nav class="flex-1 flex flex-col mt-4 space-y-2 px-1">
            <a href="{{ route('dashboard') }}"
               class="flex items-center px-3 py-3 rounded-lg text-gray-700
                    hover:bg-gradient-to-r hover:from-red-500 hover:to-red-700 hover:text-white transition-all duration-300">
                <i class="fas fa-home text-lg w-6 text-center"></i>
                <span class="ml-3 opacity-0 group-hover:opacity-100 group-hover:translate-x-2
                            transform transition-all duration-300 font-medium">Dashboard</span>
            </a>

            <a href="{{ route('kecelakaan.index') }}"
               class="flex items-center px-3 py-3 rounded-lg text-gray-700
                    hover:bg-gradient-to-r hover:from-red-500 hover:to-red-700 hover:text-white transition-all duration-300">
                <i class="fas fa-car-crash text-lg w-6 text-center"></i>
                <span class="ml-3 opacity-0 group-hover:opacity-100 group-hover:translate-x-2
                            transform transition-all duration-300 font-medium">Data Kecelakaan</span>
            </a>

            <a href="{{ route('ahliwaris.index') }}"
               class="flex items-center px-3 py-3 rounded-lg text-gray-700
                    hover:bg-gradient-to-r hover:from-red-500 hover:to-red-700 hover:text-white transition-all duration-300">
                <i class="fas fa-users text-lg w-6 text-center"></i>
                <span class="ml-3 opacity-0 group-hover:opacity-100 group-hover:translate-x-2
                            transform transition-all duration-300 font-medium">Ahli Waris</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div id="mainContent" class="flex-1 transition-all duration-300 ml-16 group-hover:ml-64 pt-0 px-6 bg-gray-50">
        <!-- Header dinamis -->
        <header class="mb-6 border-b border-gray-200 pb-3">
            <h1 class="text-2xl font-semibold text-gray-800">@yield('header')</h1>
        </header>

        <!-- Konten halaman -->
        <main class="flex-1 w-full overflow-x-hidden">
            @yield('content')
        </main>
    </div>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');

    sidebar.addEventListener('mouseenter', () => {
        mainContent.classList.replace('ml-16','ml-64');
    });
    sidebar.addEventListener('mouseleave', () => {
        mainContent.classList.replace('ml-64','ml-16');
    });

    function confirmLogout(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Yakin mau keluar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, keluar',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>

</body>
</html>
