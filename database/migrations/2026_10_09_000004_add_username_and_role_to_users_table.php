<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pastikan tabel users memiliki kolom username dan role.
     *
     * Migrasi ini diperlukan jika tabel users dibuat oleh versi awal
     * skeleton (email-based) yang berjalan sebelum perubahan skema,
     * sehingga migration 0001 tidak pernah dijalankan ulang.
     *
     * @return void
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username')->nullable()->after('name');
            });

            Schema::table('users', function (Blueprint $table) {
                $table->unique('username');
            });
        }

        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('admin')->after('password');
            });
        }

        // Skema lama skeleton memakai email NOT NULL; buat nullable agar
        // pemasukan akun (username-based) tidak gagal.
        if (Schema::hasColumn('users', 'email')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('email')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * Sengaja tidak menghapus kolom apa pun agar data pengguna tetap aman.
     *
     * @return void
     */
    public function down(): void
    {
        //
    }
};
