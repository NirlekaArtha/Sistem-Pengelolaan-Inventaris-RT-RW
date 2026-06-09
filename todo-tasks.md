# ✅ Todo Tasks: Sistem Inventaris RW

> Centang setiap task saat selesai. Kerjakan secara berurutan per fase.  
> Referensi spesifikasi lengkap di `project-spec.md`.

---

## 🔧 FASE 1 — Setup Proyek

### 1.1 Inisialisasi
- [x] Buat project Laravel baru: `composer create-project laravel/laravel inventaris-rw`
- [x] Konfigurasi `.env` untuk koneksi MariaDB (DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD)
- [x] Jalankan `php artisan key:generate`
- [x] Install Filament: `composer require filament/filament:"^3.x" -W`
- [x] Jalankan `php artisan filament:install --panels`
- [x] Buat panel admin Filament dengan ID `admin`
- [x] Test akses `/admin` di browser, pastikan halaman login muncul

### 1.2 Konfigurasi
- [x] Set timezone ke `Asia/Jakarta` di `config/app.php`
- [x] Set locale ke `id` di `config/app.php`
- [x] Tambahkan konfigurasi koneksi MariaDB yang tepat (strict mode, charset utf8mb4)
- [x] Buat `AdminUserSeeder` untuk membuat akun admin awal
- [x] Jalankan seeder, pastikan login berhasil ke Filament

---

## 🗄️ FASE 2 — Database & Model

### 2.1 Migrations (buat & jalankan berurutan)
- [x] Migration `create_warga_table` — kolom: id, nama, NIK (unique), alamat, no_hp, timestamps
- [x] Migration `create_barang_table` — kolom: id, nama_barang, keterangan (nullable), jumlah_total (default 0), timestamps
- [x] Migration `create_stok_barang_table` — kolom: id, id_barang (FK), kondisi (enum), jumlah (default 0), timestamps. Tambahkan unique(id_barang, kondisi)
- [x] Migration `create_log_barang_table` — kolom: id, id_barang (FK), kondisi (enum), tipe (enum masuk/keluar), jumlah, keterangan (nullable), created_at
- [x] Migration `create_peminjaman_table` — kolom: id, id_warga (FK), id_admin (FK→users), tanggal_pinjam, tenggat_pengembalian, tanggal_kembali (nullable), status (enum), timestamps
- [x] Migration `create_detail_peminjaman_table` — kolom: id, id_peminjaman (FK), id_stok_barang (FK), jumlah, jumlah_kembali_baik, jumlah_kembali_rusak_ringan, jumlah_kembali_rusak_berat, timestamps
- [x] Migration `create_denda_table` — kolom: id, id_peminjaman (FK), jumlah, status (enum), timestamps
- [x] Jalankan `php artisan migrate` — pastikan semua tabel terbuat tanpa error

### 2.2 Models & Relationships
- [x] Model `Warga` — hasMany Peminjaman
- [x] Model `Barang` — hasMany StokBarang, hasMany LogBarang. Tambahkan `$fillable` lengkap
- [x] Model `StokBarang` — belongsTo Barang. Tambahkan cast enum kondisi
- [x] Model `LogBarang` — belongsTo Barang. Set `$timestamps = false`, gunakan hanya `created_at`
- [x] Model `Peminjaman` — belongsTo Warga, belongsTo User, hasMany DetailPeminjaman, hasOne Denda
- [x] Model `DetailPeminjaman` — belongsTo Peminjaman, belongsTo StokBarang (dengan relasi ke Barang via StokBarang)
- [x] Model `Denda` — belongsTo Peminjaman
- [x] Model `User` — tambahkan relasi hasMany Peminjaman

---

## ⚙️ FASE 3 — Business Logic (Services & Observers)

### 3.1 Observers
- [x] Buat `BarangObserver`:
  - [x] `created`: panggil StokService untuk buat 3 baris stok (baik, rusak_ringan, rusak_berat) dengan jumlah 0
  - [x] `updated`: catat log perubahan data barang ke `log_barang` dengan tipe & keterangan yang sesuai
- [x] Buat `StokBarangObserver`:
  - [x] `updated`: hitung ulang dan update `jumlah_total` di tabel `barang` (sum semua kondisi stok)
