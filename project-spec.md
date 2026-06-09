# 📦 Spesifikasi Proyek: Sistem Inventaris RW

> **Versi:** 1.0.0  
> **Stack:** Laravel 11 · Filament v3 · MariaDB  
> **Scope:** Sistem pengelolaan inventaris barang milik RW, mencakup manajemen stok, peminjaman warga, pengembalian, denda, dan log aktivitas barang.

---

## 1. Deskripsi Proyek

Sistem Inventaris RW adalah aplikasi web berbasis admin panel yang digunakan oleh pengurus RW (admin) untuk mengelola aset/barang milik RW. Sistem ini mencakup pencatatan barang dengan multi-kondisi stok, proses peminjaman oleh warga, pengembalian dengan laporan kondisi, penghitungan denda otomatis, serta audit log setiap perubahan data barang.

---

## 2. Aktor & Peran

| Aktor | Deskripsi |
|---|---|
| **Admin (Pengurus RW)** | Satu-satunya pengguna sistem. Mengelola seluruh data barang, warga, peminjaman, dan pengembalian. |

> Sistem tidak memiliki portal warga. Semua transaksi dicatat oleh admin.

---

## 3. Fitur Utama

### 3.1 Manajemen Barang
- CRUD data barang (nama, keterangan, jumlah total).
- Setiap barang memiliki **3 entri stok** berdasarkan kondisi: `baik`, `rusak ringan`, `rusak berat`.
- Setiap perubahan data barang (tambah/edit/hapus) otomatis **mencatat log** ke tabel `log_barang`.

### 3.2 Manajemen Stok Barang
- Stok dikelola per kondisi, bukan sebagai satu angka tunggal.
- Jumlah stok berubah otomatis saat: barang ditambah, dipinjam, atau dikembalikan.
- Admin dapat menyesuaikan stok secara manual jika diperlukan (dengan log).

### 3.3 Manajemen Warga
- CRUD data warga: nama, NIK, alamat, no HP.
- Warga dapat memiliki riwayat peminjaman.

### 3.4 Peminjaman Barang
- Satu transaksi peminjaman (`Peminjaman`) dapat mencakup **banyak barang** dengan **banyak kondisi** melalui tabel `detail_peminjaman`.
- Setiap detail peminjaman merujuk ke `stok_barang` tertentu (stok kondisi tertentu dari suatu barang).
- Status peminjaman: `dipinjam` | `dikembalikan` | `terlambat` | `dikembalikan terlambat`.
- Admin mencatat tanggal pinjam dan tenggat pengembalian.

### 3.5 Pengembalian Barang
- Saat pengembalian, admin memilih berapa unit yang kembali dalam kondisi `baik`, `rusak ringan`, dan `rusak berat` **per item detail peminjaman**.
- Jumlah unit yang kembali per kondisi disimpan di kolom `jumlah_kembali_baik`, `jumlah_kembali_rusak_ringan`, dan `jumlah_kembali_rusak_berat` pada `detail_peminjaman`.
- Stok barang diperbarui otomatis sesuai kondisi kembali.

### 3.6 Denda
- Denda dibuat otomatis jika pengembalian melebihi `tenggat_pengembalian`.
- Status denda: `dibayar` | `belum dibayar`.
- Admin dapat menandai denda sebagai dibayar.

### 3.7 Log Barang
- Setiap mutasi stok atau perubahan data barang tercatat di `log_barang`.
- Kolom: `id_barang`, `kondisi`, `tipe` (`masuk`/`keluar`), `jumlah`, `keterangan`, `created_at`.

---

## 4. Skema Database

### Tabel: `users`
```sql
id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
nama          VARCHAR(100) NOT NULL
email         VARCHAR(100) UNIQUE NOT NULL
password      VARCHAR(255) NOT NULL
remember_token VARCHAR(100) NULL
timestamps
```

### Tabel: `warga`
```sql
id       BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
nama     VARCHAR(100) NOT NULL
NIK      VARCHAR(20) UNIQUE NOT NULL
alamat   TEXT NOT NULL
no_hp    VARCHAR(20) NOT NULL
timestamps
```

### Tabel: `barang`
```sql
id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
nama_barang   VARCHAR(150) NOT NULL
keterangan    TEXT NULL
jumlah_total  INT UNSIGNED NOT NULL DEFAULT 0
timestamps
```

### Tabel: `stok_barang`
```sql
id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
id_barang  BIGINT UNSIGNED NOT NULL FK → barang.id
kondisi    ENUM('baik', 'rusak_ringan', 'rusak_berat') NOT NULL
jumlah     INT UNSIGNED NOT NULL DEFAULT 0
UNIQUE(id_barang, kondisi)
timestamps
```
> Setiap barang otomatis memiliki 3 baris di tabel ini saat dibuat.

### Tabel: `log_barang`
```sql
id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
id_barang   BIGINT UNSIGNED NOT NULL FK → barang.id
kondisi     ENUM('baik', 'rusak_ringan', 'rusak_berat') NOT NULL
tipe        ENUM('masuk', 'keluar') NOT NULL
jumlah      INT UNSIGNED NOT NULL
keterangan  VARCHAR(255) NULL   -- misal: "Peminjaman #12", "Pengembalian #12", "Edit manual"
created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
```

### Tabel: `peminjaman`
```sql
id                   BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
id_warga             BIGINT UNSIGNED NOT NULL FK → warga.id
id_admin             BIGINT UNSIGNED NOT NULL FK → users.id
tanggal_pinjam       DATE NOT NULL
tenggat_pengembalian DATE NOT NULL
tanggal_kembali      DATE NULL
status               ENUM('dipinjam','dikembalikan','terlambat','dikembalikan_terlambat') DEFAULT 'dipinjam'
timestamps
```

