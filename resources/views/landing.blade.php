<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SIMPEG, layanan pengelolaan data dan administrasi kepegawaian ASN.">
    <title>SIMPEG | Sistem Informasi Kepegawaian</title>
    <link rel="icon" href="{{ asset('images/logo_asn.png') }}">
    <script>try { if (localStorage.getItem('dark-mode') === 'true') document.documentElement.classList.add('dark'); } catch {}</script>
    @vite(['resources/css/landing.css', 'resources/js/landing.js'])
</head>
<body>
    <a class="skip-link" href="#konten-utama">Lewati navigasi</a>
    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="{{ url('/') }}" aria-label="SIMPEG, halaman utama"><span class="brand-name">SIMPEG</span><span class="brand-detail">Administrasi kepegawaian ASN</span></a>
            <nav aria-label="Navigasi utama">
                <a class="nav-service" href="#layanan">Layanan</a>
                <button data-theme-toggle type="button" aria-label="Aktifkan mode gelap" aria-pressed="false">Mode gelap</button>
                @auth
                    <a class="button button-small" href="{{ route('dashboard') }}">Dashboard</a>
                @else
                    <a class="button button-small" href="{{ route('login') }}">Masuk</a>
                @endauth
            </nav>
        </div>
    </header>
    <main id="konten-utama">
        <section class="container introduction">
            <div class="intro-copy">
                <p class="eyebrow">Sistem Informasi Kepegawaian</p>
                <h1>Administrasi ASN,<br>terkelola dalam satu tempat.</h1>
                <p class="lead">Kelola data pegawai, riwayat kepegawaian, dokumen, dan laporan melalui layanan yang sesuai dengan kewenangan Anda.</p>
                <div class="intro-actions">
                    @auth
                        <a class="button" href="{{ route('dashboard') }}">Buka Dashboard <span aria-hidden="true">→</span></a>
                    @else
                        <a class="button" href="{{ route('login') }}">Masuk ke SIMPEG <span aria-hidden="true">→</span></a>
                    @endauth
                    <a class="text-link" href="#layanan">Lihat layanan</a>
                </div>
                <p class="access-note">Untuk pegawai dan pengelola kepegawaian yang memiliki akun.</p>
            </div>
            <aside class="access-panel" aria-labelledby="akses-title">
                <p class="eyebrow">Panduan akses</p>
                <h2 id="akses-title">Gunakan akun instansi Anda.</h2>
                <ol>
                    <li><span>01</span><div><strong>Masuk dengan username</strong><p>Gunakan akun yang diberikan administrator.</p></div></li>
                    <li><span>02</span><div><strong>Akses sesuai peran</strong><p>Pegawai mengakses data sendiri. Pengelola bekerja sesuai lingkup kewenangannya.</p></div></li>
                    <li><span>03</span><div><strong>Jaga kerahasiaan akun</strong><p>Keluar dari aplikasi setelah selesai, terutama pada perangkat bersama.</p></div></li>
                </ol>
                <p class="help-note">Belum memiliki akun atau mengalami kendala? Hubungi administrator kepegawaian instansi Anda.</p>
            </aside>
        </section>
        <section id="layanan" class="services container" aria-labelledby="layanan-title">
            <div class="section-heading"><p class="eyebrow">Ruang lingkup layanan</p><h2 id="layanan-title">Dari data pegawai hingga laporan.</h2><p>Layanan tersedia setelah masuk, sesuai hak akses akun.</p></div>
            <dl class="service-list">
                <div><dt>Data dan riwayat pegawai</dt><dd>Biodata, keluarga, pendidikan, pangkat, jabatan, dan riwayat kepegawaian.</dd></div>
                <div><dt>Administrasi kepegawaian</dt><dd>Pengelolaan cuti, rencana diklat, realisasi diklat, dan kenaikan gaji berkala.</dd></div>
                <div><dt>Dokumen dan laporan</dt><dd>Arsip dokumen pegawai, rekapitulasi, serta unduhan laporan dan biodata PDF.</dd></div>
            </dl>
        </section>
    </main>
    <footer class="container site-footer"><span>SIMPEG · Sistem Informasi Kepegawaian ASN</span><span>Data pribadi hanya tersedia bagi pengguna berwenang.</span></footer>
</body>
</html>
