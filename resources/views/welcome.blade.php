<!DOCTYPE html>
<html lang="id" x-data="{ dark: localStorage.getItem('theme') === 'dark' }" :class="{ 'dark': dark }" x-init="$watch('dark', v => localStorage.setItem('theme', v ? 'dark' : 'light'))">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inventra — Sistem Inventaris RW 05</title>

    @vite(['resources/css/app.css'])
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">


</head>

<body class="min-h-screen bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors duration-200">

{{-- ================================================================
     TOPBAR
================================================================ --}}
<header class="sticky top-0 z-50 h-14 border-b border-gray-200 dark:border-white/10
               bg-white/90 dark:bg-gray-900/90 backdrop-blur-sm flex items-center">
    <div class="w-full max-w-3xl mx-auto px-5 flex items-center justify-between gap-4">

        {{-- Brand --}}
        <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-primary-600 flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0v10l-8 4m-8-4V7m8 4v10"/>
                </svg>
            </div>
            <span class="font-semibold text-sm text-gray-800 dark:text-gray-100 tracking-tight">
                Inventra
                <span class="hidden sm:inline font-normal text-gray-400 dark:text-gray-500"> — RW 05 Kelurahan Muncul</span>
            </span>
        </div>

        {{-- Right actions --}}
        <div class="flex items-center gap-2">

            {{-- Dark mode toggle --}}
            <button @click="dark = !dark"
                    :aria-label="dark ? 'Mode terang' : 'Mode gelap'"
                    class="w-8 h-8 rounded-lg flex items-center justify-center
                           text-gray-500 dark:text-gray-400
                           hover:bg-gray-100 dark:hover:bg-white/5
                           hover:text-gray-800 dark:hover:text-gray-200
                           transition-all duration-150 focus:outline-none">
                <svg x-show="dark" style="width:17px;height:17px" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="4"/>
                    <path stroke-linecap="round" d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                </svg>
                <svg x-show="!dark" style="width:17px;height:17px" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
            </button>

            {{-- Login button --}}
            <a href="/dashboard"
               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-sm font-medium
                      bg-primary-600 hover:bg-primary-700 text-white
                      shadow-sm transition-colors duration-150">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M18 15l3-3m0 0l-3-3m3 3H9"/>
                </svg>
                Masuk
            </a>
        </div>
    </div>
</header>


{{-- ================================================================
     MAIN CONTENT
================================================================ --}}
<main class="max-w-3xl mx-auto px-5 py-14 sm:py-20">

    {{-- ── HERO ── --}}
    <div class="mb-12 animate-fade-up">

        <p class="text-xs font-mono font-medium text-primary-600 dark:text-primary-400
                  uppercase tracking-widest mb-4">
            Selamat datang
        </p>

        <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight
                   text-gray-900 dark:text-white leading-snug mb-4">
            Sistem pengelola inventaris<br>
            <span class="text-primary-600 dark:text-primary-400">RW 05</span>
            <span class="font-normal text-gray-400 dark:text-gray-500"> Kelurahan Muncul</span>
        </h1>

        <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400 leading-relaxed max-w-xl">
            Platform pengelolaan aset warga yang digunakan oleh pengurus RW 05
            untuk mencatat, meminjamkan, dan memantau seluruh barang milik RW
            secara terpusat dan transparan.
        </p>
    </div>

    <div class="sep mb-12"></div>

    {{-- ── FITUR ── --}}
    <div class="animate-fade-up2">
        <p class="text-xs font-mono font-medium text-gray-400 dark:text-gray-500
                  uppercase tracking-widest mb-5">
            Fitur tersedia
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach([
                [
                    'title' => 'Katalog barang',
                    'desc'  => 'Pencatatan aset lengkap dengan multi-kondisi stok, kategori, dan lokasi penyimpanan.',
                    'icon'  => 'M20 7l-8-4-8 4m16 0v10l-8 4m-8-4V7m8 4v10',
                ],
                [
                    'title' => 'Peminjaman',
                    'desc'  => 'Catat dan kelola peminjaman barang oleh warga dengan riwayat lengkap.',
                    'icon'  => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
                ],
                [
                    'title' => 'Pengembalian',
                    'desc'  => 'Proses pengembalian disertai laporan kondisi barang saat dikembalikan.',
                    'icon'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                ],
                [
                    'title' => 'Denda otomatis',
                    'desc'  => 'Penghitungan denda keterlambatan pengembalian secara otomatis oleh sistem.',
                    'icon'  => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z',
                ],
                [
                    'title' => 'Audit log',
                    'desc'  => 'Rekam jejak setiap perubahan data barang — siapa, apa, dan kapan terjadinya.',
                    'icon'  => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                ],
            ] as $feat)
            <div class="fi-card flex items-start gap-4 rounded-xl
                        border border-gray-200 dark:border-white/10
                        bg-white dark:bg-gray-900
                        p-4 sm:p-5">
                <div class="mt-0.5 w-9 h-9 rounded-lg shrink-0
                            bg-primary-50 dark:bg-primary-950/50
                            flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-primary-600 dark:text-primary-400"
                         style="width:18px;height:18px"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $feat['icon'] }}"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">
                        {{ $feat['title'] }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                        {{ $feat['desc'] }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="sep my-12 animate-fade-up3"></div>

    {{-- ── CTA ── --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center
                justify-between gap-5 animate-fade-up4">
        <div>
            <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">
                Khusus pengurus RW 05
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Masuk menggunakan akun yang telah didaftarkan oleh administrator.
            </p>
        </div>
        <a href="/dashboard"
           class="shrink-0 inline-flex items-center gap-2
                  px-5 py-2.5 rounded-lg text-sm font-medium
                  bg-primary-600 hover:bg-primary-700 text-white
                  shadow-sm transition-colors duration-150">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M18 15l3-3m0 0l-3-3m3 3H9"/>
            </svg>
            Masuk ke dashboard
        </a>
    </div>

</main>


{{-- ================================================================
     FOOTER
================================================================ --}}
<footer class="border-t border-gray-200 dark:border-white/10
               bg-white dark:bg-gray-900 py-5">
    <div class="max-w-3xl mx-auto px-5">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">

            <div class="flex items-center gap-2">
                <div class="w-5 h-5 rounded-md bg-primary-600 flex items-center justify-center">
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0v10l-8 4m-8-4V7m8 4v10"/>
                    </svg>
                </div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Inventra</span>
                <span class="text-gray-300 dark:text-gray-700">·</span>
                <span class="text-gray-400 dark:text-gray-500">RW 05 Kelurahan Muncul</span>
            </div>

            <p class="text-gray-400 dark:text-gray-600">
                &copy; {{ date('Y') }} Inventra. Hak cipta dilindungi.
            </p>

            {{-- <p class="font-mono text-gray-300 dark:text-gray-700">v1.0.0</p> --}}
        </div>
    </div>
</footer>

</body>
</html>
