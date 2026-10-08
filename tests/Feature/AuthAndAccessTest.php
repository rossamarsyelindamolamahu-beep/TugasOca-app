<?php

use App\Models\Category;
use App\Models\Medicine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('guests are redirected to the login page', function () {
    $this->get('/dashboard')->assertRedirect(route('login'));
    $this->get('/medicines')->assertRedirect(route('login'));
    $this->get('/reports')->assertRedirect(route('login'));
});

test('login page can be rendered', function () {
    $this->get(route('login'))->assertOk()->assertSee('Username');
});

test('admin can login with valid credentials', function () {
    User::factory()->create([
        'username' => 'admin',
        'password' => Hash::make('rahasia123'),
    ]);

    $this->post(route('login.attempt'), [
        'username' => 'admin',
        'password' => 'rahasia123',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticated();
});

test('login fails with invalid credentials', function () {
    User::factory()->create([
        'username' => 'admin',
        'password' => Hash::make('rahasia123'),
    ]);

    $this->from(route('login'))
        ->post(route('login.attempt'), [
            'username' => 'admin',
            'password' => 'salah',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('username');

    $this->assertGuest();
});

test('login validation requires username and password', function () {
    $this->from(route('login'))
        ->post(route('login.attempt'), [])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors(['username', 'password']);
});

test('user can logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

test('admin and pimpinan can open the dashboard and read-only pages', function () {
    $admin = User::factory()->create();
    $pimpinan = User::factory()->pimpinan()->create();

    foreach ([$admin, $pimpinan] as $user) {
        $this->actingAs($user)->get(route('dashboard'))->assertOk();
        $this->actingAs($user)->get(route('medicines.index'))->assertOk();
        $this->actingAs($user)->get(route('categories.index'))->assertOk();
        $this->actingAs($user)->get(route('stocks.index'))->assertOk();
        $this->actingAs($user)->get(route('reports.index'))->assertOk();
    }
});

test('pimpinan cannot access admin CRUD routes', function () {
    $pimpinan = User::factory()->pimpinan()->create();
    $category = Category::create(['name' => 'Analgesik']);
    $medicine = Medicine::create([
        'category_id' => $category->id,
        'name' => 'Paracetamol',
        'stock' => 50,
        'unit' => 'Tablet',
        'price' => 5000,
        'expiration_date' => '2030-01-01',
    ]);

    $payload = [
        'category_id' => $category->id,
        'name' => 'Obat Baru',
        'stock' => 10,
        'unit' => 'Tablet',
        'price' => 1000,
        'expiration_date' => '2030-01-01',
    ];

    $this->actingAs($pimpinan)->get(route('medicines.create'))->assertForbidden();
    $this->actingAs($pimpinan)->post(route('medicines.store'), $payload)->assertForbidden();
    $this->actingAs($pimpinan)->get(route('medicines.edit', $medicine))->assertForbidden();
    $this->actingAs($pimpinan)->put(route('medicines.update', $medicine), $payload)->assertForbidden();
    $this->actingAs($pimpinan)->delete(route('medicines.destroy', $medicine))->assertForbidden();

    $this->actingAs($pimpinan)->get(route('categories.create'))->assertForbidden();
    $this->actingAs($pimpinan)->post(route('categories.store'), ['name' => 'Vitamin'])->assertForbidden();
    $this->actingAs($pimpinan)->get(route('categories.edit', $category))->assertForbidden();
    $this->actingAs($pimpinan)->put(route('categories.update', $category), ['name' => 'Analgesik'])->assertForbidden();
    $this->actingAs($pimpinan)->delete(route('categories.destroy', $category))->assertForbidden();

    $this->actingAs($pimpinan)->post(route('stocks.store'), [
        'medicine_id' => $medicine->id,
        'type' => 'in',
        'quantity' => 5,
    ])->assertForbidden();

    // Data is untouched.
    $this->assertDatabaseHas('medicines', ['id' => $medicine->id, 'name' => 'Paracetamol']);
    $this->assertDatabaseMissing('categories', ['name' => 'Vitamin']);
});

test('seeded default accounts exist with hashed passwords and can login', function () {
    $this->seed();

    $admin = User::where('username', 'admin')->first();
    $pimpinan = User::where('username', 'pimpinan')->first();

    expect($admin)->not->toBeNull();
    expect($pimpinan)->not->toBeNull();

    expect($admin->role)->toBe('Admin');
    expect($pimpinan->role)->toBe('Pimpinan');

    // Password wajib tersimpan sebagai hash Laravel, bukan plain text.
    expect(Hash::isHashed($admin->password))->toBeTrue();
    expect(Hash::isHashed($pimpinan->password))->toBeTrue();
    expect(Hash::check('admin123', $admin->password))->toBeTrue();
    expect(Hash::check('pimpinan123', $pimpinan->password))->toBeTrue();

    // Login Admin.
    $this->post(route('login.attempt'), [
        'username' => 'admin',
        'password' => 'admin123',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticated();

    // Logout.
    $this->post(route('logout'))->assertRedirect(route('login'));
    $this->assertGuest();

    // Login Pimpinan.
    $this->post(route('login.attempt'), [
        'username' => 'pimpinan',
        'password' => 'pimpinan123',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticated();
});

test('seeder is idempotent and can fix a broken password hash', function () {
    $this->seed();

    // Simulasikan akun dengan password rusak / plain text.
    User::where('username', 'admin')->update(['password' => 'salah-bukan-hash']);

    $this->seed();

    $admin = User::where('username', 'admin')->first();

    expect(Hash::check('admin123', $admin->password))->toBeTrue();
    expect(User::where('username', 'admin')->count())->toBe(1);
    expect(User::where('username', 'pimpinan')->count())->toBe(1);
});

test('registration page can be rendered by guests', function () {
    $this->get(route('register'))
        ->assertOk()
        ->assertSee('Buat akun baru');
});

test('guest can register, is logged in automatically, and can login again', function () {
    $this->post(route('register.store'), [
        'name' => 'Petugas Baru',
        'username' => 'petugas1',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticated();

    $user = User::where('username', 'petugas1')->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe('Admin');
    expect(Hash::isHashed($user->password))->toBeTrue();
    expect(Hash::check('rahasia123', $user->password))->toBeTrue();

    // Logout lalu login kembali memakai akun hasil registrasi.
    $this->post(route('logout'))->assertRedirect(route('login'));
    $this->assertGuest();

    $this->post(route('login.attempt'), [
        'username' => 'petugas1',
        'password' => 'rahasia123',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticated();
});

test('registration validates name, unique username and password confirmation', function () {
    // Semua field wajib diisi.
    $this->from(route('register'))
        ->post(route('register.store'), [])
        ->assertRedirect(route('register'))
        ->assertSessionHasErrors(['name', 'username', 'password']);

    // Username sudah dipakai.
    User::factory()->create(['username' => 'sudahada']);

    $this->from(route('register'))
        ->post(route('register.store'), [
            'name' => 'Pengguna',
            'username' => 'sudahada',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])
        ->assertRedirect(route('register'))
        ->assertSessionHasErrors('username');

    // Password minimal 8 karakter dan konfirmasi harus cocok.
    $this->from(route('register'))
        ->post(route('register.store'), [
            'name' => 'Pengguna',
            'username' => 'pengguna1',
            'password' => 'abc',
            'password_confirmation' => 'xyz',
        ])
        ->assertRedirect(route('register'))
        ->assertSessionHasErrors('password');

    $this->assertGuest();
});
