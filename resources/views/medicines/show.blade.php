@extends('layouts.app')

@section('title', 'Detail Obat')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('medicines.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-slate-800">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M15 18l-6-6 6-6"/></svg>
                Kembali ke Data Obat
            </a>

            @if (auth()->user()->isAdmin())
                <div class="flex gap-2">
                    <a href="{{ route('medicines.edit', $medicine) }}"
                       class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-2 text-sm font-medium text-amber-700 transition hover:bg-amber-100">Edit Obat</a>
                    <form method="POST" action="{{ route('medicines.destroy', $medicine) }}"
                          data-confirm="Yakin ingin menghapus obat {{ $medicine->name }}?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-medium text-rose-700 transition hover:bg-rose-100">Hapus</button>
                    </form>
                </div>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">ID Obat #{{ $medicine->id }}</p>
                    <h2 class="mt-1 text-xl font-semibold text-slate-900">{{ $medicine->name }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $medicine->category->name ?? '-' }}</p>
                </div>
                <span class="rounded-full {{ $medicine->isLowStock() ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }} px-3 py-1.5 text-sm font-semibold">
                    {{ $medicine->stockStatus() }}
                </span>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="text-xs text-slate-500">Stok</p>
                    <p class="mt-1 text-lg font-semibold {{ $medicine->isLowStock() ? 'text-rose-600' : 'text-slate-900' }}">{{ $medicine->stock }} {{ $medicine->unit }}</p>
                </div>
                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="text-xs text-slate-500">Harga</p>
                    <p class="mt-1 text-lg font-semibold text-slate-900">Rp {{ number_format($medicine->price, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="text-xs text-slate-500">Tanggal Kadaluarsa</p>
                    <p class="mt-1 text-lg font-semibold text-slate-900">{{ $medicine->expiration_date?->format('d/m/Y') ?? '-' }}</p>
                </div>
            </div>

            <div class="mt-5">
                <p class="text-xs text-slate-500">Deskripsi</p>
                <p class="mt-1 text-sm leading-relaxed text-slate-700">{{ $medicine->description ?: '—' }}</p>
            </div>
        </div>

        <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Riwayat Perubahan Stok</h3>
                    <p class="text-xs text-slate-500">Seluruh pergerakan stok obat ini</p>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ $movements->total() }} riwayat</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[560px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs uppercase tracking-wide text-slate-400">
                            <th class="pb-2.5 pr-3 font-semibold">Tanggal</th>
                            <th class="pb-2.5 pr-3 font-semibold">Jenis</th>
                            <th class="pb-2.5 pr-3 text-right font-semibold">Jumlah</th>
                            <th class="pb-2.5 font-semibold">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse ($movements as $movement)
                            <tr>
                                <td class="py-2.5 pr-3 text-slate-500">{{ $movement->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="py-2.5 pr-3">
                                    @if ($movement->isIncoming())
                                        <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Masuk</span>
                                    @else
                                        <span class="rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700">Keluar</span>
                                    @endif
                                </td>
                                <td class="py-2.5 pr-3 text-right font-semibold {{ $movement->isIncoming() ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $movement->isIncoming() ? '+' : '-' }}{{ $movement->quantity }}
                                </td>
                                <td class="py-2.5 text-slate-600">{{ $movement->description ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-sm text-slate-400">Belum ada riwayat perubahan stok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $movements->links() }}
            </div>
        </div>
    </div>
@endsection
