<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-screen bg-white flex relative overflow-hidden">

  <!-- Left Side (Company Image with dark overlay) -->
  <div class="relative w-5/12 h-full">
    <img src="{{ asset('build/assets/images/company.jpg') }}"
         alt="Company"
         class="w-full h-full object-cover">
  </div>

  <!-- Right Side -->
  <div class="flex w-7/12 h-full items-center justify-center relative
              bg-[linear-gradient(to_right,#d1d5db_1px,transparent_1px),linear-gradient(to_bottom,#d1d5db_1px,transparent_1px)]
              bg-[size:40px_40px]">

    <!-- Red box small -->
    <div class="absolute bg-[#B22234] w-[420px] h-[500px] rounded-3xl"></div>

    <!-- White Card -->
    <div class="relative z-10 bg-white w-[360px] rounded-2xl shadow-lg p-6">

      <!-- Header -->
      <div class="border-b mb-4 pb-2">
        <h3 class="text-sm font-semibold text-gray-700 inline-block border-b-2 border-[#B22234]">
          Sign In
        </h3>
      </div>

      <!-- Logo in top-right corner -->
      <img src="{{ asset('build/assets/images/logo.png') }}"
           alt="Logo"
           class="absolute top-4 right-4 w-28 drop-shadow-lg z-10">

      <!-- Title -->
      <h2 class="text-xl font-extrabold text-[#B22234] text-center">WELCOME</h2>

      <!-- Form -->
      <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Username (pakai email kalau default Laravel) -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" placeholder="Enter your email"
                class="w-full px-3 py-2 rounded-md bg-gray-100 border border-gray-300
                    focus:ring-2 focus:ring-[#B22234] outline-none" required>
        </div>

        <!-- Password -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <div class="relative">
                <input type="password" id="password" name="password" placeholder="Enter your password"
                    class="w-full px-3 py-2 rounded-md bg-gray-100 border border-gray-300
                        focus:ring-2 focus:ring-[#B22234] outline-none pr-10" required>

                <!-- Eye icon toggle -->
                <span id="togglePassword" class="absolute right-3 top-2.5 text-gray-400 cursor-pointer">
                    <!-- Eye open -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" class="w-5 h-5" id="eyeOpen">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477
                            0 8.268 2.943 9.542 7-1.274 4.057-5.065
                            7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <!-- Eye closed -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" class="w-5 h-5 hidden" id="eyeClosed">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 3l18 18M9.88 9.88A3 3 0 0012
                            15a3 3 0 002.12-5.12M6.1 6.1C4.35
                            7.72 3.12 9.72 2.46 12c1.27
                            4.06 5.07 7 9.54 7 1.55 0
                            3.01-.35 4.32-.97M17.9
                            17.9c1.75-1.62 2.98-3.62
                            3.64-5.9a11.946 11.946 0
                            00-2.64-4.36"/>
                    </svg>
                </span>
            </div>
        </div>

        <!-- Forgot password -->
        <div class="flex justify-end">
            <a href="{{ route('password.request') }}" class="text-[#B22234] text-sm hover:underline">
                Forgot Password?
            </a>
        </div>

        <!-- Submit -->
        <button type="submit"
            class="w-full bg-gradient-to-r from-[#B22234] to-[#B22234] text-white py-2 rounded-md
                font-semibold shadow hover:opacity-90 transition">
            Login
        </button>
    </form>
    </div>
  </div>

  <!-- Toggle Password Script -->
  <script>
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    const eyeOpen = document.getElementById('eyeOpen');
    const eyeClosed = document.getElementById('eyeClosed');

    togglePassword.addEventListener('click', () => {
      const isHidden = password.type === 'password';
      password.type = isHidden ? 'text' : 'password';
      eyeOpen.classList.toggle('hidden', !isHidden);
      eyeClosed.classList.toggle('hidden', isHidden);
    });
  </script>

</body>
</html>
