@extends('layouts.app')

@section('title', 'Data Obat')

@section('content')
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Daftar Data Obat</h2>
                <p class="text-xs text-slate-500">Total {{ $medicines->total() }} data obat ditemukan</p>
            </div>

            @if (auth()->user()->isAdmin())
                <a href="{{ route('medicines.create') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-emerald-400">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-4 w-4"><path d="M12 5v14M5 12h14"/></svg>
                    Tambah Obat
                </a>
            @endif
        </div>

        {{-- Search & filter --}}
        <form method="GET" action="{{ route('medicines.index') }}" class="mt-5 flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input type="search" name="q" value="{{ $search }}" placeholder="Cari nama obat, satuan, atau kategori..."
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-9 pr-3 text-sm text-slate-800 placeholder-slate-400 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
            </div>

            <select name="category"
                class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:w-56">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected($categoryId === $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <button type="submit"
                    class="flex-1 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Cari
                </button>
                <a href="{{ route('medicines.index') }}"
                   class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">
                    Reset
                </a>
            </div>
        </form>

        {{-- Table --}}
        <div class="mt-5 overflow-x-auto">
            <table class="w-full min-w-[900px] text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-xs uppercase tracking-wide text-slate-500">
                        <th class="px-3 py-3 font-semibold">ID Obat</th>
                        <th class="px-3 py-3 font-semibold">Nama Obat</th>
                        <th class="px-3 py-3 font-semibold">Kategori</th>
                        <th class="px-3 py-3 text-right font-semibold">Stok</th>
                        <th class="px-3 py-3 font-semibold">Satuan</th>
                        <th class="px-3 py-3 text-right font-semibold">Harga</th>
                        <th class="px-3 py-3 font-semibold">Kadaluarsa</th>
                        <th class="px-3 py-3 font-semibold">Status</th>
                        <th class="px-3 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($medicines as $medicine)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-3 py-3 text-slate-400">#{{ $medicine->id }}</td>
                            <td class="px-3 py-3">
                                <a href="{{ route('medicines.show', $medicine) }}" class="font-semibold text-slate-800 hover:text-emerald-600">{{ $medicine->name }}</a>
                            </td>
                            <td class="px-3 py-3">
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">{{ $medicine->category->name ?? '-' }}</span>
                            </td>
                            <td class="px-3 py-3 text-right font-semibold {{ $medicine->isLowStock() ? 'text-rose-600' : 'text-slate-800' }}">{{ $medicine->stock }}</td>
                            <td class="px-3 py-3 text-slate-600">{{ $medicine->unit }}</td>
                            <td class="px-3 py-3 text-right text-slate-700">Rp {{ number_format($medicine->price, 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-slate-600">{{ $medicine->expiration_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-3 py-3">
                                @if ($medicine->isLowStock())
                                    <span class="rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700">Stok Menipis</span>
                                @else
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Stok Aman</span>
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('medicines.show', $medicine) }}"
                                       class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-50" title="Lihat">Lihat</a>

                                    @if (auth()->user()->isAdmin())
                                        <a href="{{ route('medicines.edit', $medicine) }}"
                                           class="rounded-lg border border-amber-200 px-2.5 py-1.5 text-xs font-medium text-amber-600 transition hover:bg-amber-50" title="Edit">Edit</a>

                                        <form method="POST" action="{{ route('medicines.destroy', $medicine) }}"
                                              data-confirm="Yakin ingin menghapus obat {{ $medicine->name }}?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg border border-rose-200 px-2.5 py-1.5 text-xs font-medium text-rose-600 transition hover:bg-rose-50" title="Hapus">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-3 py-10 text-center">
                                <p class="font-medium text-slate-500">Data obat tidak ditemukan.</p>
                                <p class="mt-1 text-xs text-slate-400">Coba ubah kata kunci pencarian atau filter kategori.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">
            {{ $medicines->links() }}
        </div>
    </div>
@endsection
