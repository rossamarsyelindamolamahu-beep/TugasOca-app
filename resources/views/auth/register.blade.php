<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar · Sistem Informasi Data Obat</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-900 px-4 py-10">
    <div class="w-full max-w-md">

        <div class="mb-8 text-center">
            <span class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-500 text-slate-900">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7"><rect x="3" y="8" width="18" height="8" rx="4"/><path d="M12 8v8"/></svg>
            </span>
            <h1 class="text-2xl font-semibold text-white">Sistem Informasi Data Obat</h1>
            <p class="mt-1 text-sm text-slate-400">Buat akun baru untuk mengakses sistem</p>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-800/60 p-6 shadow-xl sm:p-8">
            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-rose-500/40 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.store') }}" class="space-y-5" novalidate>
                @csrf

                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-slate-300">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                        placeholder="Masukkan nama lengkap"
                        class="w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30">
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="username" class="mb-1.5 block text-sm font-medium text-slate-300">Username</label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" required
                        autocomplete="username"
                        placeholder="Masukkan username"
                        class="w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30">
                    @error('username')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-slate-300">Password</label>
                    <input type="password" name="password" id="password" required
                        autocomplete="new-password" minlength="8"
                        placeholder="Minimal 8 karakter"
                        class="w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30">
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-slate-300">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        autocomplete="new-password" minlength="8"
                        placeholder="Ulangi password"
                        class="w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30">
                    @error('password_confirmation')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                    Daftar
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-slate-500">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-medium text-emerald-400 hover:text-emerald-300">Login di sini</a>
        </p>
    </div>
</body>
</html>
