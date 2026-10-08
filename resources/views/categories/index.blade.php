@extends('layouts.app')

@section('title', 'Kategori Obat')

@section('content')
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Daftar Kategori Obat</h2>
                <p class="text-xs text-slate-500">Total {{ $categories->total() }} kategori ditemukan</p>
            </div>

            @if (auth()->user()->isAdmin())
                <a href="{{ route('categories.create') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-emerald-400">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-4 w-4"><path d="M12 5v14M5 12h14"/></svg>
                    Tambah Kategori
                </a>
            @endif
        </div>

        <form method="GET" action="{{ route('categories.index') }}" class="mt-5 flex gap-2">
            <div class="relative flex-1">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input type="search" name="q" value="{{ $search }}" placeholder="Cari kategori..."
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-9 pr-3 text-sm text-slate-800 placeholder-slate-400 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
            </div>
            <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">Cari</button>
            <a href="{{ route('categories.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">Reset</a>
        </form>

        <div class="mt-5 overflow-x-auto">
            <table class="w-full min-w-[640px] text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-xs uppercase tracking-wide text-slate-500">
                        <th class="px-3 py-3 font-semibold">ID</th>
                        <th class="px-3 py-3 font-semibold">Nama Kategori</th>
                        <th class="px-3 py-3 font-semibold">Deskripsi</th>
                        <th class="px-3 py-3 text-right font-semibold">Jumlah Obat</th>
                        <th class="px-3 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($categories as $category)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-3 py-3 text-slate-400">#{{ $category->id }}</td>
                            <td class="px-3 py-3 font-semibold text-slate-800">{{ $category->name }}</td>
                            <td class="px-3 py-3 text-slate-600">{{ $category->description ?: '—' }}</td>
                            <td class="px-3 py-3 text-right">
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">{{ $category->medicines_count }} obat</span>
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('medicines.index', ['category' => $category->id]) }}"
                                       class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-50">Lihat Obat</a>

                                    @if (auth()->user()->isAdmin())
                                        <a href="{{ route('categories.edit', $category) }}"
                                           class="rounded-lg border border-amber-200 px-2.5 py-1.5 text-xs font-medium text-amber-600 transition hover:bg-amber-50">Edit</a>

                                        <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                              data-confirm="Yakin ingin menghapus kategori {{ $category->name }}?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg border border-rose-200 px-2.5 py-1.5 text-xs font-medium text-rose-600 transition hover:bg-rose-50">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-10 text-center">
                                <p class="font-medium text-slate-500">Kategori tidak ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">
            {{ $categories->links() }}
        </div>
    </div>
@endsection