- [x] Daftarkan kedua observer di `AppServiceProvider`

### 3.2 StokService
- [x] Buat `app/Services/StokService.php`
- [x] Method `inisialisasiStok(Barang $barang)`: buat 3 baris stok awal
- [x] Method `tambahStok(StokBarang $stok, int $jumlah, string $keterangan)`: tambah stok + catat log `masuk`
- [x] Method `kurangiStok(StokBarang $stok, int $jumlah, string $keterangan)`: validasi stok cukup, kurangi stok + catat log `keluar`
- [x] Method `cekKetersediaan(StokBarang $stok, int $jumlah): bool`: return true jika stok cukup

### 3.3 PeminjamanService
- [x] Buat `app/Services/PeminjamanService.php`
- [x] Method `buatPeminjaman(array $data, array $details)`: dalam satu transaksi DB:
  - [x] Validasi stok cukup untuk setiap item detail
  - [x] Insert `peminjaman`
  - [x] Insert setiap `detail_peminjaman`
  - [x] Kurangi stok via `StokService::kurangiStok()`

### 3.4 PengembalianService
- [x] Buat `app/Services/PengembalianService.php`
- [x] Method `prosesKembali(Peminjaman $peminjaman, array $kondisiKembali)`: dalam satu transaksi DB:
  - [x] Update setiap `detail_peminjaman` dengan data kondisi & jumlah kembali
  - [x] Tambah stok sesuai kondisi kembali via `StokService::tambahStok()`
  - [x] Update `tanggal_kembali` di peminjaman
  - [x] Tentukan status: `dikembalikan` atau `dikembalikan_terlambat`
  - [x] Jika terlambat, buat record `Denda` otomatis

---

## 🖥️ FASE 4 — Filament Resources

### 4.1 Resource: Warga
- [x] Generate: `php artisan make:filament-resource Warga --generate`
- [x] Form: input nama, NIK, alamat, no_hp. Tambahkan validasi NIK unique
- [x] Table: kolom nama, NIK, alamat, no_hp. Tambahkan searchable dan sortable
- [x] Tambahkan action "Lihat Riwayat Peminjaman" di table rows

### 4.2 Resource: Barang
- [x] Generate: `php artisan make:filament-resource Barang --generate`
- [x] Form: input nama_barang, keterangan (textarea). Hilangkan input `jumlah_total` (dihitung otomatis)
- [x] Table: kolom nama_barang, keterangan, jumlah_total (stok baik / rusak ringan / rusak berat ditampilkan terpisah)
- [x] Tambahkan `RelationManager` untuk `StokBarang` (read-only display per kondisi)
- [x] Tambahkan `RelationManager` untuk `LogBarang` (read-only, sorted by created_at desc)
- [x] Tambahkan validasi: barang tidak bisa dihapus jika ada peminjaman aktif (override `canDelete`)

### 4.3 Resource: Peminjaman
- [x] Generate: `php artisan make:filament-resource Peminjaman --generate`
- [x] Form `CreatePeminjaman`:
  - [x] Select warga (searchable)
  - [x] DatePicker tanggal_pinjam (default today)
  - [x] DatePicker tenggat_pengembalian
  - [x] Repeater untuk `detail_peminjaman`: pilih barang → pilih kondisi stok → input jumlah
  - [x] Saat pilih barang + kondisi, tampilkan stok tersedia secara live (menggunakan `afterStateUpdated`)
- [x] Table: kolom warga.nama, tanggal_pinjam, tenggat_pengembalian, status (dengan badge warna)
- [x] Tambahkan filter status di table
- [x] Tambahkan Action "Proses Pengembalian":
  - [x] Modal dengan form per-item: input jumlah kembali kondisi baik/rusak_ringan/rusak_berat
  - [x] Validasi total kembali ≤ jumlah dipinjam
  - [x] Submit memanggil `PengembalianService::prosesKembali()`
- [x] View page untuk detail peminjaman (tampilkan semua detail, denda jika ada)

### 4.4 Resource: Denda
- [x] Generate: `php artisan make:filament-resource Denda --generate`
- [x] Table: kolom peminjaman (nama warga + tanggal), jumlah, status (badge)
- [x] Tambahkan Action "Tandai Lunas" — update status ke `dibayar`
- [x] Sembunyikan tombol Create (denda dibuat otomatis oleh sistem)

