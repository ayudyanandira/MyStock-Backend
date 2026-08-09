<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Role;
use App\Models\Satuan;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PenerimaanTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_a_receipt_with_photo(): void
    {
        Storage::fake('public');
        [$user, $supplier, $barang] = $this->makeData();
        Sanctum::actingAs($user);

        $response = $this->post('/api/penerimaan', [
            'supplier_id' => $supplier->id,
            'tanggal' => '2026-07-28',
            'items' => [[
                'barang_id' => $barang->id,
                'jumlah' => 5,
                'harga_satuan' => 12500,
                'catatan' => 'Diterima lengkap',
            ]],
            'photos' => [UploadedFile::fake()->image('bukti.png')],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.detail.0.jumlah', '5.00')
            ->assertJsonPath('data.detail.0.subtotal', '62500.00');

        $this->assertDatabaseHas('detail_penerimaan', [
            'barang_id' => $barang->id,
            'jumlah' => 5,
            'harga_satuan' => 12500,
            'subtotal' => 62500,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'barang_id' => $barang->id,
            'qty_in' => 5,
            'qty_out' => 0,
            'created_by' => $user->id,
        ]);
        $this->assertDatabaseHas('foto_penerimaan', [
            'nama_file' => 'bukti.png',
            'mime_type' => 'image/png',
            'uploaded_by' => $user->id,
        ]);
        $this->assertSame(5, $barang->fresh()->stok);
    }

    public function test_receipt_update_recalculates_stock_and_subtotal(): void
    {
        [$user, $supplier, $barang] = $this->makeData();
        Sanctum::actingAs($user);

        $created = $this->postJson('/api/penerimaan', $this->payload($supplier, $barang, 5, 10000));
        $id = $created->json('data.id');

        $response = $this->putJson("/api/penerimaan/{$id}", $this->payload($supplier, $barang, 8, 15000));

        $response->assertOk()
            ->assertJsonPath('data.detail.0.jumlah', '8.00')
            ->assertJsonPath('data.detail.0.subtotal', '120000.00');
        $this->assertSame(8, $barang->fresh()->stok);
        $this->assertDatabaseCount('stock_movements', 1);
    }

    public function test_receipt_destroy_reverses_stock_and_removes_its_records(): void
    {
        [$user, $supplier, $barang] = $this->makeData();
        Sanctum::actingAs($user);
        $id = $this->postJson('/api/penerimaan', $this->payload($supplier, $barang, 3, 10000))->json('data.id');

        $this->deleteJson("/api/penerimaan/{$id}")->assertOk();

        $this->assertDatabaseMissing('penerimaan', ['id' => $id]);
        $this->assertDatabaseCount('detail_penerimaan', 0);
        $this->assertDatabaseCount('stock_movements', 0);
        $this->assertSame(0, $barang->fresh()->stok);
    }

    public function test_receipt_requires_authentication_and_unique_item_rows(): void
    {
        [, $supplier, $barang] = $this->makeData();
        $this->postJson('/api/penerimaan', $this->payload($supplier, $barang, 1, 1000))->assertUnauthorized();

        $user = User::first();
        Sanctum::actingAs($user);
        $payload = $this->payload($supplier, $barang, 1, 1000);
        $payload['items'][] = $payload['items'][0];

        $this->postJson('/api/penerimaan', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('items.0.barang_id');
    }

    private function makeData(): array
    {
        $role = Role::create(['name' => 'Admin']);
        $user = User::create([
            'role_id' => $role->id,
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
        ]);
        $kategori = Kategori::create(['nama' => 'Bahan Baku']);
        $satuan = Satuan::create(['nama' => 'Pcs']);
        $barang = Barang::create([
            'nama_barang' => 'Beras',
            'kategori_id' => $kategori->id,
            'satuan_id' => $satuan->id,
            'stok' => 0,
            'stok_minimum' => 0,
        ]);
        $supplier = Supplier::create(['nama_supplier' => 'PT Pangan']);

        return [$user, $supplier, $barang];
    }

    private function payload(Supplier $supplier, Barang $barang, int $jumlah, int $hargaSatuan): array
    {
        return [
            'supplier_id' => $supplier->id,
            'tanggal' => '2026-07-28',
            'items' => [[
                'barang_id' => $barang->id,
                'jumlah' => $jumlah,
                'harga_satuan' => $hargaSatuan,
            ]],
        ];
    }
}
