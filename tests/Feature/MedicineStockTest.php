<?php

use App\Models\Category;
use App\Models\Medicine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

function makeAdmin(): User
{
    return User::factory()->create();
}

function medicinePayload(array $overrides = []): array
{
    return array_merge([
        'category_id' => 0,
        'name' => 'Paracetamol',
        'stock' => 50,
        'unit' => 'Tablet',
        'price' => 5000,
        'expiration_date' => '2030-06-30',
        'description' => 'Obat pereda nyeri',
    ], $overrides);
}

function makeCategoryAndMedicine(string $categoryName = 'Analgesik', array $medicineOverrides = []): array
{
    $category = Category::create(['name' => $categoryName]);
    $medicine = Medicine::create(array_merge(medicinePayload([
        'category_id' => $category->id,
    ]), $medicineOverrides));

    return [$category, $medicine];
}

test('admin can create a medicine and the initial stock is recorded', function () {
    $category = Category::create(['name' => 'Analgesik']);

    $response = $this->actingAs(makeAdmin())->post(route('medicines.store'), medicinePayload([
        'category_id' => $category->id,
    ]));

    $response->assertRedirect(route('medicines.index'));

    $medicine = Medicine::firstWhere('name', 'Paracetamol');
    expect($medicine)->not->toBeNull();
    expect($medicine->stock)->toBe(50);

    $this->assertDatabaseHas('stock_movements', [
        'medicine_id' => $medicine->id,
        'type' => 'in',
        'quantity' => 50,
    ]);
});

test('creating a medicine requires all mandatory fields', function () {
    $this->actingAs(makeAdmin())
        ->from(route('medicines.create'))
        ->post(route('medicines.store'), [])
        ->assertRedirect(route('medicines.create'))
        ->assertSessionHasErrors(['category_id', 'name', 'stock', 'unit', 'price', 'expiration_date']);

    $this->assertDatabaseCount('medicines', 0);
});

test('admin can update a medicine and stock changes are recorded', function () {
    [$category, $medicine] = makeCategoryAndMedicine();

    $response = $this->actingAs(makeAdmin())->put(route('medicines.update', $medicine), medicinePayload([
        'category_id' => $category->id,
        'name' => 'Paracetamol 500mg',
        'stock' => 60,
    ]));

    $response->assertRedirect(route('medicines.index'));

    $medicine->refresh();
    expect($medicine->name)->toBe('Paracetamol 500mg');
    expect($medicine->stock)->toBe(60);

    $this->assertDatabaseHas('stock_movements', [
        'medicine_id' => $medicine->id,
        'quantity' => 10,
    ]);
});

test('admin can delete a medicine with its stock history', function () {
    [, $medicine] = makeCategoryAndMedicine();

    $this->actingAs(makeAdmin())
        ->delete(route('medicines.destroy', $medicine))
        ->assertRedirect(route('medicines.index'));

    $this->assertDatabaseMissing('medicines', ['id' => $medicine->id]);
});

test('stock movements update the medicine stock automatically', function () {
    [, $medicine] = makeCategoryAndMedicine('Analgesik', ['stock' => 50]);

    // Stok awal = 50, masuk = 20, keluar = 10, stok akhir = 60.
    $this->actingAs(makeAdmin())->post(route('stocks.store'), [
        'medicine_id' => $medicine->id,
        'type' => 'in',
        'quantity' => 20,
        'description' => 'Pembelian supplier',
    ])->assertRedirect(route('stocks.index'));

    expect($medicine->fresh()->stock)->toBe(70);

    $this->actingAs(makeAdmin())->post(route('stocks.store'), [
        'medicine_id' => $medicine->id,
        'type' => 'out',
        'quantity' => 10,
        'description' => 'Distribusi ruang rawat inap',
    ])->assertRedirect(route('stocks.index'));

    expect($medicine->fresh()->stock)->toBe(60);

    $this->assertDatabaseHas('stock_movements', ['medicine_id' => $medicine->id, 'type' => 'in', 'quantity' => 20]);
    $this->assertDatabaseHas('stock_movements', ['medicine_id' => $medicine->id, 'type' => 'out', 'quantity' => 10]);
});

test('stock out cannot exceed the available stock', function () {
    [, $medicine] = makeCategoryAndMedicine('Analgesik', ['stock' => 5]);

    $this->from(route('stocks.index'))
        ->actingAs(makeAdmin())
        ->post(route('stocks.store'), [
            'medicine_id' => $medicine->id,
            'type' => 'out',
            'quantity' => 50,
        ])
        ->assertRedirect(route('stocks.index'))
        ->assertSessionHasErrors('quantity');

    expect($medicine->fresh()->stock)->toBe(5);
});