### 4.5 Resource: Log Barang (Read-only)
- [x] Generate: `php artisan make:filament-resource LogBarang`
- [x] Table: kolom barang.nama_barang, kondisi, tipe (badge masuk=hijau/keluar=merah), jumlah, keterangan, created_at
- [x] Tambahkan filter: per barang, per kondisi, per tipe, per rentang tanggal
- [x] Hapus halaman Create, Edit, Delete — hanya List dan View

---

## 🔒 FASE 5 — Validasi & Keamanan

- [ ] Pastikan semua form Filament memiliki validasi server-side yang lengkap
- [ ] Tambahkan validasi stok di `PeminjamanService` (stok tidak bisa negatif)
- [ ] Bungkus semua operasi multi-tabel dengan `DB::transaction()`
- [ ] Test edge case: peminjaman dengan jumlah = stok penuh
- [ ] Test edge case: pengembalian parsial (tidak semua item dikembalikan sekaligus — scope V2)
- [ ] Tambahkan konfirmasi dialog untuk action yang destruktif (hapus barang, tandai lunas)

---

## 🎨 FASE 6 — UX & Polish

- [ ] Konfigurasi navigasi Filament: grouping menu (Inventaris, Transaksi, Laporan)
- [ ] Tambahkan badge count di navigasi (misal: jumlah denda belum dibayar)
- [ ] Tambahkan widget di Dashboard:
  - [ ] Total barang aktif
  - [ ] Peminjaman aktif hari ini
  - [ ] Denda belum dibayar (jumlah & total nominal)
  - [ ] Tabel barang dengan stok rendah (< threshold)
- [ ] Sesuaikan branding Filament: nama panel "Inventaris RW", logo/icon
- [ ] Pastikan semua label form & kolom tabel menggunakan Bahasa Indonesia

---

## 🧪 FASE 7 — Testing

- [ ] Tulis `Feature Test` untuk alur peminjaman (buat → cek stok berkurang)
- [ ] Tulis `Feature Test` untuk alur pengembalian (kembali → cek stok bertambah)
- [ ] Tulis `Unit Test` untuk `StokService::kurangiStok()` (termasuk case stok tidak cukup)
- [ ] Tulis `Unit Test` untuk `PengembalianService` kalkulasi status terlambat
- [ ] Tulis `Unit Test` untuk `BarangObserver` — pastikan 3 stok terbuat saat barang dibuat
- [ ] Jalankan `php artisan test` — pastikan semua test hijau

---

## 🚀 FASE 8 — Deployment

- [ ] Buat file `.env.production` dengan konfigurasi production (DB, APP_ENV=production, APP_DEBUG=false)
- [ ] Jalankan `php artisan config:cache`, `route:cache`, `view:cache`
- [ ] Konfigurasi web server (Nginx/Apache) dengan vhost yang benar
- [ ] Pastikan `storage/` dan `bootstrap/cache/` writable oleh web server
- [ ] Jalankan `php artisan migrate --force` di server production
- [ ] Jalankan `AdminUserSeeder` di production
- [ ] Test fungsional end-to-end di environment production:
  - [ ] Login admin berhasil
  - [ ] Tambah barang → cek 3 stok & log terbuat
  - [ ] Buat peminjaman → cek stok berkurang
  - [ ] Proses pengembalian → cek stok bertambah & denda muncul jika terlambat
- [ ] Setup backup database otomatis (cron harian)
- [ ] Setup monitoring error (opsional: Laravel Telescope atau Sentry)

---

## 📝 Catatan & Backlog (V2)

> Item berikut di luar scope V1, bisa dikerjakan di versi berikutnya.

- [ ] Export laporan peminjaman ke PDF/Excel
- [ ] Pengembalian parsial (item dikembalikan bertahap dalam beberapa kali)
- [ ] Notifikasi (email/WhatsApp) H-1 sebelum tenggat pengembalian
- [ ] Portal warga (view riwayat peminjaman sendiri)
- [ ] Kalkulasi denda otomatis per hari dengan tarif yang bisa dikonfigurasi
- [ ] Foto barang (menggunakan Spatie Media Library)
- [ ] Multi-admin dengan role berbeda (Ketua RW vs Sekretaris)
