# SIMPEG APP - Sistem Kepegawaian ASN

SIMPEG APP adalah aplikasi web berbasis Laravel untuk mengelola data kepegawaian ASN, mulai dari data master, biodata pegawai, riwayat keluarga, pendidikan, jabatan, pangkat, Diklat, TPP, KGB, rekapitulasi, report, dashboard, hingga profil mandiri pegawai.

Repository ini sudah dilengkapi dengan:
- fitur **Rencana Diklat vs Realisasi Diklat**
- report gap Diklat per pegawai dan per unit
- analytics Diklat di dashboard
- summary Diklat pada profil pegawai
- **comprehensive dummy data seeder** agar aplikasi langsung terisi data lintas modul

---

## Panduan AI Coding Agent

Jika Anda AI coding agent (Claude Code, Codex, Cursor, dll) yang bekerja di
repo ini, taruh pointer berikut di file entry-point Anda (`AGENTS.md`,
`CLAUDE.md`, `GEMINI.md`):

```md
## Design & UI
Jika tugas berhubungan dengan membuat atau mengedit UI/UX, baca
`DESIGN.md` (arah gaya) lalu `ANTISLOP.md` (filter anti-slop) sebelum
membuat apa pun. Ikuti pola komponen yang sudah ada di aplikasi.
```

- **`DESIGN.md`** — arah desain SIMPEG (identitas, palet, tipografi, pola
  komponen yang sudah ada, nada copy Bahasa Indonesia). Milik repo ini.
