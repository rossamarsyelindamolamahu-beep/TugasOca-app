<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · Sistem Informasi Data Obat</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased">
    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        <aside id="sidebar"
            class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-slate-900 text-slate-100 transition-transform duration-200 lg:translate-x-0">
            <div class="flex h-16 items-center gap-3 border-b border-slate-800 px-5">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-500 text-slate-900">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><rect x="3" y="8" width="18" height="8" rx="4"/><path d="M12 8v8"/></svg>
                </span>
                <div class="leading-tight">
                    <p class="text-sm font-semibold text-white">Sistem Informasi</p>
                    <p class="text-xs text-slate-400">Data Obat</p>
                </div>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4 text-sm">
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('dashboard') ? 'bg-emerald-500 text-slate-900' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 10v10h13V10"/></svg>
                    Dashboard
                </a>

                <a href="{{ route('medicines.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('medicines.*') ? 'bg-emerald-500 text-slate-900' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><rect x="3" y="8" width="18" height="8" rx="4"/><path d="M12 8v8"/></svg>
                    Data Obat
                </a>

                <a href="{{ route('categories.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('categories.*') ? 'bg-emerald-500 text-slate-900' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5"/></svg>
                    Kategori Obat
                </a>

                <a href="{{ route('stocks.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('stocks.*') ? 'bg-emerald-500 text-slate-900' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M3 7.5 12 3l9 4.5v9L12 21l-9-4.5v-9z"/><path d="M3 7.5 12 12l9-4.5M12 12v9"/></svg>
                    Manajemen Stok
                </a>

                <a href="{{ route('reports.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('reports.*') ? 'bg-emerald-500 text-slate-900' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M6 3h9l3 3v15H6z"/><path d="M9 9h6M9 13h6M9 17h4"/></svg>
                    Laporan
                </a>
            </nav>

            <div class="border-t border-slate-800 px-3 py-4">
                <div class="mb-3 flex items-center gap-3 px-2">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-700 text-sm font-semibold text-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0 leading-tight">
                        <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                        <span class="inline-block rounded-full {{ auth()->user()->isAdmin() ? 'bg-emerald-500/15 text-emerald-400' : 'bg-sky-500/15 text-sky-400' }} px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wide">
                            {{ auth()->user()->isAdmin() ? 'Admin' : 'Pimpinan' }}
                        </span>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-lg border border-slate-700 px-3 py-2.5 text-sm font-medium text-slate-300 transition hover:border-rose-500 hover:bg-rose-500 hover:text-white">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M17 16l4-4-4-4"/><path d="M21 12H9"/><path d="M9 4H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h4"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <div id="sidebar-overlay" class="fixed inset-0 z-30 hidden bg-slate-900/50 lg:hidden"></div>

        {{-- Main column --}}
        <div class="flex min-h-screen w-full flex-col lg:pl-64 print:pl-0">

            {{-- Topbar --}}
            <header class="no-print sticky top-0 z-20 flex h-16 items-center justify-between gap-3 border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <button type="button" id="sidebar-toggle" aria-label="Buka menu"
                        class="rounded-lg border border-slate-200 p-2 text-slate-600 transition hover:bg-slate-100 lg:hidden">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" class="h-5 w-5"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="text-base font-semibold text-slate-900 sm:text-lg">@yield('title', 'Dashboard')</h1>
                        <p class="hidden text-xs text-slate-500 sm:block">Sistem Informasi Data Obat</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="hidden text-right leading-tight sm:block">
                        <p class="text-sm font-medium text-slate-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500">{{ auth()->user()->isAdmin() ? 'Admin / Petugas' : 'Pimpinan / Kepala' }}</p>
                    </div>
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                </div>
            </header>

            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                @if (session('success'))
                    <div class="mb-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 h-5 w-5 shrink-0"><path d="m5 13 4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-5 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 h-5 w-5 shrink-0"><path d="M12 8v5"/><path d="M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>

            <footer class="no-print border-t border-slate-200 px-4 py-4 text-center text-xs text-slate-400 sm:px-6 lg:px-8">
                © {{ date('Y') }} Sistem Informasi Data Obat
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
