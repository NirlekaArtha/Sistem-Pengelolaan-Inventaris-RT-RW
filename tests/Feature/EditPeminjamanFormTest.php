<?php

namespace Tests\Feature;

use App\Filament\Resources\Peminjamen\Pages\EditPeminjaman;
use App\Models\User;
use App\Models\Warga;
use App\Models\Barang;
use App\Models\StokBarang;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Enums\KondisiBarang;
use App\Enums\StatusPeminjaman;
use App\Services\StokService;
use App\Services\PeminjamanService;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EditPeminjamanFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_peminjaman_form_calculates_correct_stok_tersedia()
    {
        // 1. Setup Admin, Warga, and Barang
        $admin = User::factory()->create();
        $warga = Warga::factory()->create();
        
        $barang = Barang::create(['nama_barang' => 'Tenda', 'jumlah_total' => 0]);
        $stokBaik = $barang->stokBarang()->where('kondisi', KondisiBarang::BAIK)->first();
        
        $stokService = app(StokService::class);
        $stokService->tambahStok($stokBaik, 3, 'Restock'); // total stock = 3

        $peminjamanService = app(PeminjamanService::class);
        $loanData = [
            'id_warga' => $warga->id,
            'id_admin' => $admin->id,
            'tanggal_pinjam' => today()->toDateString(),
            'tenggat_pengembalian' => today()->addDays(7)->toDateString(),
        ];

        // Borrow 2 units. stock_tersedia will become 1.
        $details = [
            [
                'id_stok_barang' => $stokBaik->id,
                'jumlah' => 2,
            ]
        ];
        $peminjaman = $peminjamanService->buatPeminjaman($loanData, $details);

        // Verify database state before edit
        $stokBaik->refresh();
        $this->assertEquals(1, $stokBaik->stok_tersedia);

        // 2. Authenticate admin and mount Edit page
        $this->actingAs($admin);

        $test = Livewire::test(EditPeminjaman::class, [
            'record' => $peminjaman->getKey(),
        ]);

        // Verify that the initial stok_tersedia on the form is correctly calculated
        $details = $test->instance()->data['detailPeminjaman'];
        $uuid = array_key_first($details);
        
        $this->assertEquals(3, $details[$uuid]['stok_tersedia']);

        // 3. Try to increase borrowed quantity to 3 (which is within the recalculated available stock of 3)
        $test->fillForm([
            'detailPeminjaman' => [
                $uuid => [
                    'id_barang' => $barang->id,
                    'kondisi' => KondisiBarang::BAIK->value,
                    'id_stok_barang' => $stokBaik->id,
                    'stok_tersedia' => 3,
                    'jumlah' => 3,
                ]
            ]
        ])
        ->call('save')
        ->assertHasNoFormErrors();

        // 4. Verify updated state in the database
        $stokBaik->refresh();
        $this->assertEquals(0, $stokBaik->stok_tersedia); // 3 - 3 = 0
        $this->assertEquals(3, $stokBaik->jumlah_total);

        $this->assertDatabaseHas('detail_peminjaman', [
            'id_peminjaman' => $peminjaman->id,
            'id_stok_barang' => $stokBaik->id,
            'jumlah' => 3,
        ]);
    }
}
