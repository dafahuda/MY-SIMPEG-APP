# DESIGN.md — Arah Desain MY-SIMPEG-APP

> File ini adalah **arah desain** (jiwa UI). Filter anti-slop ada di `ANTISLOP.md`.
> Bahasa antarmuka: **Bahasa Indonesia**. Semua copy UI ditulis dalam Bahasa
> Indonesia yang sopan, ringkas, dan resmi (ini aplikasi kepegawaian pemerintah,
> bukan landing page marketing).

## Identitas

**MY-SIMPEG** — Sistem Informasi Kepegawaian ASN. Penggunanya adalah admin
kepegawaian dan pegawai negeri yang membuka aplikasi ini **untuk bekerja**
(bukan browsing). Desain harus terasa seperti **perkakas kantor pemerintah yang
*rapi, tenang, dan efisien*** — informatif dulu, cantik kemudian.

## Prinsip

1. **Padat data, tidak ramai.** Halaman seperti daftar pegawai, rekapitulasi,
   dan laporan menampilkan tabel dan angka. Prioritas: keterbacaan baris,
   scan-ability kolom, dan hierarki tipografi yang jelas.
2. **Konsisten dengan yang ada.** Aplikasi ini sudah punya 100+ halaman
   memakai pola tertentu (kartu putih `bg-white` + `shadow-sm sm:rounded-lg`,
   tabel dengan header `bg-gray-50` uppercase, badge status berwarna,
   tombol solid warna tunggal). **Halaman baru mengikuti pola yang sudah ada** —
   jangan memperkenalkan gaya baru di tengah aplikasi.
3. **Warna fungsional, bukan dekoratif.** Warna dipakai untuk berarti sesuatu:
   hijau = sukses/disetujui, kuning = perlu perhatian/ubah, merah = bahaya/hapus,
   biru = aksi utama/informasi. Tidak ada gradien dekoratif.
4. **Dukungan dark mode** dipertahankan (kelas `dark:` Tailwind) di semua
   komponen baru.

## Palet (mengikuti Tailwind yang sudah dipakai)

- Latar aplikasi: `gray-100` (light) / `gray-900` (dark)
- Permukaan kartu: putih / `gray-800`
- Teks utama: `gray-800–900` / `gray-100–200`
- Teks sekunder: `gray-500–600` / `gray-400`
- Aksen utama (aksi/link): **biru** (`blue-600`/`blue-700`) — jangan ungu, jangan teal
- Status: hijau (sukses), kuning (warning), merah (error/bahaya), amber (menunggu)

## Tipografi

- Judul halaman: `text-2xl font-semibold` (pola header yang sudah ada)
- Label kolom: `text-xs uppercase tracking-wider text-gray-600`
- Angka identitas (NIP, NIK, NPWP): `font-mono`
- Bahasa Indonesia yang benar untuk semua label dan pesan

## Nada Copy (Bahasa Indonesia)

- Sopan dan resmi: "Dokumen berhasil diunggah.", bukan "Awesome! Your doc has landed 🎉"
- Tidak ada buzzword marketing, tidak ada emoji dekoratif berlebihan
  (emoji kecil sebagai penanda ikon sudah cukup, maksimal 1 per judul)
- Pesan error menjelaskan apa yang salah dan bagaimana memperbaikinya:
  "Ukuran file maksimal 5 MB." bukan "Oops! Something went wrong."

## Komponen Berulang (pola yang sudah ada — ikuti)

| Komponen | Pola |
|---|---|
| Halaman daftar | Judul + breadcrumb → flash message → kartu putih berisi tabel |
| Badge status | `rounded-full px-2.5 py-0.5 text-xs font-semibold` + warna sesuai makna |
| Tombol aksi utama | `bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg` |
| Tombol sekunder | `bg-gray-200 dark:bg-gray-700` |
| Tombol bahaya | teks `text-red-600` + konfirmasi sebelum hapus |
| Konfirmasi hapus | dialog `confirm()` dengan teks Indonesia yang menyebut objek yang dihapus |
| Flash sukses | `bg-green-50 text-green-700` + border hijau |

## Dial (target liveliness)

Dial: **ENERGY 1 / RHYTHM 2 / MOTION 1**

Aplikasi kerja pemerintah: tenang dan stabil. Animasi hanya transisi hover/
focus yang halus; tidak ada animasi masuk yang mencolok, tidak ada paralaks,
tidada confetti. "Hidup" datang dari data yang akurat dan status yang jelas,
bukan dari gerakan.

## Yang Tidak Boleh Terjadi

- Gradien biru-ungu, glassmorphism, blob dekoratif, ilustrasi AI generik
- Emoji berjamaah di tombol/judul (1 penanda kecil cukup)
- Copy bahasa Inggris di UI ("Submit", "Success!", "Oops")
- Layout dashboard penuh kartu statistik yang tidak dipakai siapa pun
- Font display mewah yang tidak perlu — pakai font default Tailwind
