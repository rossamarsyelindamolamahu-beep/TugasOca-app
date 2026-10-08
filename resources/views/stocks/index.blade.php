@extends('layouts.app')

@section('title', 'Manajemen Stok')

@section('content')
    {{-- Low stock alert --}}
    @if ($lowStockMedicines->isNotEmpty())
        <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-5">
            <div class="flex items-center gap-2">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-rose-600"><path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.3 3.9 2.4 17.5A2 2 0 0 0 4.1 20.5h15.8a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/></svg>
                <h2 class="text-sm font-semibold text-rose-800">Peringatan Stok Menipis</h2>
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($lowStockMedicines as $medicine)
                    <a href="{{ route('medicines.show', $medicine) }}"
                       class="rounded-full border border-rose-200 bg-white px-3 py-1.5 text-xs font-medium text-rose-700 transition hover:bg-rose-100">
                        {{ $medicine->name }} — {{ $medicine->stock }} {{ $medicine->unit }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        {{-- Stock movement form (admin only) --}}
        @if (auth()->user()->isAdmin())
            <div class="xl:col-span-1">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-base font-semibold text-slate-900">Catat Perubahan Stok</h2>
                    <p class="mt-0.5 text-xs text-slate-500">Stok obat akan diperbarui otomatis.</p>

                    @if ($errors->any())
                        <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            <ul class="list-inside list-disc space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('stocks.store') }}" class="mt-5 space-y-4" novalidate>
                        @csrf

                        <div>
                            <label for="medicine_id" class="mb-1.5 block text-sm font-medium text-slate-700">Obat <span class="text-rose-500">*</span></label>
                            <select name="medicine_id" id="medicine_id" required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
                                <option value="">-- Pilih Obat --</option>
                                @foreach ($medicines as $medicine)
                                    <option value="{{ $medicine->id }}" @selected((int) old('medicine_id') === $medicine->id)>
                                        {{ $medicine->name }} (stok: {{ $medicine->stock }} {{ $medicine->unit }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <span class="mb-1.5 block text-sm font-medium text-slate-700">Jenis Pergerakan <span class="text-rose-500">*</span></span>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-medium text-slate-700 transition has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 has-[:checked]:text-emerald-700">
                                    <input type="radio" name="type" value="in" @checked(old('type') === 'in' || old('type') === null)>
                                    Stok Masuk
                                </label>
                                <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-medium text-slate-700 transition has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50 has-[:checked]:text-rose-700">
                                    <input type="radio" name="type" value="out" @checked(old('type') === 'out'>
                                    Stok Keluar
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="quantity" class="mb-1.5 block text-sm font-medium text-slate-700">Jumlah <span class="text-rose-500">*</span></label>
                            <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" required min="1" step="1"
                                placeholder="Contoh: 20"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
                        </div>

                        <div>
                            <label for="description" class="mb-1.5 block text-sm font-medium text-slate-700">Keterangan</label>
                            <input type="text" name="description" id="description" value="{{ old('description') }}" maxlength="255"
                                placeholder="Contoh: Pembelian dari supplier"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
                        </div>

                        <button type="submit"
                            class="w-full rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-emerald-400">
                            Simpan Perubahan Stok
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Movement history --}}
        <div class="{{ auth()->user()->isAdmin() ? 'xl:col-span-2' : 'xl:col-span-3' }}">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Riwayat Perubahan Stok</h2>
                        <p class="text-xs text-slate-500">Total {{ $movements->total() }} riwayat</p>
                    </div>

                    <div class="flex flex-wrap gap-1.5">
                        <a href="{{ route('stocks.index') }}"
                           class="rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ $type === null ? 'bg-slate-900 text-white' : 'border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Semua</a>
                        <a href="{{ route('stocks.index', ['type' => 'in']) }}"
                           class="rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ $type === 'in' ? 'bg-emerald-500 text-slate-900' : 'border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Masuk</a>
                        <a href="{{ route('stocks.index', ['type' => 'out']) }}"
                           class="rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ $type === 'out' ? 'bg-rose-500 text-white' : 'border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Keluar</a>
                    </div>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full min-w-[640px] text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 text-xs uppercase tracking-wide text-slate-400">
                                <th class="pb-2.5 pr-3 font-semibold">Tanggal</th>
                                <th class="pb-2.5 pr-3 font-semibold">Obat</th>
                                <th class="pb-2.5 pr-3 font-semibold">Jenis</th>
                                <th class="pb-2.5 pr-3 text-right font-semibold">Jumlah</th>
                                <th class="pb-2.5 font-semibold">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse ($movements as $movement)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="py-3 pr-3 text-slate-500">{{ $movement->created_at?->format('d/m/Y H:i') }}</td>
                                    <td class="py-3 pr-3">
                                        <a href="{{ route('medicines.show', $movement->medicine) }}" class="font-medium text-slate-800 hover:text-emerald-600">
                                            {{ $movement->medicine->name ?? '-' }}
                                        </a>
                                    </td>
                                    <td class="py-3 pr-3">
                                        @if ($movement->isIncoming())
                                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Masuk</span>
                                        @else
                                            <span class="rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700">Keluar</span>
                                        @endif
                                    </td>
                                    <td class="py-3 pr-3 text-right font-semibold {{ $movement->isIncoming() ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $movement->isIncoming() ? '+' : '-' }}{{ $movement->quantity }}
                                    </td>
                                    <td class="py-3 text-slate-600">{{ $movement->description ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-sm text-slate-400">Belum ada riwayat perubahan stok.</td>
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
    </div>
@endsection