### Tabel: `detail_peminjaman`
```sql
id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
id_peminjaman    BIGINT UNSIGNED NOT NULL FK → peminjaman.id
id_stok_barang   BIGINT UNSIGNED NOT NULL FK → stok_barang.id
jumlah           INT UNSIGNED NOT NULL
jumlah_kembali_baik          INT UNSIGNED NOT NULL DEFAULT 0
jumlah_kembali_rusak_ringan  INT UNSIGNED NOT NULL DEFAULT 0
jumlah_kembali_rusak_berat   INT UNSIGNED NOT NULL DEFAULT 0
timestamps
```

### Tabel: `denda`
```sql
id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
id_peminjaman  BIGINT UNSIGNED NOT NULL FK → peminjaman.id
jumlah         INT UNSIGNED NOT NULL   -- dalam Rupiah
status         ENUM('dibayar','belum_dibayar') DEFAULT 'belum_dibayar'
timestamps
```

---

## 5. Aturan Bisnis Penting

| # | Aturan |
|---|---|
| B-01 | Saat barang dibuat, sistem **otomatis membuat 3 baris** di `stok_barang` (baik, rusak_ringan, rusak_berat) dengan jumlah 0. |
| B-02 | Setiap perubahan stok harus **mencatat log** di `log_barang`. |
| B-03 | Peminjaman hanya bisa dilakukan jika **stok kondisi yang dipilih ≥ jumlah yang dipinjam**. |
| B-04 | Saat peminjaman dikonfirmasi, stok kondisi terkait **dikurangi** sejumlah yang dipinjam. |
| B-05 | Saat pengembalian, admin mengisi berapa unit kembali dalam kondisi `baik`, `rusak_ringan`, `rusak_berat`. Total harus **≤ jumlah yang dipinjam**. |
| B-06 | Stok diperbarui sesuai kondisi kembali (tambah stok kondisi masing-masing). |
| B-07 | Jika `tanggal_kembali > tenggat_pengembalian`, status jadi `dikembalikan_terlambat` dan **denda dibuat otomatis**. |
| B-08 | `jumlah_total` di tabel `barang` adalah **total semua stok** (baik + rusak_ringan + rusak_berat), diupdate via observer. |
| B-09 | Barang tidak bisa dihapus jika ada peminjaman aktif. |

---

## 6. Arsitektur Folder Laravel

```
app/
├── Filament/
│   └── Resources/
│       ├── BarangResource/
│       │   ├── BarangResource.php
│       │   └── Pages/
│       │       ├── CreateBarang.php
│       │       ├── EditBarang.php
│       │       └── ListBarang.php
│       ├── WargaResource/
│       ├── PeminjamanResource/
│       │   └── Pages/
│       │       ├── CreatePeminjaman.php
│       │       ├── EditPeminjaman.php
│       │       └── ListPeminjaman.php
│       ├── DendaResource/
│       └── LogBarangResource/  (read-only)
├── Models/
│   ├── User.php
│   ├── Warga.php
│   ├── Barang.php
│   ├── StokBarang.php
│   ├── LogBarang.php
│   ├── Peminjaman.php
│   ├── DetailPeminjaman.php
│   └── Denda.php
├── Observers/
│   ├── BarangObserver.php       -- trigger log saat barang dibuat/diedit
│   └── StokBarangObserver.php   -- trigger log saat stok berubah
├── Services/
│   ├── StokService.php          -- logika kurangi/tambah stok + logging
│   ├── PeminjamanService.php    -- buat peminjaman + validasi stok
│   └── PengembalianService.php  -- proses pengembalian + kalkulasi denda
└── Policies/
    └── (opsional, untuk multi-role masa depan)

database/
├── migrations/
│   ├── ..._create_warga_table.php
│   ├── ..._create_barang_table.php
│   ├── ..._create_stok_barang_table.php
│   ├── ..._create_log_barang_table.php
│   ├── ..._create_peminjaman_table.php
│   ├── ..._create_detail_peminjaman_table.php
│   └── ..._create_denda_table.php
└── seeders/
    ├── DatabaseSeeder.php
    └── AdminUserSeeder.php
```

---

## 7. Aturan Koding

### 7.1 Umum
- Ikuti **PSR-12** untuk style PHP.
- Gunakan **Form Request** untuk validasi input kompleks.
- Gunakan **Service Class** untuk logika bisnis (jangan taruh di Resource/Controller).
- Gunakan **Observer** untuk efek samping model (logging, update `jumlah_total`).
- Semua operasi stok harus melalui `StokService` — **jangan manipulasi stok langsung**.

### 7.2 Database
- Semua foreign key harus didefinisikan secara eksplisit di migration.
- Gunakan `DB::transaction()` untuk operasi yang melibatkan lebih dari satu tabel.
- Hindari N+1 query — selalu gunakan `with()` untuk eager loading di Filament.

### 7.3 Filament
- Gunakan `RelationManager` untuk detail peminjaman di dalam resource peminjaman.
- Gunakan `Actions` Filament untuk aksi kustom seperti "Proses Pengembalian" dan "Tandai Denda Lunas".
- Log barang ditampilkan sebagai resource **read-only** (tanpa Create/Edit/Delete).

### 7.4 Penamaan
- Model: `PascalCase` singular (`StokBarang`, `DetailPeminjaman`)
- Tabel DB: `snake_case` plural (`stok_barang`, `detail_peminjaman`)
- Service: `[Nama]Service.php`
- Observer: `[NamaModel]Observer.php`

---

## 8. Dependensi & Versi

| Paket | Versi |
|---|---|
| PHP | ^8.5 |
| Laravel | ^13.x |
| Filament | ^5.x |
| MariaDB | ^11.8 |
