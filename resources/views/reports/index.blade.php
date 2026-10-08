@extends('layouts.app')

@section('title', 'Laporan Data Obat')

@section('content')
    {{-- Print-only header --}}
    <div class="mb-4 hidden print:block">
        <h1 class="text-xl font-bold">Laporan Data Obat</h1>
        <p class="text-sm text-slate-600">Dicetak: {{ now()->format('d/m/Y H:i') }} · Sistem Informasi Data Obat</p>
    </div>

    {{-- Filters --}}
    <div class="no-print rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
            <form method="GET" action="{{ route('reports.index') }}" class="flex flex-1 flex-col gap-3 sm:flex-row lg:items-end">
                <div class="flex-1">
                    <label for="q" class="mb-1.5 block text-sm font-medium text-slate-700">Pencarian</label>
                    <input type="search" name="q" id="q" value="{{ $search }}" placeholder="Cari nama obat..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
                </div>

                <div class="sm:w-52">
                    <label for="category" class="mb-1.5 block text-sm font-medium text-slate-700">Kategori</label>
                    <select name="category" id="category"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected($categoryId === $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:w-52">
                    <label for="status" class="mb-1.5 block text-sm font-medium text-slate-700">Status Stok</label>
                    <select name="status" id="status"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
                        <option value="">Semua Status</option>
                        <option value="low" @selected($status === 'low')>Stok Menipis</option>
                        <option value="safe" @selected($status === 'safe')>Stok Aman</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">Terapkan</button>
                    <a href="{{ route('reports.index') }}"
                       class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">Reset</a>
                </div>
            </form>

            <button type="button" onclick="window.print()"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-emerald-400">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M6 9V3h12v6"/><path d="M6 18H4a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v7H6z"/></svg>
                Cetak Laporan
            </button>
        </div>
    </div>

    {{-- Report table --}}
    <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm print:border-0 print:p-0 print:shadow-none">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2 no-print">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Laporan Data Obat</h2>
                <p class="text-xs text-slate-500">Menampilkan {{ $medicines->count() }} data obat</p>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                Stok menipis: {{ $medicines->filter(fn ($m) => $m->isLowStock())->count() }} obat
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[860px] text-left text-sm">
                <thead>
                    <tr class="border-b-2 border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                        <th class="px-3 py-3 font-semibold">ID</th>
                        <th class="px-3 py-3 font-semibold">Nama Obat</th>
                        <th class="px-3 py-3 font-semibold">Kategori</th>
                        <th class="px-3 py-3 text-right font-semibold">Stok</th>
                        <th class="px-3 py-3 font-semibold">Satuan</th>
                        <th class="px-3 py-3 text-right font-semibold">Harga</th>
                        <th class="px-3 py-3 font-semibold">Kadaluarsa</th>
                        <th class="px-3 py-3 font-semibold">Status Stok</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($medicines as $medicine)
                        <tr>
                            <td class="px-3 py-2.5 text-slate-400">#{{ $medicine->id }}</td>
                            <td class="px-3 py-2.5 font-semibold text-slate-800">{{ $medicine->name }}</td>
                            <td class="px-3 py-2.5 text-slate-600">{{ $medicine->category->name ?? '-' }}</td>
                            <td class="px-3 py-2.5 text-right font-semibold {{ $medicine->isLowStock() ? 'text-rose-600' : 'text-slate-800' }}">{{ $medicine->stock }}</td>
                            <td class="px-3 py-2.5 text-slate-600">{{ $medicine->unit }}</td>
                            <td class="px-3 py-2.5 text-right text-slate-700">Rp {{ number_format($medicine->price, 0, ',', '.') }}</td>
                            <td class="px-3 py-2.5 text-slate-600">{{ $medicine->expiration_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-3 py-2.5">
                                @if ($medicine->isLowStock())
                                    <span class="rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700">Stok Menipis</span>
                                @else
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Stok Aman</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-10 text-center text-sm text-slate-400">Tidak ada data obat yang cocok dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
