<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Warga;
use App\Models\Barang;
use App\Models\StokBarang;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\Denda;
use App\Models\LogBarang;
use App\Enums\KondisiBarang;
use App\Enums\TipeLogBarang;
use App\Enums\StatusPeminjaman;
use App\Enums\StatusDenda;
use App\Services\StokService;
use App\Services\PeminjamanService;
use App\Services\PengembalianService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $stokService;
    protected $peminjamanService;
    protected $pengembalianService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->stokService = $this->app->make(StokService::class);
        $this->peminjamanService = $this->app->make(PeminjamanService::class);
        $this->pengembalianService = $this->app->make(PengembalianService::class);
    }

    public function test_automatically_creates_three_stock_rows_when_barang_is_created()
    {
        $barang = Barang::create([
            'nama_barang' => 'Kursi Mewah',
            'keterangan' => 'Kursi untuk acara formal',
            'jumlah_total' => 0,
        ]);

        $this->assertDatabaseCount('stok_barang', 3);
        $this->assertDatabaseHas('stok_barang', [
            'id_barang' => $barang->id,
            'kondisi' => KondisiBarang::BAIK->value,
            'jumlah' => 0,
        ]);
        $this->assertDatabaseHas('stok_barang', [
            'id_barang' => $barang->id,
            'kondisi' => KondisiBarang::RUSAK_RINGAN->value,
            'jumlah' => 0,
        ]);
        $this->assertDatabaseHas('stok_barang', [
            'id_barang' => $barang->id,
            'kondisi' => KondisiBarang::RUSAK_BERAT->value,
            'jumlah' => 0,
        ]);

        $this->assertDatabaseHas('log_barang', [
            'id_barang' => $barang->id,
            'keterangan' => 'Barang baru ditambahkan ke sistem',
        ]);
    }

    public function test_can_add_stock_and_creates_log()
    {
        $barang = Barang::create(['nama_barang' => 'Tenda', 'jumlah_total' => 0]);
        $stokBaik = $barang->stokBarang()->where('kondisi', KondisiBarang::BAIK)->first();

        $this->stokService->tambahStok($stokBaik, 10, 'Restock awal');

        $stokBaik->refresh();
        $barang->refresh();

        $this->assertEquals(10, $stokBaik->jumlah);
        $this->assertEquals(10, $barang->jumlah_total);

        $this->assertDatabaseHas('log_barang', [
            'id_barang' => $barang->id,
            'kondisi' => KondisiBarang::BAIK->value,
            'tipe' => TipeLogBarang::MASUK->value,
            'jumlah' => 10,
            'keterangan' => 'Restock awal',
        ]);
    }

    public function test_can_reduce_stock_and_creates_log()
    {
        $barang = Barang::create(['nama_barang' => 'Tenda', 'jumlah_total' => 0]);
        $stokBaik = $barang->stokBarang()->where('kondisi', KondisiBarang::BAIK)->first();
        
        $this->stokService->tambahStok($stokBaik, 10, 'Restock awal');

        $this->stokService->kurangiStok($stokBaik, 4, 'Dipakai');

        $stokBaik->refresh();
        $barang->refresh();

        $this->assertEquals(6, $stokBaik->jumlah);
        $this->assertEquals(6, $barang->jumlah_total);

        $this->assertDatabaseHas('log_barang', [
            'id_barang' => $barang->id,
            'kondisi' => KondisiBarang::BAIK->value,
            'tipe' => TipeLogBarang::KELUAR->value,
            'jumlah' => 4,
            'keterangan' => 'Dipakai',
        ]);
    }

    public function test_fails_to_reduce_stock_if_insufficient()
    {
        $barang = Barang::create(['nama_barang' => 'Tenda', 'jumlah_total' => 0]);
        $stokBaik = $barang->stokBarang()->where('kondisi', KondisiBarang::BAIK)->first();
        
        $this->stokService->tambahStok($stokBaik, 5, 'Restock');

        $this->expectException(InvalidArgumentException::class);
        $this->stokService->kurangiStok($stokBaik, 10, 'Dipinjam banyak');
    }

    public function test_can_process_loan_successfully()
    {
        $admin = User::factory()->create();
        $warga = Warga::factory()->create();
        
        $barang = Barang::create(['nama_barang' => 'Tenda', 'jumlah_total' => 0]);
        $stokBaik = $barang->stokBarang()->where('kondisi', KondisiBarang::BAIK)->first();
        $this->stokService->tambahStok($stokBaik, 10, 'Restock');

        $loanData = [
            'id_warga' => $warga->id,
            'id_admin' => $admin->id,
            'tanggal_pinjam' => today()->toDateString(),
            'tenggat_pengembalian' => today()->addDays(7)->toDateString(),
        ];

        $details = [
            [
                'id_stok_barang' => $stokBaik->id,
                'jumlah' => 3,
            ]
        ];

        $peminjaman = $this->peminjamanService->buatPeminjaman($loanData, $details);

        $this->assertNotNull($peminjaman);
        $stokBaik->refresh();
        $this->assertEquals(7, $stokBaik->jumlah);

        $this->assertDatabaseHas('peminjaman', [
            'id' => $peminjaman->id,
            'id_warga' => $warga->id,
            'status' => StatusPeminjaman::DIPINJAM->value,
        ]);

        $this->assertDatabaseHas('detail_peminjaman', [
            'id_peminjaman' => $peminjaman->id,
            'id_stok_barang' => $stokBaik->id,
            'jumlah' => 3,
        ]);
    }

    public function test_can_process_return_successfully_and_adds_stok()
    {
        $admin = User::factory()->create();
        $warga = Warga::factory()->create();
        
        $barang = Barang::create(['nama_barang' => 'Tenda', 'jumlah_total' => 0]);
        $stokBaik = $barang->stokBarang()->where('kondisi', KondisiBarang::BAIK)->first();
        $this->stokService->tambahStok($stokBaik, 10, 'Restock');

        $peminjaman = $this->peminjamanService->buatPeminjaman([
            'id_warga' => $warga->id,
            'id_admin' => $admin->id,
            'tanggal_pinjam' => today()->toDateString(),
            'tenggat_pengembalian' => today()->addDays(7)->toDateString(),
        ], [
            [
                'id_stok_barang' => $stokBaik->id,
                'jumlah' => 3,
            ]
        ]);

        $detail = $peminjaman->detailPeminjaman->first();

        $kondisiKembali = [
            $detail->id => [
                'baik' => 2,
                'rusak_ringan' => 1,
                'rusak_berat' => 0,
            ]
        ];

        $peminjaman = $this->pengembalianService->prosesKembali($peminjaman, $kondisiKembali, today()->toDateString());

        $this->assertEquals(StatusPeminjaman::DIKEMBALIKAN, $peminjaman->status);

        $stokBaik->refresh();
        $stokRusakRingan = $barang->stokBarang()->where('kondisi', KondisiBarang::RUSAK_RINGAN)->first();

        $this->assertEquals(9, $stokBaik->jumlah); // 7 + 2 = 9
        $this->assertEquals(1, $stokRusakRingan->jumlah); // 0 + 1 = 1
    }

    public function test_automatically_creates_fine_if_return_is_late()
    {
        $admin = User::factory()->create();
        $warga = Warga::factory()->create();
        
        $barang = Barang::create(['nama_barang' => 'Tenda', 'jumlah_total' => 0]);
        $stokBaik = $barang->stokBarang()->where('kondisi', KondisiBarang::BAIK)->first();
        $this->stokService->tambahStok($stokBaik, 10, 'Restock');

        $peminjaman = $this->peminjamanService->buatPeminjaman([
            'id_warga' => $warga->id,
            'id_admin' => $admin->id,
            'tanggal_pinjam' => today()->toDateString(),
            'tenggat_pengembalian' => today()->addDays(7)->toDateString(),
        ], [
            [
                'id_stok_barang' => $stokBaik->id,
                'jumlah' => 2,
            ]
        ]);

        $detail = $peminjaman->detailPeminjaman->first();

        $kondisiKembali = [
            $detail->id => [
                'baik' => 2,
                'rusak_ringan' => 0,
                'rusak_berat' => 0,
            ]
        ];

        $lateDate = today()->addDays(10)->toDateString();
        $peminjaman = $this->pengembalianService->prosesKembali($peminjaman, $kondisiKembali, $lateDate);

        $this->assertEquals(StatusPeminjaman::DIKEMBALIKAN_TERLAMBAT, $peminjaman->status);
        
        $this->assertDatabaseHas('denda', [
            'id_peminjaman' => $peminjaman->id,
            'jumlah' => 15000, // 3 days late * 5000 = 15000
            'status' => StatusDenda::BELUM_DIBAYAR->value,
        ]);
    }
}
