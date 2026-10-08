<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login · Sistem Informasi Data Obat</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-900 px-4 py-10">
    <div class="w-full max-w-md">

        <div class="mb-8 text-center">
            <span class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-500 text-slate-900">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7"><rect x="3" y="8" width="18" height="8" rx="4"/><path d="M12 8v8"/></svg>
            </span>
            <h1 class="text-2xl font-semibold text-white">Sistem Informasi Data Obat</h1>
            <p class="mt-1 text-sm text-slate-400">Silakan login menggunakan akun anda</p>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-800/60 p-6 shadow-xl sm:p-8">
            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-rose-500/40 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-5" novalidate>
                @csrf

                <div>
                    <label for="username" class="mb-1.5 block text-sm font-medium text-slate-300">Username</label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}"
                        autocomplete="username" autofocus required
                        placeholder="Masukkan username"
                        class="w-full rounded-xl border {{ $errors->has('username') && old('username') ? 'border-rose-500' : 'border-slate-700' }} bg-slate-900 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30">
                    @error('username')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-slate-300">Password</label>
                    <input type="password" name="password" id="password"
                        autocomplete="current-password" required
                        placeholder="Masukkan password"
                        class="w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30">
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-400">
                    <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-emerald-500 focus:ring-emerald-500/30">
                    Ingat saya
                </label>

                <button type="submit"
                    class="w-full rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                    Login
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-slate-500">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-medium text-emerald-400 hover:text-emerald-300">Daftar di sini</a>
        </p>
    </div>
</body>
</html>
