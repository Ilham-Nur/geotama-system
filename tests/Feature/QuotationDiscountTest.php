<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\Concerns\UsesIsolatedTestDatabase;
use Tests\TestCase;

class QuotationDiscountTest extends TestCase
{
    use UsesIsolatedTestDatabase;

    public function test_discount_is_subtracted_from_quotation_grand_total(): void
    {
        Storage::fake('public');

        $permission = Permission::create(['name' => 'quotation.create']);
        $user = User::factory()->create(['username' => 'quotation-discount']);
        $user->givePermissionTo($permission);
        $client = Client::create([
            'nama_perusahaan' => 'Client Discount',
            'alamat' => 'Batam',
            'nama_pic' => 'PIC Client',
            'no_telp' => '08123456789',
            'email' => 'client@example.com',
        ]);

        $this->actingAs($user)
            ->post(route('quotation.store'), [
                'no_quo' => 'GGI-QUO-2026-9998',
                'tanggal' => '2026-08-13',
                'client_id' => $client->id,
                'client_mode' => 'existing',
                'discount' => 25000,
                'items' => [
                    [
                        'description' => 'Jasa inspeksi',
                        'satuan' => 'Lot',
                        'qty' => 1,
                        'total' => 100000,
                    ],
                    [
                        'description' => 'Transportasi',
                        'satuan' => 'Trip',
                        'qty' => 1,
                        'total' => 50000,
                    ],
                ],
            ])
            ->assertRedirect(route('quotation.index'));

        $this->assertDatabaseHas('quotations', [
            'no_quo' => 'GGI-QUO-2026-9998',
            'discount' => 25000,
            'grand_total_quo' => 125000,
        ]);
    }

    public function test_discount_cannot_exceed_quotation_subtotal(): void
    {
        $permission = Permission::create(['name' => 'quotation.create']);
        $user = User::factory()->create(['username' => 'quotation-invalid-discount']);
        $user->givePermissionTo($permission);
        $client = Client::create([
            'nama_perusahaan' => 'Client Invalid Discount',
            'alamat' => 'Batam',
            'nama_pic' => 'PIC Client',
            'no_telp' => '08123456789',
            'email' => 'invalid@example.com',
        ]);

        $this->actingAs($user)
            ->from(route('quotation.create'))
            ->post(route('quotation.store'), [
                'no_quo' => 'GGI-QUO-2026-9999',
                'tanggal' => '2026-08-13',
                'client_id' => $client->id,
                'client_mode' => 'existing',
                'discount' => 100001,
                'items' => [[
                    'description' => 'Jasa inspeksi',
                    'satuan' => 'Lot',
                    'qty' => 1,
                    'total' => 100000,
                ]],
            ])
            ->assertRedirect(route('quotation.create'))
            ->assertSessionHasErrors('discount');

        $this->assertDatabaseMissing('quotations', [
            'no_quo' => 'GGI-QUO-2026-9999',
        ]);
    }
}
