@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
    <div class="mx-auto max-w-xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-base font-semibold text-slate-900">Edit Kategori Obat</h2>
            <p class="mt-0.5 text-xs text-slate-500">Ubah informasi kategori <span class="font-semibold text-slate-700">{{ $category->name }}</span></p>

            <form method="POST" action="{{ route('categories.update', $category) }}" class="mt-6 space-y-5" novalidate>
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required maxlength="255"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
                    @error('name') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="mb-1.5 block text-sm font-medium text-slate-700">Deskripsi</label>
                    <textarea name="description" id="description" rows="3" maxlength="255"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">{{ old('description', $category->description) }}</textarea>
                    @error('description') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('categories.index') }}"
                       class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">Batal</a>
                    <button type="submit"
                        class="rounded-xl bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-emerald-400">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