test('stock movement validates input', function () {
    [, $medicine] = makeCategoryAndMedicine();

    $this->actingAs(makeAdmin())
        ->post(route('stocks.store'), [
            'medicine_id' => $medicine->id,
            'type' => 'invalid',
            'quantity' => 0,
        ])
        ->assertSessionHasErrors(['type', 'quantity']);
});

test('admin can create and update a category', function () {
    $this->actingAs(makeAdmin())
        ->post(route('categories.store'), ['name' => 'Analgesik', 'description' => 'Obat nyeri'])
        ->assertRedirect(route('categories.index'));

    $this->assertDatabaseHas('categories', ['name' => 'Analgesik']);

    $category = Category::firstWhere('name', 'Analgesik');

    $this->actingAs(makeAdmin())
        ->post(route('categories.store'), ['name' => 'Analgesik'])
        ->assertSessionHasErrors('name');

    $this->actingAs(makeAdmin())
        ->put(route('categories.update', $category), ['name' => 'Analgesik & Nyeri'])
        ->assertRedirect(route('categories.index'));

    $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Analgesik & Nyeri']);
});

test('a category that still has medicines cannot be deleted', function () {
    [$category] = makeCategoryAndMedicine();

    $this->actingAs(makeAdmin())
        ->delete(route('categories.destroy', $category))
        ->assertStatus(302);

    $this->assertDatabaseHas('categories', ['id' => $category->id]);
});

test('an unused category can be deleted', function () {
    $category = Category::create(['name' => 'Vitamin']);

    $this->actingAs(makeAdmin())
        ->delete(route('categories.destroy', $category))
        ->assertStatus(302);

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

test('medicine list can be searched and filtered by category', function () {
    $analgesik = Category::create(['name' => 'Analgesik']);
    $antibiotik = Category::create(['name' => 'Antibiotik']);

    Medicine::create(medicinePayload(['category_id' => $analgesik->id, 'name' => 'Paracetamol']));
    Medicine::create(medicinePayload(['category_id' => $antibiotik->id, 'name' => 'Amoxicillin']));

    $this->actingAs(makeAdmin())
        ->get(route('medicines.index', ['q' => 'Paracetamol']))
        ->assertOk()
        ->assertSee('Paracetamol')
        ->assertDontSee('Amoxicillin');

    $this->actingAs(makeAdmin())
        ->get(route('medicines.index', ['category' => $antibiotik->id]))
        ->assertOk()
        ->assertSee('Amoxicillin')
        ->assertDontSee('Paracetamol');
});

test('low stock warning is shown for medicines at or below the threshold', function () {
    $category = Category::create(['name' => 'Analgesik']);
    Medicine::create(medicinePayload(['category_id' => $category->id, 'name' => 'Paracetamol', 'stock' => 50]));
    Medicine::create(medicinePayload(['category_id' => $category->id, 'name' => 'CTM', 'stock' => 10]));

    $this->actingAs(makeAdmin())
        ->get(route('medicines.index'))
        ->assertOk()
        ->assertSee('Stok Menipis')
        ->assertSee('Stok Aman');

    $this->actingAs(makeAdmin())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Stok Menipis');
});

test('report can be filtered by category and stock status', function () {
    $analgesik = Category::create(['name' => 'Analgesik']);
    $antibiotik = Category::create(['name' => 'Antibiotik']);

    Medicine::create(medicinePayload(['category_id' => $analgesik->id, 'name' => 'Paracetamol', 'stock' => 50]));
    Medicine::create(medicinePayload(['category_id' => $antibiotik->id, 'name' => 'CTM', 'stock' => 5]));

    $this->actingAs(makeAdmin())
        ->get(route('reports.index'))
        ->assertOk()
        ->assertSee('Paracetamol')
        ->assertSee('CTM')
        ->assertSee('Cetak Laporan');

    $this->actingAs(makeAdmin())
        ->get(route('reports.index', ['status' => 'low']))
        ->assertOk()
        ->assertSee('CTM')
        ->assertDontSee('Paracetamol');

    $this->actingAs(makeAdmin())
        ->get(route('reports.index', ['category' => $analgesik->id]))
        ->assertOk()
        ->assertSee('Paracetamol')
        ->assertDontSee('CTM');
});
