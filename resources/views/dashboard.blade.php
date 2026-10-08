@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    {{-- Statistic cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm text-slate-500">Total Jenis Obat</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-50 text-sky-600">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><rect x="3" y="8" width="18" height="8" rx="4"/><path d="M12 8v8"/></svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $totalMedicines }}</p>
            <p class="mt-1 text-xs text-slate-400">Jenis obat terdaftar</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm text-slate-500">Total Seluruh Stok</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M3 7.5 12 3l9 4.5v9L12 21l-9-4.5v-9z"/><path d="M3 7.5 12 12l9-4.5M12 12v9"/></svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($totalStock, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-slate-400">Satuan obat tersedia</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm text-slate-500">Total Kategori Obat</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5"/></svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $totalCategories }}</p>
            <p class="mt-1 text-xs text-slate-400">Kategori obat</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm text-slate-500">Obat Stok Menipis</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-rose-50 text-rose-600">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.3 3.9 2.4 17.5A2 2 0 0 0 4.1 20.5h15.8a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/></svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $lowStockCount }}</p>
            <p class="mt-1 text-xs text-slate-400">Stok ≤ {{ $threshold }} (Stok Menipis)</p>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
        {{-- Chart: jumlah obat per kategori --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Grafik Obat per Kategori</h2>
                    <p class="text-xs text-slate-500">Jumlah jenis obat pada setiap kategori</p>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ $totalCategories }} kategori</span>
            </div>

            @if ($categoryStats->isEmpty())
                <p class="py-8 text-center text-sm text-slate-400">Belum ada kategori.</p>
            @else
                <div class="space-y-4">
                    @foreach ($categoryStats as $category)
                        @php $pct = $category->medicines_count > 0 ? max(3, (int) round($category->medicines_count / $maxPerCategory * 100)) : 0; @endphp
                        <div>
                            <div class="mb-1.5 flex items-center justify-between text-sm">
                                <span class="font-medium text-slate-700">{{ $category->name }}</span>
                                <span class="text-slate-500">{{ $category->medicines_count }} obat</span>
                            </div>
                            <div class="h-2.5 w-full overflow-hidden rounded-full bg-slate-100">
                                <div class="h-2.5 rounded-full bg-emerald-500 transition-all" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Low stock alert list --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Obat dengan Stok Menipis</h2>
                    <p class="text-xs text-slate-500">Batas minimum: {{ $threshold }} satuan</p>
                </div>
                <span class="rounded-full {{ $lowStockCount > 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} px-3 py-1 text-xs font-semibold">
                    {{ $lowStockCount }} obat
                </span>
            </div>

            @if ($lowStockMedicines->isEmpty())
                <div class="py-8 text-center">
                    <span class="mx-auto mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="m5 13 4 4L19 7"/></svg>
                    </span>
                    <p class="text-sm text-slate-500">Semua stok obat masih aman.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 text-xs uppercase tracking-wide text-slate-400">
                                <th class="pb-2.5 pr-3 font-semibold">Obat</th>
                                <th class="pb-2.5 pr-3 font-semibold">Kategori</th>
                                <th class="pb-2.5 pr-3 text-right font-semibold">Stok</th>
                                <th class="pb-2.5 text-right font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($lowStockMedicines as $medicine)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-2.5 pr-3">
                                        <a href="{{ route('medicines.show', $medicine) }}" class="font-medium text-slate-800 hover:text-emerald-600">{{ $medicine->name }}</a>
                                    </td>
                                    <td class="py-2.5 pr-3 text-slate-500">{{ $medicine->category->name ?? '-' }}</td>
                                    <td class="py-2.5 pr-3 text-right font-semibold text-slate-800">{{ $medicine->stock }} {{ $medicine->unit }}</td>
                                    <td class="py-2.5 text-right">
                                        <span class="rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700">Stok Menipis</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Recent medicines --}}
    <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Data Obat Terbaru</h2>
                <p class="text-xs text-slate-500">5 obat yang terakhir ditambahkan</p>
            </div>
            <a href="{{ route('medicines.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Lihat semua →</a>
        </div>

        @if ($recentMedicines->isEmpty())
            <p class="py-6 text-center text-sm text-slate-400">Belum ada data obat.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs uppercase tracking-wide text-slate-400">
                            <th class="pb-2.5 pr-3 font-semibold">ID</th>
                            <th class="pb-2.5 pr-3 font-semibold">Nama Obat</th>
                            <th class="pb-2.5 pr-3 font-semibold">Kategori</th>
                            <th class="pb-2.5 pr-3 text-right font-semibold">Stok</th>
                            <th class="pb-2.5 text-right font-semibold">Harga</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($recentMedicines as $medicine)
                            <tr class="hover:bg-slate-50">
                                <td class="py-2.5 pr-3 text-slate-400">#{{ $medicine->id }}</td>
                                <td class="py-2.5 pr-3">
                                    <a href="{{ route('medicines.show', $medicine) }}" class="font-medium text-slate-800 hover:text-emerald-600">{{ $medicine->name }}</a>
                                </td>
                                <td class="py-2.5 pr-3 text-slate-500">{{ $medicine->category->name ?? '-' }}</td>
                                <td class="py-2.5 pr-3 text-right">{{ $medicine->stock }} {{ $medicine->unit }}</td>
                                <td class="py-2.5 text-right">Rp {{ number_format($medicine->price, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