- **`ANTISLOP.md`** — filter anti "AI slop" dari
  [miqdadbadjuber/anti-slop](https://github.com/miqdadbadjuber/anti-slop)
  (MIT). Mencegah UI generik ala AI (gradien biru-ungu, buzzword, tombol
  tidak berfungsi, dll) tanpa memaksakan gaya tertentu.

---

## 1. Fitur utama

### Superadmin
- Kelola master data (instansi, sekretariat, unit kerja)
- Kelola user admin dan user pegawai
- Kelola data pegawai dan hampir seluruh modul kepegawaian
- Akses seluruh dashboard, report, rekapitulasi, backup, dan analytics lintas unit

### Admin
- Kelola pegawai dalam unitnya sendiri
- Kelola data kepegawaian, Diklat, TPP, KGB, report, dan rekap dalam scope unitnya

### Pegawai
- Login dan melihat profil sendiri
- Melihat ringkasan Diklat sendiri
- Mengakses halaman self-service sesuai scope pegawai

---

## 2. Tech stack

- PHP 8.2+
- Laravel 11
- MySQL
- Livewire 3
- Jetstream
- Tailwind CSS
- Vite
- Chart.js
- Laravel Excel (`maatwebsite/excel`)

---

## 3. Prasyarat instalasi

Pastikan environment kamu sudah memiliki:

- **PHP 8.2 atau lebih baru**
- **Composer**
- **Node.js 18+**
- **npm**
- **MySQL**
- **Git**

Opsional tapi sangat membantu di Windows:
- Laragon / XAMPP / WAMP untuk PHP + MySQL

---

## 4. Cara instalasi project

### 4.1 Clone repository

Jika kamu memakai repository baru milikmu sendiri:

```bash
git clone https://github.com/dafahuda/MY-SIMPEG-APP.git
cd MY-SIMPEG-APP
```

Jika kamu masih bekerja lokal tanpa remote baru, cukup masuk ke folder project ini.

### 4.2 Install dependency backend

```bash
composer install
```

### 4.3 Install dependency frontend

```bash
npm install
```

### 4.4 Buat file environment

Linux / macOS:

```bash
cp .env.example .env
```

Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

### 4.5 Generate application key

```bash
php artisan key:generate
```

### 4.6 Konfigurasi database di `.env`

Sesuaikan bagian berikut:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simpeg_app
DB_USERNAME=root
DB_PASSWORD=
```

Buat dulu database kosong, misalnya `simpeg_app`.

### 4.7 Storage link

Aplikasi ini memakai upload file dan file dummy, jadi storage link wajib dibuat.

```bash
php artisan storage:link
```

---

## 5. Menjalankan migrasi

### Opsi A - database kosong tanpa dummy data

```bash
php artisan migrate
```

### Opsi B - database lengkap dengan dummy data (direkomendasikan untuk development / demo)

```bash
php artisan migrate:fresh --seed
```

Perintah ini akan:
- membuat ulang seluruh tabel
- menjalankan `DashboardTableSeeder`
- menjalankan `SimpegComprehensiveDummySeeder`
- mengisi akun role + master data + graph data pegawai lintas modul

---

## 6. Menjalankan aplikasi

### Mode development penuh

```bash
composer run dev
```

Perintah ini menjalankan:
- `php artisan serve`
- `php artisan queue:listen --tries=1 --timeout=0`
- `npm run dev`

### Alternatif manual

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

Terminal 3 (opsional tapi disarankan):

```bash
php artisan queue:listen --tries=1 --timeout=0
```

---

## 7. Dummy data yang tersedia

Seeder komprehensif sudah menyiapkan data untuk banyak halaman, bukan hanya sebagian kecil modul.

### Master data yang dibuat
- 1 instansi
- 1 sekretariat
- 3 unit kerja
- master jabatan
- master eselon
- master pangkat
- master golongan

### Akun yang dibuat

Semua password akun demo adalah:

```text
password
```

#### Superadmin
- `superadmin.demo`

#### Admin
- `admin.bkpsdm`
- `admin.dinkes`

#### Pegawai
- `pegawai.andi`
- `pegawai.bela`
- `pegawai.citra`

### Data modul yang diisi per pegawai

Setiap akun pegawai demo sudah dipasangi bundle data yang luas, termasuk:
- biodata pegawai
- suami/istri
- anak
- orang tua
- riwayat pendidikan sekolah
- pendidikan lanjut
- pendidikan bahasa
- jabatan aktif + riwayat
- pangkat aktif + riwayat
- hukuman
- rencana Diklat
- realisasi Diklat
- penghargaan
- penugasan luar negeri
- seminar
- cuti
- latihan jabatan
- mutasi
- tunjangan
- izin kawin
- prestasi kerja
- TPP
- KGB

Jadi dashboard, report, profile, dan banyak modul lain langsung punya data representatif.

---

## 8. Cara reset dummy data

Kalau ingin mengulang dari awal:

```bash
php artisan migrate:fresh --seed
```

Kalau hanya ingin refresh asset frontend production:

```bash
npm run build
```

---

## 9. Struktur penting yang perlu diperhatikan

- `app/` → controller, model, service
- `database/migrations/` → struktur tabel
- `database/seeders/` → seeder database
- `resources/views/` → Blade views
- `routes/web.php` → route web utama
- `tests/Feature/Diklat/` → regression tests fitur Diklat / plan-realization

---

## 10. Panduan push ke repo baru (opsi 1 - kontribusi hanya milik `dafahuda`)

Kalau targetmu adalah repository baru di bawah akun `dafahuda` **dengan identitas kontribusi hanya milikmu**, maka:

### Prinsip penting
Jangan push history lama apa adanya ke repo baru, karena author commit lama tetap milik author lama.

Solusi aman:
- buat repo baru kosong di GitHub
- siapkan **fresh history / clean snapshot push**
- push hanya isi source yang memang ingin dibawa

---

## 11. Temporary push hygiene untuk `.sisyphus/` dan `.playwright-mcp/`

Saat ini `.gitignore` sudah ditambah sementara dengan:

```gitignore
/.sisyphus
/.playwright-mcp
```

Tujuannya:
- folder automation/context tidak ikut ke push bersih awal
- setelah push ke repo baru selesai, dua baris ini bisa dihapus lagi kalau kamu ingin folder-folder itu tetap tersedia untuk konteks development lokal

---

## 12. Apa yang **boleh** dipush

### Wajib / aman dipush
- `app/`
- `bootstrap/`
- `config/`
- `database/`
- `public/images/` (jika memang bagian aplikasi)
- `resources/`
- `routes/`
- `tests/`
- `artisan`
- `composer.json`
- `composer.lock`
- `package.json`
- `package-lock.json`
- `vite.config.js`
- `phpunit.xml`
- `.editorconfig`
- `.gitattributes`
- `.gitignore`
- `README.md`

### Khusus fitur yang baru saya kerjakan, ini juga seharusnya ikut dipush
- controller/model/service Diklat plan-realization
- migration `tb_rencana_diklat` dan link ke `tb_diklat`
- report Diklat gap
- dashboard Diklat analytics
- profile Diklat summary
- comprehensive dummy data seeder
- seluruh regression tests terkait Diklat

---

## 13. Apa yang **jangan** dipush

### Jangan dipush ke repo produk / clean repo
- `.playwright-mcp/`
- `.sisyphus/run-continuation/`
- `.sisyphus/boulder.json`
- `.sisyphus/drafts/`
- log runtime automation
- file screenshot QA di root project
- artifact hasil download/export sementara
- file environment lokal:
  - `.env`
  - `.env.*`

### Sebaiknya juga jangan dipush kecuali memang sengaja untuk dokumentasi internal
- sebagian besar `.sisyphus/evidence/`
- notepad kerja agent
- artifact browser QA / xlsx ekstraksi

Kalau kamu ingin repo yang bersih untuk publik/produk, lebih baik `.sisyphus/` dan `.playwright-mcp/` tidak ikut di push awal.

---

## 14. Apa yang bisa dipertimbangkan untuk disimpan lokal saja

- `.sisyphus/plans/`
- `.sisyphus/notepads/`
- `.sisyphus/evidence/`

Ini berguna untuk jejak implementasi, tapi tidak wajib menjadi bagian repo aplikasi yang akan dibagikan/dipublish.

---

## 15. Troubleshooting singkat

### `php artisan migrate:fresh --seed` gagal
- pastikan MySQL aktif
- cek nama database di `.env`
- cek user/password database

### `php artisan storage:link` gagal
- hapus `public/storage` lama jika broken symlink
- jalankan ulang perintah

### Frontend tidak muncul / asset error
- jalankan:

```bash
npm install
npm run build
```

atau saat dev:

```bash
npm run dev
```

### Queue / fitur tertentu terasa tidak jalan
- jalankan queue listener:

```bash
php artisan queue:listen --tries=1 --timeout=0
```

---

## 16. Ringkasan cepat setup developer baru

Kalau ingin cepat langsung jalan:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate:fresh --seed
composer run dev
```

Lalu login pakai salah satu akun demo dengan password:

```text
password
```
