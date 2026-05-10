<?php

namespace Database\Seeders;

use App\Models\Cuti;
use App\Models\Diklat;
use App\Models\Eselon;
use App\Models\Hukuman;
use App\Models\InstansiLembaga;
use App\Models\IzinKawin;
use App\Models\Jabatan;
use App\Models\KGB;
use App\Models\LatihanJabatan;
use App\Models\MasterEselon;
use App\Models\MasterGolongan;
use App\Models\MasterJabatan;
use App\Models\MasterPangkat;
use App\Models\Mutasi;
use App\Models\Pangkat;
use App\Models\Pegawai;
use App\Models\Penghargaan;
use App\Models\PenugasanLuarNegeri;
use App\Models\PrestasiKerja;
use App\Models\RencanaDiklat;
use App\Models\RiwayatKeluargaAnak;
use App\Models\RiwayatKeluargaOrangtua;
use App\Models\RiwayatKeluargaSuamiIstri;
use App\Models\RiwayatPendidikanBahasa;
use App\Models\RiwayatPendidikanLanjut;
use App\Models\RiwayatPendidikanSekolah;
use App\Models\Sekretariat;
use App\Models\Seminar;
use App\Models\Tpp;
use App\Models\Tunjangan;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SimpegComprehensiveDummySeeder extends Seeder
{
    public function run(): void
    {
        $currentYear = (int) now()->year;
        $nextYear = $currentYear + 1;

        $references = $this->seedReferenceData();
        $roleData = $this->seedRoleAccounts($references['units']);

        $this->seedPegawaiBundle(
            $roleData['pegawai_andi']['pegawai'],
            [
                'slug' => 'andi-pratama',
                'spouse' => 'Sari Pratama',
                'child' => 'Naufal Pratama',
                'father' => 'Budi Pratama',
                'mother' => 'Rina Pratama',
                'language' => 'Inggris',
                'plannedTraining' => 'Pelatihan Manajemen ASN',
                'realizedTraining' => 'Pelatihan Kepemimpinan Administrator',
                'crossYearTraining' => 'Pelatihan Reformasi Birokrasi',
                'outOfPlanTraining' => 'Workshop Digital Arsip',
                'seminar' => 'Seminar Tata Kelola Pemerintahan',
                'award' => 'ASN Teladan Kota Contoh',
                'destinationCountry' => 'Jepang',
                'mutationTarget' => 'BKPSDM Provinsi Contoh',
                'marriagePartner' => 'Sari Pratama',
            ],
            $references,
            $currentYear,
            $nextYear,
            true,
        );

        $this->seedPegawaiBundle(
            $roleData['pegawai_bela']['pegawai'],
            [
                'slug' => 'bela-ayuningtyas',
                'spouse' => 'Raka Ayuningtyas',
                'child' => 'Anya Ayuningtyas',
                'father' => 'Surya Santoso',
                'mother' => 'Dewi Santoso',
                'language' => 'Arab',
                'plannedTraining' => 'Pelatihan Layanan Kesehatan Primer',
                'realizedTraining' => 'Pelatihan Audit Klinis',
                'crossYearTraining' => 'Pelatihan Transformasi Digital Puskesmas',
                'outOfPlanTraining' => 'Workshop Keselamatan Pasien',
                'seminar' => 'Seminar Inovasi Pelayanan Kesehatan',
                'award' => 'Pegawai Inovatif Dinas Kesehatan',
                'destinationCountry' => 'Singapura',
                'mutationTarget' => 'Dinas Kesehatan Provinsi Contoh',
                'marriagePartner' => 'Raka Ayuningtyas',
            ],
            $references,
            $currentYear,
            $nextYear,
            false,
        );

        $this->seedPegawaiBundle(
            $roleData['pegawai_citra']['pegawai'],
            [
                'slug' => 'citra-lestari',
                'spouse' => 'Arga Lestari',
                'child' => 'Mika Lestari',
                'father' => 'Hendra Lestari',
                'mother' => 'Lina Lestari',
                'language' => 'Jepang',
                'plannedTraining' => 'Pelatihan Kurikulum Merdeka',
                'realizedTraining' => 'Pelatihan Supervisi Pendidikan',
                'crossYearTraining' => 'Pelatihan Kepemimpinan Sekolah',
                'outOfPlanTraining' => 'Workshop Media Pembelajaran',
                'seminar' => 'Seminar Peningkatan Mutu Pendidikan',
                'award' => 'Guru Berprestasi Kota Contoh',
                'destinationCountry' => 'Malaysia',
                'mutationTarget' => 'Dinas Pendidikan Provinsi Contoh',
                'marriagePartner' => 'Arga Lestari',
            ],
            $references,
            $currentYear,
            $nextYear,
            false,
        );
    }

    private function seedReferenceData(): array
    {
        $instansiLogo = $this->createSvgAsset('dummy/logos/instansi-kota-contoh.svg', 'Instansi', '#2563eb');
        $sekretariatLogo = $this->createSvgAsset('dummy/logos/sekretariat-kota-contoh.svg', 'Sekretariat', '#7c3aed');

        $instansi = InstansiLembaga::updateOrCreate(
            ['nama_instansi_lembaga' => 'Pemerintah Kota Contoh'],
            [
                'kabupaten_kota' => 'Kota',
                'nama_kota_kabupaten' => 'Kota Contoh',
                'alamat' => 'Jl. Pemerintahan No. 1 Kota Contoh',
                'no_telp' => '021-5551000',
                'email' => 'pemkot@example.test',
                'kepala_dinas' => 'Drs. Kepala Daerah',
                'nip' => '196501011990031001',
                'gambar_logo' => $instansiLogo,
            ],
        );

        $sekretariat = Sekretariat::updateOrCreate(
            ['nama_sekretariat' => 'Sekretariat Daerah Kota Contoh'],
            [
                'kabupaten_kota' => 'Kota',
                'nama_kabupaten_kota' => 'Kota Contoh',
                'alamat' => 'Jl. Sekretariat No. 2 Kota Contoh',
                'email' => 'sekretariat@example.test',
                'no_telp' => '021-5552000',
                'sekretaris' => 'Drs. Sekretaris Daerah',
                'nip' => '196701011992031001',
                'gambar_logo' => $sekretariatLogo,
            ],
        );

        $unitBkpsdm = UnitKerja::updateOrCreate(
            ['nama_unit' => 'BKPSDM Kota Contoh'],
            ['alamat' => 'Jl. BKPSDM No. 10 Kota Contoh'],
        );

        $unitDinkes = UnitKerja::updateOrCreate(
            ['nama_unit' => 'Dinas Kesehatan Kota Contoh'],
            ['alamat' => 'Jl. Sehat No. 20 Kota Contoh'],
        );

        $unitDisdik = UnitKerja::updateOrCreate(
            ['nama_unit' => 'Dinas Pendidikan Kota Contoh'],
            ['alamat' => 'Jl. Pendidikan No. 30 Kota Contoh'],
        );

        $masterJabatan = [
            'analis_kepegawaian' => MasterJabatan::updateOrCreate(['nama_jabatan' => 'Analis Kepegawaian Ahli Muda']),
            'perencana' => MasterJabatan::updateOrCreate(['nama_jabatan' => 'Perencana Ahli Muda']),
            'kepala_bidang' => MasterJabatan::updateOrCreate(['nama_jabatan' => 'Kepala Bidang']),
            'pengawas_sekolah' => MasterJabatan::updateOrCreate(['nama_jabatan' => 'Pengawas Sekolah Ahli Muda']),
        ];

        $masterEselon = [
            'iii' => MasterEselon::updateOrCreate(['nama_eselon' => 'III.a']),
            'iv' => MasterEselon::updateOrCreate(['nama_eselon' => 'IV.a']),
        ];

        $masterPangkat = [
            'penata_muda' => MasterPangkat::updateOrCreate(['nama_pangkat' => 'Penata Muda']),
            'penata_muda_tk1' => MasterPangkat::updateOrCreate(['nama_pangkat' => 'Penata Muda Tk. I']),
            'penata' => MasterPangkat::updateOrCreate(['nama_pangkat' => 'Penata']),
        ];

        $masterGolongan = [
            'iiia' => MasterGolongan::updateOrCreate(['nama_golongan' => 'III/a']),
            'iiib' => MasterGolongan::updateOrCreate(['nama_golongan' => 'III/b']),
            'iiic' => MasterGolongan::updateOrCreate(['nama_golongan' => 'III/c']),
        ];

        foreach (['I/a', 'II/a', 'III/a', 'III/b', 'III/c', 'IV/a'] as $namaGolongan) {
            DB::table('tb_golongan')->updateOrInsert(
                ['nama_golongan' => $namaGolongan],
                ['created_at' => now(), 'updated_at' => now()],
            );
        }

        foreach ($masterEselon as $eselon) {
            Eselon::updateOrCreate(['master_eselon_id' => $eselon->id]);
        }

        return [
            'instansi' => $instansi,
            'sekretariat' => $sekretariat,
            'units' => [
                'bkpsdm' => $unitBkpsdm,
                'dinkes' => $unitDinkes,
                'disdik' => $unitDisdik,
            ],
            'master_jabatan' => $masterJabatan,
            'master_eselon' => $masterEselon,
            'master_pangkat' => $masterPangkat,
            'master_golongan' => $masterGolongan,
        ];
    }

    private function seedRoleAccounts(array $units): array
    {
        $superadmin = User::updateOrCreate(
            ['username' => 'superadmin.demo'],
            [
                'name' => 'Superadmin Demo SIMPEG',
                'email' => 'superadmin.demo@example.test',
                'role' => 'superadmin',
                'unit_kerja_id' => null,
                'password' => Hash::make('password'),
            ],
        );

        $adminBkpsdm = User::updateOrCreate(
            ['username' => 'admin.bkpsdm'],
            [
                'name' => 'Admin BKPSDM Demo',
                'email' => 'admin.bkpsdm@example.test',
                'role' => 'admin',
                'unit_kerja_id' => $units['bkpsdm']->id,
                'password' => Hash::make('password'),
            ],
        );

        $adminDinkes = User::updateOrCreate(
            ['username' => 'admin.dinkes'],
            [
                'name' => 'Admin Dinkes Demo',
                'email' => 'admin.dinkes@example.test',
                'role' => 'admin',
                'unit_kerja_id' => $units['dinkes']->id,
                'password' => Hash::make('password'),
            ],
        );

        return [
            'superadmin' => $superadmin,
            'admin_bkpsdm' => $adminBkpsdm,
            'admin_dinkes' => $adminDinkes,
            'pegawai_andi' => [
                'user' => $this->seedPegawaiUser('pegawai.andi', 'Andi Pratama', 'pegawai.andi@example.test', $units['bkpsdm']),
                'pegawai' => $this->seedPegawai(
                    '198801012020011001',
                    '3276010101880001',
                    'Andi Pratama',
                    'S.T.',
                    'Ir.',
                    'Bandung',
                    '1988-01-01',
                    'laki-laki',
                    'Islam',
                    'O',
                    'Nikah',
                    $units['bkpsdm'],
                    'pegawai.andi@example.test',
                    '2000000000000001',
                    'KARPEG-ANDI-001',
                    'III/a',
                    5000000,
                    'andi-pratama',
                ),
            ],
            'pegawai_bela' => [
                'user' => $this->seedPegawaiUser('pegawai.bela', 'Bela Ayuningtyas', 'pegawai.bela@example.test', $units['dinkes']),
                'pegawai' => $this->seedPegawai(
                    '199002142021022002',
                    '3276021402900002',
                    'Bela Ayuningtyas',
                    'S.KM.',
                    'dr.',
                    'Garut',
                    '1990-02-14',
                    'perempuan',
                    'Islam',
                    'A',
                    'Nikah',
                    $units['dinkes'],
                    'pegawai.bela@example.test',
                    '2000000000000002',
                    'KARPEG-BELA-002',
                    'III/b',
                    5250000,
                    'bela-ayuningtyas',
                ),
            ],
            'pegawai_citra' => [
                'user' => $this->seedPegawaiUser('pegawai.citra', 'Citra Lestari', 'pegawai.citra@example.test', $units['disdik']),
                'pegawai' => $this->seedPegawai(
                    '199305202022032003',
                    '3276032005930003',
                    'Citra Lestari',
                    'M.Pd.',
                    'Dr.',
                    'Tasikmalaya',
                    '1993-05-20',
                    'perempuan',
                    'Islam',
                    'B',
                    'Nikah',
                    $units['disdik'],
                    'pegawai.citra@example.test',
                    '2000000000000003',
                    'KARPEG-CITRA-003',
                    'III/c',
                    5400000,
                    'citra-lestari',
                ),
            ],
        ];
    }

    private function seedPegawaiUser(string $username, string $name, string $email, UnitKerja $unitKerja): User
    {
        return User::updateOrCreate(
            ['username' => $username],
            [
                'name' => $name,
                'email' => $email,
                'role' => 'pegawai',
                'unit_kerja_id' => $unitKerja->id,
                'password' => Hash::make('password'),
            ],
        );
    }

    private function seedPegawai(
        string $nip,
        string $nik,
        string $nama,
        string $gelar,
        string $gelarDepan,
        string $tempatLahir,
        string $tanggalLahir,
        string $jenisKelamin,
        string $agama,
        string $golonganDarah,
        string $statusPernikahan,
        UnitKerja $unitKerja,
        string $email,
        string $bpjs,
        string $karpeg,
        string $golAwal,
        int $nilaiTpp,
        string $slug,
    ): Pegawai {
        $user = User::where('email', $email)->firstOrFail();

        return Pegawai::updateOrCreate(
            ['nip' => $nip],
            [
                'user_id' => $user->id,
                'unit_kerja_id' => $unitKerja->id,
                'foto' => $this->createSvgAsset("dummy/photos/{$slug}.svg", $nama, '#0f766e'),
                'nik' => $nik,
                'nama' => $nama,
                'gelar' => $gelar,
                'gelar_depan' => $gelarDepan,
                'tmpt_lahir' => $tempatLahir,
                'tgl_lahir' => $tanggalLahir,
                'jenis_kelamin' => $jenisKelamin,
                'agama' => $agama,
                'golongan_darah' => $golonganDarah,
                'status_pernikahan' => $statusPernikahan,
                'alamat' => "Jl. {$nama} No. 1 Kota Contoh",
                'no_hp' => '081234567890',
                'email' => $email,
                'email_gov' => str_replace('@example.test', '@kota-contoh.go.id', $email),
                'no_npwp' => '00.000.000.0-000.000',
                'no_bpjs' => $bpjs,
                'status_kepegawaian' => 'PNS',
                'karpeg' => $karpeg,
                'no_sk_cpns' => 'SK-CPNS-' . substr($nip, -4),
                'tmt_cpns' => '2020-01-01',
                'no_sk_pns' => 'SK-PNS-' . substr($nip, -4),
                'tmt_pns' => '2022-01-01',
                'gol_awal' => $golAwal,
                'nilai_tpp' => $nilaiTpp,
            ],
        );
    }

    private function seedPegawaiBundle(Pegawai $pegawai, array $config, array $references, int $currentYear, int $nextYear, bool $createCrossYearForCurrentUnit): void
    {
        $slug = $config['slug'];

        RiwayatKeluargaSuamiIstri::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'nama' => $config['spouse']],
            [
                'no_ktp_nik' => '3500000000000001',
                'tgl_lahir' => '1990-06-10',
                'tempat_lahir' => 'Cimahi',
                'pendidikan' => 'S1',
                'pekerjaan' => 'ASN',
                'status_hubungan' => $pegawai->jenis_kelamin === 'laki-laki' ? 'Istri' : 'Suami',
            ],
        );

        RiwayatKeluargaAnak::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'nama' => $config['child']],
            [
                'nik' => '3500000000000002',
                'tempat_lahir' => 'Bandung',
                'tgl_lahir' => '2018-08-17',
                'jenis_kelamin' => 'laki-laki',
                'pendidikan' => 'SD',
                'status_hubungan' => 'Anak Kandung',
                'pekerjaan' => 'Pelajar',
            ],
        );

        DB::table('tb_riwayat_orangtua')->updateOrInsert(
            ['pegawai_id' => $pegawai->id, 'nama' => $config['father']],
            [
                'nik' => '3500000000000003',
                'tempat_lahir' => 'Sumedang',
                'tgl_lahir' => '1960-03-03',
                'jenis_kelamin' => 'laki-laki',
                'pendidikan' => 'SLTA',
                'pekerjaan' => 'Wiraswasta',
                'status_hubungan' => 'Ayah Kandung',
                'updated_at' => now(),
                'created_at' => now(),
            ],
        );

        DB::table('tb_riwayat_orangtua')->updateOrInsert(
            ['pegawai_id' => $pegawai->id, 'nama' => $config['mother']],
            [
                'nik' => '3500000000000004',
                'tempat_lahir' => 'Cirebon',
                'tgl_lahir' => '1963-04-04',
                'jenis_kelamin' => 'perempuan',
                'pendidikan' => 'SLTA',
                'pekerjaan' => 'Ibu Rumah Tangga',
                'status_hubungan' => 'Ibu Kandung',
                'updated_at' => now(),
                'created_at' => now(),
            ],
        );

        RiwayatPendidikanSekolah::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'jenjang_pendidikan' => 'S1'],
            [
                'nama_sekolah_universitas' => 'Universitas Negeri Contoh',
                'lokasi' => 'Bandung',
                'jurusan' => 'Administrasi Publik',
                'no_ijazah' => 'IJZ-' . strtoupper($slug) . '-S1',
                'tgl_ijazah' => '2011-09-01',
                'nama_kepsek_rektor' => 'Prof. Akademisi',
            ],
        );

        RiwayatPendidikanSekolah::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'jenjang_pendidikan' => 'S2'],
            [
                'nama_sekolah_universitas' => 'Institut Pemerintahan Contoh',
                'lokasi' => 'Jakarta',
                'jurusan' => 'Manajemen SDM',
                'no_ijazah' => 'IJZ-' . strtoupper($slug) . '-S2',
                'tgl_ijazah' => '2016-09-01',
                'nama_kepsek_rektor' => 'Prof. Pascasarjana',
            ],
        );

        RiwayatPendidikanLanjut::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'jenjang_pendidikan' => 'S2'],
            [
                'nama_sekolah_universitas' => 'Pusat Pelatihan Aparatur',
                'jurusan' => 'Kepemimpinan',
                'thn_mulai' => (string) ($currentYear - 2),
                'thn_selesai' => (string) ($currentYear - 2),
                'status' => 'Ijin Belajar',
            ],
        );

        RiwayatPendidikanBahasa::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'bahasa' => $config['language']],
            [
                'jenis_bahasa' => 'Asing',
                'kemampuan_bicara' => 'Aktif',
            ],
        );

        RiwayatPendidikanBahasa::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'bahasa' => 'Indonesia'],
            [
                'jenis_bahasa' => 'Daerah/Nasional',
                'kemampuan_bicara' => 'Aktif',
            ],
        );

        $jabatanAktif = Jabatan::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'no_sk' => 'SK-JAB-AKTIF-' . $pegawai->id],
            [
                'master_jabatan_id' => $references['master_jabatan']['kepala_bidang']->id,
                'master_eselon_id' => $references['master_eselon']['iii']->id,
                'jenis_jabatan' => 'Jabatan Struktural',
                'tmt_jabatan_mulai' => now()->startOfYear()->toDateString(),
                'tmt_jabatan_selesai' => null,
                'periode' => 'I',
                'tahun_ke' => '1',
                'tgl_sk' => now()->startOfYear()->toDateString(),
                'terbit' => 'Wali Kota Contoh',
            ],
        );

        Jabatan::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'no_sk' => 'SK-JAB-LAMA-' . $pegawai->id],
            [
                'master_jabatan_id' => $references['master_jabatan']['analis_kepegawaian']->id,
                'master_eselon_id' => $references['master_eselon']['iv']->id,
                'jenis_jabatan' => 'Jabatan Fungsional Umum',
                'tmt_jabatan_mulai' => now()->subYears(3)->startOfYear()->toDateString(),
                'tmt_jabatan_selesai' => now()->subYear()->endOfYear()->toDateString(),
                'periode' => 'Sudah Selesai',
                'tahun_ke' => 'Sudah Selesai',
                'tgl_sk' => now()->subYears(3)->startOfYear()->toDateString(),
                'terbit' => 'Sekretaris Daerah',
            ],
        );

        Pangkat::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'no_sk' => 'SK-PANG-AKTIF-' . $pegawai->id],
            [
                'master_pangkat_id' => $references['master_pangkat']['penata']->id,
                'master_golongan_id' => $references['master_golongan']['iiic']->id,
                'jenis_pangkat' => 'Reguler',
                'tmt_pangkat_mulai' => now()->subYears(1)->toDateString(),
                'tmt_pangkat_selesai' => now()->addYears(4)->toDateString(),
                'tgl_sk' => now()->subYears(1)->toDateString(),
                'pejabat_pengesah_sk' => 'Wali Kota Contoh',
            ],
        );

        Pangkat::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'no_sk' => 'SK-PANG-LAMA-' . $pegawai->id],
            [
                'master_pangkat_id' => $references['master_pangkat']['penata_muda_tk1']->id,
                'master_golongan_id' => $references['master_golongan']['iiib']->id,
                'jenis_pangkat' => 'Reguler',
                'tmt_pangkat_mulai' => now()->subYears(4)->toDateString(),
                'tmt_pangkat_selesai' => now()->subYears(1)->subDay()->toDateString(),
                'tgl_sk' => now()->subYears(4)->toDateString(),
                'pejabat_pengesah_sk' => 'Sekretaris Daerah',
            ],
        );

        Hukuman::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'no_sk' => 'SK-HKM-' . $pegawai->id],
            [
                'pelanggaran_yg_dilakukan' => 'Keterlambatan administrasi laporan bulanan.',
                'tingkat_hukuman' => 'Ringan',
                'jenis_hukuman' => 'Teguran Tertulis',
                'isi_teguran' => 'Pegawai diminta meningkatkan ketepatan waktu pelaporan.',
                'pejabat_pengesahan_sk_hukuman' => 'Sekretaris Daerah',
                'file_sk_hukuman' => $this->createTextAsset("dummy/docs/hukuman-{$slug}.txt", 'Dokumen dummy SK hukuman.'),
                'tgl_pengesahan_sk' => now()->subMonths(8)->toDateString(),
                'tmt_hukuman_mulai' => now()->subMonths(8)->toDateString(),
                'tmt_hukuman_pemulihan' => now()->subMonths(2)->toDateString(),
                'no_pemulihan_hukuman' => 'PM-HKM-' . $pegawai->id,
                'pejabat_pemulihan_hukuman' => 'Wali Kota Contoh',
                'tgl_pemulihan_hukuman' => now()->subMonths(2)->toDateString(),
            ],
        );

        $rencanaPlanned = RencanaDiklat::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'tahun_rencana' => (string) $currentYear, 'nama_diklat_rencana' => $config['plannedTraining']],
            [
                'target_kompetensi' => 'Manajemen Organisasi',
                'kategori_diklat' => 'Manajerial',
                'prioritas' => 'Tinggi',
                'target_jam' => 40,
                'target_penyelenggara' => 'BPSDM Provinsi Contoh',
                'alasan_kebutuhan' => 'Peningkatan kapasitas jabatan aktif.',
                'catatan' => 'Belum direalisasikan.',
                'status' => 'planned',
            ],
        );

        $rencanaRealized = RencanaDiklat::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'tahun_rencana' => (string) $currentYear, 'nama_diklat_rencana' => $config['realizedTraining']],
            [
                'target_kompetensi' => 'Kepemimpinan',
                'kategori_diklat' => 'Struktural',
                'prioritas' => 'Tinggi',
                'target_jam' => 32,
                'target_penyelenggara' => 'LAN RI',
                'alasan_kebutuhan' => 'Kebutuhan peningkatan kapasitas kepemimpinan.',
                'catatan' => 'Sudah direalisasikan.',
                'status' => 'realized',
            ],
        );

        $rencanaDraft = RencanaDiklat::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'tahun_rencana' => (string) $currentYear, 'nama_diklat_rencana' => $config['plannedTraining'] . ' Draft'],
            [
                'target_kompetensi' => 'Perencanaan Program',
                'kategori_diklat' => 'Teknis',
                'prioritas' => 'Sedang',
                'target_jam' => 16,
                'target_penyelenggara' => 'Lembaga Internal',
                'alasan_kebutuhan' => 'Masih tahap penajaman kebutuhan.',
                'catatan' => 'Draft internal.',
                'status' => 'draft',
            ],
        );

        $rencanaCancelled = RencanaDiklat::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'tahun_rencana' => (string) $currentYear, 'nama_diklat_rencana' => $config['plannedTraining'] . ' Cancelled'],
            [
                'target_kompetensi' => 'Komunikasi Publik',
                'kategori_diklat' => 'Teknis',
                'prioritas' => 'Rendah',
                'target_jam' => 12,
                'target_penyelenggara' => 'BPSDM Kota Contoh',
                'alasan_kebutuhan' => 'Dibatalkan karena perubahan prioritas.',
                'catatan' => 'Cancelled untuk uji status.',
                'status' => 'cancelled',
            ],
        );

        $rencanaCrossYear = null;

        if ($createCrossYearForCurrentUnit) {
            $rencanaCrossYear = RencanaDiklat::updateOrCreate(
                ['pegawai_id' => $pegawai->id, 'tahun_rencana' => (string) $currentYear, 'nama_diklat_rencana' => $config['crossYearTraining']],
                [
                    'target_kompetensi' => 'Transformasi Digital',
                    'kategori_diklat' => 'Teknis',
                    'prioritas' => 'Sedang',
                    'target_jam' => 24,
                    'target_penyelenggara' => 'Kementerian PANRB',
                    'alasan_kebutuhan' => 'Disiapkan untuk realisasi tahun berikutnya.',
                    'catatan' => 'Cross-year sample.',
                    'status' => 'realized',
                ],
            );
        }

        Diklat::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'nama_diklat' => $config['realizedTraining'], 'tahun' => (string) $currentYear],
            [
                'rencana_diklat_id' => $rencanaRealized->id,
                'jumlah_jam' => 32,
                'penyelenggara' => 'LAN RI',
                'tempat' => 'Jakarta',
                'angkatan' => '1',
                'no_sttpp' => 'STTPP-' . strtoupper($slug) . '-REALIZED',
                'tgl_sttpp' => now()->subMonths(1)->toDateString(),
                'file_sertifikat_diklat' => $this->createTextAsset("dummy/docs/diklat-realized-{$slug}.txt", 'Dokumen dummy sertifikat diklat realized.'),
            ],
        );

        Diklat::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'nama_diklat' => $config['outOfPlanTraining'], 'tahun' => (string) $currentYear],
            [
                'rencana_diklat_id' => null,
                'jumlah_jam' => 16,
                'penyelenggara' => 'Kominfo',
                'tempat' => 'Bandung',
                'angkatan' => '2',
                'no_sttpp' => 'STTPP-' . strtoupper($slug) . '-OOP',
                'tgl_sttpp' => now()->subMonths(2)->toDateString(),
                'file_sertifikat_diklat' => $this->createTextAsset("dummy/docs/diklat-oop-{$slug}.txt", 'Dokumen dummy sertifikat diklat out of plan.'),
            ],
        );

        if ($rencanaCrossYear) {
            Diklat::updateOrCreate(
                ['pegawai_id' => $pegawai->id, 'nama_diklat' => $config['crossYearTraining'], 'tahun' => (string) $nextYear],
                [
                    'rencana_diklat_id' => $rencanaCrossYear->id,
                    'jumlah_jam' => 24,
                    'penyelenggara' => 'Kementerian PANRB',
                    'tempat' => 'Jakarta',
                    'angkatan' => '3',
                    'no_sttpp' => 'STTPP-' . strtoupper($slug) . '-CROSS',
                    'tgl_sttpp' => now()->addMonths(2)->toDateString(),
                    'file_sertifikat_diklat' => $this->createTextAsset("dummy/docs/diklat-cross-{$slug}.txt", 'Dokumen dummy sertifikat diklat cross year.'),
                ],
            );
        }

        Penghargaan::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'nama_penghargaan' => $config['award']],
            [
                'instansi_pemberi' => 'Pemerintah Kota Contoh',
                'tingkat_kegiatan' => 'Nasional',
                'tempat_penghargaan' => 'Aula Kota Contoh',
                'tgl_penghargaan' => now()->subMonths(6)->toDateString(),
                'file_sertifikat_penghargaan' => $this->createTextAsset("dummy/docs/penghargaan-{$slug}.txt", 'Dokumen dummy sertifikat penghargaan.'),
                'tahun' => (string) $currentYear,
                'no_sertifikat' => 'SERT-' . strtoupper($slug),
            ],
        );

        PenugasanLuarNegeri::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'no_st' => 'ST-LN-' . strtoupper($slug)],
            [
                'alasan_penugasan' => 'Benchmark layanan publik internasional.',
                'negara_tujuan' => $config['destinationCountry'],
                'tahun' => (string) $currentYear,
                'durasi_hari' => '7',
                'st' => $this->createTextAsset("dummy/docs/penugasan-ln-{$slug}.txt", 'Dokumen dummy surat tugas luar negeri.'),
            ],
        );

        Seminar::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'nama_seminar' => $config['seminar']],
            [
                'tingkat_kegiatan' => 'Nasional',
                'tempat_seminar' => 'Bandung',
                'tgl_seminar' => now()->subMonths(3)->toDateString(),
                'penyelenggara' => 'Kemendagri',
                'jumlah_jam' => '12',
                'no_piagam' => 'PGM-' . strtoupper($slug),
                'tgl_piagam' => now()->subMonths(3)->toDateString(),
                'file_piagam' => $this->createTextAsset("dummy/docs/seminar-{$slug}.txt", 'Dokumen dummy piagam seminar.'),
            ],
        );

        Cuti::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'no_surat_cuti' => 'CUTI-' . strtoupper($slug)],
            [
                'jenis_cuti' => 'Tahunan',
                'tgl_surat_cuti' => now()->subMonths(4)->toDateString(),
                'pelaksanaan_cuti_mulai' => now()->subMonths(4)->addDays(7)->toDateString(),
                'pelaksanaan_cuti_selesai' => now()->subMonths(4)->addDays(12)->toDateString(),
                'durasi_cuti' => '5 Hari',
                'ketentuan_a' => 'Disetujui',
                'ketentuan_b' => 'Hak cuti mencukupi',
                'ketentuan_c' => 'Tanpa catatan',
                'file_surat_cuti' => $this->createTextAsset("dummy/docs/cuti-{$slug}.txt", 'Dokumen dummy surat cuti.'),
                'tebusan' => 'Kepala BKPSDM; Arsip',
            ],
        );

        LatihanJabatan::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'nomor_sertifikat' => 'LATJAB-' . strtoupper($slug)],
            [
                'tempat_latihan' => 'Bandung',
                'waktu_latihan' => now()->subMonths(5)->toDateString(),
                'nama_pelatih' => 'Instruktur Nasional',
                'tahun_latihan' => (string) $currentYear,
                'jumlah_jam' => '20',
                'tgl_sertifikat' => now()->subMonths(5)->toDateString(),
                'file_sertifikat' => $this->createTextAsset("dummy/docs/latihan-jabatan-{$slug}.txt", 'Dokumen dummy sertifikat latihan jabatan.'),
            ],
        );

        Mutasi::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'no_sk_mutasi' => 'MTS-' . strtoupper($slug)],
            [
                'jenis_mutasi' => 'Pindah Antar Instansi',
                'instansi_tujuan' => $config['mutationTarget'],
                'tgl_sk_mutasi' => now()->subMonths(7)->toDateString(),
                'file_sk_mutasi' => $this->createTextAsset("dummy/docs/mutasi-{$slug}.txt", 'Dokumen dummy SK mutasi.'),
            ],
        );

        Tunjangan::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'no_tunjangan' => 'TNJ-' . strtoupper($slug)],
            [
                'jenis_tunjangan_anak' => 'Anak Kandung',
                'tgl_tunjangan' => now()->subMonths(6)->toDateString(),
                'terhitung_mulai' => now()->subMonths(6)->toDateString(),
                'akta_perkawinan_dari' => 'KUA Kota Contoh',
                'no_akta_perkawinan' => 'AKTA-KWN-' . strtoupper($slug),
                'tgl_akta_perkawinan' => now()->subYears(5)->toDateString(),
                'akta_kelahiran_dari' => 'Disdukcapil Kota Contoh',
                'no_akta_kelahiran' => 'AKTA-LHR-' . strtoupper($slug),
                'tgl_akta_kelahiran' => now()->subYears(4)->toDateString(),
                'tebusan' => 'Bendahara; Arsip',
            ],
        );

        IzinKawin::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'no_surat_izin_perkawinan' => 'IZK-' . strtoupper($slug)],
            [
                'tgl_izin_surat_perkawinan' => now()->subYears(5)->toDateString(),
                'kebangsaan_pegawai' => 'Indonesia',
                'nama_wali_bapak_pegawai' => $config['father'],
                'pekerjaan_wali_bapak_pegawai' => 'Wiraswasta',
                'alamat_wali_bapak' => 'Jl. Wali Bapak Pegawai',
                'nama_wali_ibu_pegawai' => $config['mother'],
                'pekerjaan_wali_ibu_pegawai' => 'Ibu Rumah Tangga',
                'alamat_wali_ibu_pegawai' => 'Jl. Wali Ibu Pegawai',
                'nama_calon_suami_istri' => $config['marriagePartner'],
                'tempat_lahir_calon_suami_istri' => 'Bandung',
                'tgl_lahir_calon_suami_istri' => '1991-07-07',
                'pekerjaan_calon_suami_istri' => 'ASN',
                'nip_nik_calon_suami_istri' => '3200000000000001',
                'pangkat_golongan_calon_suami_istri' => 'III/b',
                'jabatan_calon_suami_istri' => 'Analis',
                'instansi_calon_suami_istri' => 'Pemerintah Kota Contoh',
                'kebangsaan_calon_suami_istri' => 'Indonesia',
                'agama_calon_suami_istri' => 'Islam',
                'alamat_calon_suami_istri' => 'Jl. Pasangan Pegawai',
                'nama_wali_bapak_calon_suami_istri' => 'Ayah Pasangan',
                'pekerjaan_wali_bapak_calon_suami_istri' => 'Wiraswasta',
                'alamat_wali_bapak_calon_suami_istri' => 'Jl. Wali Pasangan Ayah',
                'nama_wali_ibu_calon_suami_istri' => 'Ibu Pasangan',
                'pekerjaan_wali_ibu_calon_suami_istri' => 'Ibu Rumah Tangga',
                'alamat_wali_ibu_calon_suami_istri' => 'Jl. Wali Pasangan Ibu',
                'tempat_perkawinan' => 'Bandung',
                'tgl_perkawinan' => now()->subYears(5)->addMonth()->toDateString(),
                'tgl_ditetapkan_perkawinan' => now()->subYears(5)->addDays(10)->toDateString(),
            ],
        );

        PrestasiKerja::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'tahun_periode' => (string) $currentYear],
            [
                'periode_nilai_dari' => now()->startOfYear()->toDateString(),
                'periode_nilai_sampai' => now()->endOfYear()->toDateString(),
                'nama_pejabat_nilai' => 'Atasan Langsung',
                'nama_atasan_pejabat_penilai' => 'Kepala Perangkat Daerah',
                'skp' => 90,
                'orientasi_pelayanan' => 88,
                'integritas' => 90,
                'komitmen' => 89,
                'disiplin' => 87,
                'kerjasama' => 91,
                'kepemimpinan' => 85,
                'tgl_keberatan_pegawai' => now()->endOfYear()->subDays(20)->toDateString(),
                'isi_keberatan' => 'Tidak ada keberatan.',
                'tgl_pejabat_penilai' => now()->endOfYear()->subDays(15)->toDateString(),
                'isi_tanggapan' => 'Penilaian sudah sesuai.',
                'tgl_keputusan_atasan_pejabat_penilai' => now()->endOfYear()->subDays(10)->toDateString(),
                'isi_keputusan' => 'Ditetapkan.',
                'rekomendasi' => 'Layak dikembangkan lebih lanjut.',
                'tgl_diterima_pegawai' => now()->endOfYear()->subDays(5)->toDateString(),
                'tgl_diterima_atasan' => now()->endOfYear()->subDays(4)->toDateString(),
                'total_nilai' => 89,
            ],
        );

        Tpp::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'periode' => 'Januari', 'tahun' => (string) $currentYear],
            [
                'jml_hari_kerja' => 22,
                'tidak_ada_produktifitas' => 0,
                'terlambat_1_30' => 1,
                'terlambat_31_60' => 0,
                'terlambat_61_90' => 0,
                'terlambat_91_lebih' => 0,
                'pulang_awal_1_30' => 0,
                'pulang_awal_31_60' => 0,
                'pulang_awal_61_90' => 0,
                'pulang_awal_91_lebih' => 0,
                'tidak_masuk_kerja' => 0,
                'nilai_basic_tpp' => 5000000,
                'pengurangan_produktifitas' => 0,
                'pengurangan_disiplin' => 50000,
                'tpp_diterima' => 4950000,
            ],
        );

        Tpp::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'periode' => 'Februari', 'tahun' => (string) $currentYear],
            [
                'jml_hari_kerja' => 20,
                'tidak_ada_produktifitas' => 0,
                'terlambat_1_30' => 0,
                'terlambat_31_60' => 1,
                'terlambat_61_90' => 0,
                'terlambat_91_lebih' => 0,
                'pulang_awal_1_30' => 0,
                'pulang_awal_31_60' => 1,
                'pulang_awal_61_90' => 0,
                'pulang_awal_91_lebih' => 0,
                'tidak_masuk_kerja' => 0,
                'nilai_basic_tpp' => 5000000,
                'pengurangan_produktifitas' => 0,
                'pengurangan_disiplin' => 85000,
                'tpp_diterima' => 4915000,
            ],
        );

        KGB::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'no_kgb' => 'KGB-' . strtoupper($slug)],
            [
                'tgl_kgb' => now()->subMonths(1)->toDateString(),
                'pejabat' => 'Wali Kota Contoh',
                'no_sk_terakhir' => 'SK-GAJI-' . strtoupper($slug),
                'tgl_sk_terakhir' => now()->subYear()->toDateString(),
                'tgl_berlaku_gaji' => now()->subMonths(1)->toDateString(),
                'masa_kerja_lama_tahun' => '4',
                'masa_kerja_lama_bulan' => '0',
                'gaji_baru' => '5500000',
                'gaji_baru_terbilang' => 'Lima Juta Lima Ratus Ribu Rupiah',
                'masa_kerja_baru_tahun' => '5',
                'masa_kerja_baru_bulan' => '0',
                'tmt_kgb' => now()->addMonths(2)->toDateString(),
                'tembusan' => ['Bendahara', 'BKPSDM', 'Arsip'],
                'periode' => (string) $currentYear,
            ],
        );
    }

    private function createSvgAsset(string $path, string $label, string $color): string
    {
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" viewBox="0 0 400 400">
  <rect width="400" height="400" fill="{$color}" rx="24" />
  <text x="200" y="215" font-family="Arial, sans-serif" font-size="28" text-anchor="middle" fill="#ffffff">{$label}</text>
</svg>
SVG;

        Storage::disk('public')->put($path, $svg);

        return $path;
    }

    private function createTextAsset(string $path, string $contents): string
    {
        Storage::disk('public')->put($path, $contents . PHP_EOL . 'Generated by SimpegComprehensiveDummySeeder.');

        return $path;
    }
}
