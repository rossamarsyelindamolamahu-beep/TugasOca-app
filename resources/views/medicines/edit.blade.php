@extends('layouts.app')

@section('title', 'Edit Obat')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Edit Data Obat</h2>
                    <p class="mt-0.5 text-xs text-slate-500">Ubah informasi obat <span class="font-semibold text-slate-700">#{{ $medicine->id }} — {{ $medicine->name }}</span></p>
                </div>
                <span class="rounded-full {{ $medicine->isLowStock() ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }} px-3 py-1 text-xs font-semibold">
                    {{ $medicine->stockStatus() }}
                </span>
            </div>

            <form method="POST" action="{{ route('medicines.update', $medicine) }}" class="mt-6 space-y-5" novalidate>
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Obat <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $medicine->name) }}" required maxlength="255"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
                        @error('name') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="category_id" class="mb-1.5 block text-sm font-medium text-slate-700">Kategori <span class="text-rose-500">*</span></label>
                        <select name="category_id" id="category_id" required
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((int) old('category_id', $medicine->category_id) === $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="unit" class="mb-1.5 block text-sm font-medium text-slate-700">Satuan <span class="text-rose-500">*</span></label>
                        <input type="text" name="unit" id="unit" value="{{ old('unit', $medicine->unit) }}" required maxlength="50"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
                        @error('unit') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="stock" class="mb-1.5 block text-sm font-medium text-slate-700">Stok <span class="text-rose-500">*</span></label>
                        <input type="number" name="stock" id="stock" value="{{ old('stock', $medicine->stock) }}" required min="0" step="1"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
                        <p class="mt-1 text-xs text-slate-400">Perubahan stok akan otomatis dicatat ke riwayat stok. Untuk transaksi, gunakan menu Manajemen Stok.</p>
                        @error('stock') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="price" class="mb-1.5 block text-sm font-medium text-slate-700">Harga (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="price" id="price" value="{{ old('price', $medicine->price) }}" required min="0" step="1"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
                        @error('price') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="expiration_date" class="mb-1.5 block text-sm font-medium text-slate-700">Tanggal Kadaluarsa <span class="text-rose-500">*</span></label>
                        <input type="date" name="expiration_date" id="expiration_date"
                            value="{{ old('expiration_date', $medicine->expiration_date?->format('Y-m-d')) }}" required
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
                        @error('expiration_date') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="mb-1.5 block text-sm font-medium text-slate-700">Deskripsi</label>
                        <textarea name="description" id="description" rows="3" maxlength="1000"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">{{ old('description', $medicine->description) }}</textarea>
                        @error('description') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('medicines.index') }}"
                       class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">Batal</a>
                    <button type="submit"
                        class="rounded-xl bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-emerald-400">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
