<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataFeedController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AktivitasLogController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\InstansiLembagaController;
use App\Http\Controllers\SekretariatController;
use App\Http\Controllers\MasterJabatanController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\DokumenPegawaiController;
use App\Http\Controllers\RiwayatKeluargaAnakController;
use App\Http\Controllers\RiwayatKeluargaOrangTuaController;
use App\Http\Controllers\RiwayatKeluargaSuamiIstriController;
use App\Http\Controllers\RiwayatPendidikanBahasaController;
use App\Http\Controllers\RiwayatPendidikanLanjutController;
use App\Http\Controllers\RiwayatPendidikanSekolahController;
use App\Http\Controllers\UnitKerjaController;
use App\Http\Controllers\UserAdminController;
use App\Http\Controllers\UserPegawaiController;
use App\Http\Controllers\HukumanController;
use App\Http\Controllers\DiklatController;
use App\Http\Controllers\RencanaDiklatController;
use App\Http\Controllers\PenghargaanController;
use App\Http\Controllers\PenugasanLuarNegeriController;
use App\Http\Controllers\SeminarController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\LatihanJabatanController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\TunjanganController;
use App\Http\Controllers\IzinKawinController;
use App\Http\Controllers\MasterEselonController;
use App\Http\Controllers\MasterPangkatController;
use App\Http\Controllers\MasterGolonganController;
use App\Http\Controllers\PangkatController;
use App\Http\Controllers\PrestasiKerjaController;
use App\Http\Controllers\RekapitulasiController;
use App\Http\Controllers\RekapitulasiPdfController;
use App\Http\Controllers\TppController;
use App\Http\Controllers\DiklatGapReportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportPdfController;
use App\Http\Controllers\ProfilePegawaiController;
use App\Http\Controllers\BackupDatabaseController;
use App\Http\Controllers\KGBController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', 'login');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    // Notifikasi: tandai semua sebagai dibaca
    Route::post('/notifications/read-all', function () {
        auth()->user()->unreadNotifications->markAsRead();

        return back();
    })->name('notifications.readAll');

    // Route for the getting the data feed
    Route::get('/json-data-feed', [DataFeedController::class, 'getDataFeed'])->name('json_data_feed');

    // Dokumen digital pegawai
    Route::get('/data_pegawai/{pegawai}/dokumen', [DokumenPegawaiController::class, 'index'])->name('dokumen-pegawai.index');
    Route::post('/data_pegawai/{pegawai}/dokumen', [DokumenPegawaiController::class, 'store'])->name('dokumen-pegawai.store');
    Route::get('/dokumen_pegawai/{dokumen}', [DokumenPegawaiController::class, 'show'])->name('dokumen-pegawai.show');
    Route::get('/dokumen_pegawai/{dokumen}/download', [DokumenPegawaiController::class, 'download'])->name('dokumen-pegawai.download');
    Route::delete('/dokumen_pegawai/{dokumen}', [DokumenPegawaiController::class, 'destroy'])->name('dokumen-pegawai.destroy');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Pegawai (role: pegawai)
    Route::get('/profile_saya', [ProfilePegawaiController::class, 'index'])->name('profile.pegawai');
    Route::get('/profile_saya/edit', [ProfilePegawaiController::class, 'edit'])->name('profile.pegawai.edit');
    Route::put('/profile_saya/update', [ProfilePegawaiController::class, 'update'])->name('profile.pegawai.update');
    Route::post('/profile_saya/upload_foto', [ProfilePegawaiController::class, 'uploadFoto'])->name('profile.pegawai.upload_foto');
    Route::get('/profile_saya/print', [ProfilePegawaiController::class, 'print'])->name('profile.pegawai.print');
    Route::get('/profile_saya/unduh_pdf', [ProfilePegawaiController::class, 'unduhPdfSendiri'])->name('profile.pegawai.pdf');

    Route::prefix('data_pegawai')->group(function() {
        Route::get('/pegawai', [PegawaiController::class, 'index']);
        Route::get('/view_form_tambah_data_pegawai', [PegawaiController::class, 'create']);
        Route::post('/tambah_data_pegawai', [PegawaiController::class, 'store']);
        Route::get('/view_form_edit_data_pegawai/{pegawai}', [PegawaiController::class, 'edit']);
        Route::get('/detail_data_pegawai/{pegawai}', [PegawaiController::class, 'show'])->name('pegawai.show');
        Route::get('/biodata_pdf/{pegawai}', [ProfilePegawaiController::class, 'unduhPdf'])->name('pegawai.biodata.pdf');
        Route::put('/ubah_data_pegawai/{pegawai}', [PegawaiController::class, 'update']);
        Route::delete('/delete_data_pegawai/{pegawai}', [PegawaiController::class, 'destroy']);
        Route::post('/cariPegawai', [PegawaiController::class, 'cariPegawai']);
    });

    Route::prefix('manajemen_setup')->group(function() {

        Route::middleware('role:superadmin')->group(function() {
            Route::get('/instansi_lembaga', [InstansiLembagaController::class, 'index']);
            Route::get('/instansi_lembaga/setup', [InstansiLembagaController::class, 'create']);
            Route::post('/instansi_lembaga/buat_data_instansi', [InstansiLembagaController::class, 'store']);
            Route::get('/setup_instansi_lembaga/{instansiLembaga}', [InstansiLembagaController::class, 'edit']);
            Route::put('/setupInstansiLembaga/{instansiLembaga}', [InstansiLembagaController::class, 'update']);


            Route::get('/sekretariat', [SekretariatController::class, 'index']);
            Route::get('/sekretariat/create', [SekretariatController::class, 'create']);
            Route::post('/sekretariat/buat_data_sekretariat', [SekretariatController::class, 'store']);
            Route::get('/sekretariat/setup_sekretariat/{sekretariat}', [SekretariatController::class, 'edit']);
            Route::put('/sekretariat/setup_sekretariat/{sekretariat}', [SekretariatController::class, 'update']);


            Route::get('/opd_skpd_unitkerja', [UnitKerjaController::class, 'index']);
            Route::get('/view_form_tambah_unitkerja', [UnitKerjaController::class, 'create']);
            Route::post('/opd_skd_unitkerja/tambah_unit_kerja', [UnitKerjaController::class, 'store']);
            Route::get('/view_form_edit_unitkerja/{unitKerja}', [UnitKerjaController::class, 'edit']);
            Route::put("/opd_skd_unitkerja/edit_unit_kerja/{unitKerja}", [UnitKerjaController::class, 'update']);
            Route::delete('/opd_skpd_unit_kerja/delete_unitkerja/{unitKerja}', [UnitKerjaController::class, 'destroy']);
            Route::post('/opd_skpd_unit_kerja/cariUnitKerja', [UnitKerjaController::class, 'cariUnitKerja']);

             // URL buat data user admin
            Route::get('/data_user_admin', [UserAdminController::class, 'index']);
            Route::get('/view_form_tambah_user_admin', [UserAdminController::class, 'create']);
            Route::post('/tambah_user_admin', [UserAdminController::class, 'store']);
            Route::get('/data_user_pegawai', [UserPegawaiController::class, 'index']);
            Route::get('/view_form_edit_user_admin/{user}', [UserAdminController::class, 'edit']);
            Route::put('/edit_data_user_admin/{user}', [UserAdminController::class, 'update']);
            Route::delete('/delete_user_admin/{user}', [UserAdminController::class, 'destroy']);
            Route::post('/data_user_admin/cariUserAdmin', [UserAdminController::class, 'cariUserAdmin']);
        });

        // URL buat data user pegawai
        Route::get('/data_user_pegawai', [UserPegawaiController::class, 'index']);
        Route::get('/view_form_tambah_user_pegawai', [UserPegawaiController::class, 'create']);
        Route::get('/view_form_edit_user_pegawai/{user}', [UserPegawaiController::class, 'edit']);
        Route::post('/tambah_user_pegawai', [UserPegawaiController::class, 'store']);
        Route::put('/edit_data_user_pegawai/{user}', [UserPegawaiController::class, 'update']);
        Route::delete('/delete_user_pegawai/{user}', [UserPegawaiController::class, 'destroy']);
        Route::post('/data_user_pegawai/cariUserPegawai', [UserPegawaiController::class, 'cariUserPegawai']);
    });

    Route::prefix('riwayat_keluarga')->group(function() {
        // URL Riwayat Keluarga Suami / Istri Pegawai
        Route::get('/suami_istri', [RiwayatKeluargaSuamiIstriController::class, 'index']);
        Route::get('/suami_istri/view_form_tambah_suami_istri', [RiwayatKeluargaSuamiIstriController::class, 'create']);
        Route::post('/suami_istri/tambah_data_suami_istri', [RiwayatKeluargaSuamiIstriController::class, 'store']);
        Route::get('/suami_istri/view_edit_data_suami_istri/{riwayatKeluargaSuamiIstri}', [RiwayatKeluargaSuamiIstriController::class, 'edit']);
        Route::put('/suami_istri/edit_data_suami_istri/{riwayatKeluargaSuamiIstri}', [RiwayatKeluargaSuamiIstriController::class, 'update']);
        Route::delete('/suami_istri/delete_data_suami_istri/{riwayatKeluargaSuamiIstri}', [RiwayatKeluargaSuamiIstriController::class, 'destroy']);
        Route::post('/suami_istri/cariPegawaiSuamiIstri', [RiwayatKeluargaSuamiIstriController::class, 'cariPegawaiSuamiIstri']);

        // URL Riwayat Keluarga Anak Pegawai
        Route::get('/anak', [RiwayatKeluargaAnakController::class, 'index']);
        Route::get('/anak/view_tambah_data_anak', [RiwayatKeluargaAnakController::class, 'create']);
        Route::post('/anak/tambah_data_anak', [RiwayatKeluargaAnakController::class, 'store']);
        Route::get('/anak/view_edit_data_anak/{riwayatKeluargaAnak}', [RiwayatKeluargaAnakController::class, 'edit']);
        Route::put('/anak/edit_data_anak/{riwayatKeluargaAnak}', [RiwayatKeluargaAnakController::class, 'update']);
        Route::delete('/anak/delete_data_anak/{riwayatKeluargaAnak}', [RiwayatKeluargaAnakController::class, 'destroy']);
        Route::post("/anak/cariPegawaiAnak", [RiwayatKeluargaAnakController::class, 'cariPegawaiAnak']);

        Route::get('/orang_tua', [RiwayatKeluargaOrangTuaController::class, 'index']);
        Route::get('/orang_tua/view_form_tambah_orang_tua', [RiwayatKeluargaOrangTuaController::class, 'create']);
        Route::post('/orang_tua/tambah_data_orang_tua', [RiwayatKeluargaOrangTuaController::class, 'store']);
        Route::get('/orang_tua/view_form_edit_orang_tua/{riwayatKeluargaOrangTua}', [RiwayatKeluargaOrangTuaController::class, 'edit']);
        Route::put('/orang_tua/edit_data_orang_tua/{riwayatKeluargaOrangTua}', [RiwayatKeluargaOrangTuaController::class, 'update']);
        Route::delete('/orang_tua/delete_data_orang_tua/{riwayatKeluargaOrangTua}', [RiwayatKeluargaOrangTuaController::class, 'destroy']);
        Route::post('/orang_tua/cariPegawaiOrangTua', [RiwayatKeluargaOrangTuaController::class, 'cariPegawaiOrangTua']);
    });

    Route::prefix('riwayat_pendidikan')->group(function() {
        // URL Riwayat Pendidikan Sekolah
        Route::get('/sekolah', [RiwayatPendidikanSekolahController::class, 'index']);
        Route::get('/sekolah/view_form_tambah_pendidikan_sekolah', [RiwayatPendidikanSekolahController::class, 'create']);
        Route::post('/sekolah/tambah_pendidikan_sekolah', [RiwayatPendidikanSekolahController::class, 'store']);
        Route::get('/sekolah/view_form_edit_pendidikan_sekolah/{riwayatPendidikanSekolah}', [RiwayatPendidikanSekolahController::class, 'edit']);
        Route::put('/sekolah/edit_pendidikan_sekolah/{riwayatPendidikanSekolah}', [RiwayatPendidikanSekolahController::class, 'update']);
        Route::delete('/sekolah/delete_pendidikan_sekolah/{riwayatPendidikanSekolah}', [RiwayatPendidikanSekolahController::class, 'destroy']);
        Route::post('/sekolah/cariPendidikanSekolah', [RiwayatPendidikanSekolahController::class, 'cariPendidikanSekolah']);

        // URL Riwayat Pendidikan lanjut
        Route::get('/sekolah_lanjut', [RiwayatPendidikanLanjutController::class, 'index']);
        Route::get('/sekolah_lanjut/view_form_tambah_pendidikan_lanjut', [RiwayatPendidikanLanjutController::class, 'create']);
        Route::post('/sekolah_lanjut/tambah_pendidikan_lanjut', [RiwayatPendidikanLanjutController::class, 'store']);
        Route::get('/sekolah_lanjut/view_form_edit_pendidikan_lanjut/{riwayatPendidikanLanjut}', [RiwayatPendidikanLanjutController::class, 'edit']);
        Route::put('/sekolah_lanjut/edit_pendidikan_lanjut/{riwayatPendidikanLanjut}', [RiwayatPendidikanLanjutController::class, 'update']);
        Route::delete('/sekolah_lanjut/delete_pendidikan_lanjut/{riwayatPendidikanLanjut}', [RiwayatPendidikanLanjutController::class, 'destroy']);
        Route::post("/sekolah_lanjut/cariPendidikanLanjut", [RiwayatPendidikanLanjutController::class, 'cariPendidikanLanjut']);


        // URL Riwayat Pendidikan Bahasa
        Route::get('/pendidikan_bahasa', [RiwayatPendidikanBahasaController::class, 'index']);
        Route::get('/pendidikan_bahasa/view_form_tambah_pendidikan_bahasa', [RiwayatPendidikanBahasaController::class, 'create']);
        Route::post('/pendidikan_bahasa/tambah_data_bahasa', [RiwayatPendidikanBahasaController::class, 'store']);
        Route::get('/pendidikan_bahasa/view_form_edit_pendidikan_bahasa/{riwayatPendidikanBahasa}', [RiwayatPendidikanBahasaController::class, 'edit']);
        Route::put('/pendidikan_bahasa/edit_data_bahasa/{riwayatPendidikanBahasa}', [RiwayatPendidikanBahasaController::class, 'update']);
        Route::delete('/pendidikan_bahasa/delete_data_bahasa/{riwayatPendidikanBahasa}', [RiwayatPendidikanBahasaController::class, 'destroy']);
        Route::post('/pendidikan_bahasa/cariPendidikanBahasa', [RiwayatPendidikanBahasaController::class, 'cariPendidikanBahasa']);
    });

    Route::prefix('kepegawaian')->group(function() {
        // URL Kepegawaian Jabatan
        Route::get('/jabatan', [JabatanController::class, 'index']);
        Route::get('/jabatan/view_tambah_jabatan', [JabatanController::class, 'create']);
        Route::post('/jabatan/tambah_data_jabatan', [JabatanController::class, 'store']);
        Route::get('/jabatan/view_form_edit_jabatan/{jabatan}', [JabatanController::class, 'edit']);
        Route::put('/jabatan/edit_data_jabatan/{jabatan}', [JabatanController::class, 'update']);
        Route::delete('/jabatan/delete_data_jabatan/{jabatan}', [JabatanController::class, 'destroy']);
        Route::post('/jabatan/cariJabatan', [JabatanController::class, 'cariJabatan']);

        // URL untuk master data jabatan
        // Route::get('/jabatan/master_data_jabatan', [MasterJabatanController::class, 'index']);
        Route::get('/jabatan/master_data_jabatan/{masterJabatan}', [MasterJabatanController::class, 'index']);

        // url untuk ajax master jabatan dan eselon
        Route::post('/master_jabatan/store', [MasterJabatanController::class, 'store']);
        Route::get('/master_jabatan/view_form_edit_master_jabatan/{masterJabatan}', [MasterJabatanController::class, 'edit']);
        Route::delete('/master_jabatan/delete_master_jabatan/{masterJabatan}', [MasterJabatanController::class, 'destroy']);
        Route::put('/master_jabatan/update_master_jabatan/{masterJabatan}', [MasterJabatanController::class, 'update']);
        Route::post('/master_eselon/store', [MasterEselonController::class, 'store']);
        Route::get('/master_eselon/view_form_edit_master_eselon/{masterEselon}', [MasterEselonController::class, 'edit']);
        Route::put('/master_eselon/edit_master_eselon/{masterEselon}', [MasterEselonController::class, 'update']);
        Route::delete('/master_eselon/delete_master_eselon/{masterEselon}', [MasterEselonController::class, 'destroy']);

        // URL untuk kepegawaian pangkat
        Route::get('/pangkat', [PangkatController::class, 'index']);
        Route::get('/pangkat/view_form_tambah_pangkat', [PangkatController::class, 'create']);
        Route::post('/pangkat/tambah_data_pangkat', [PangkatController::class, 'store']);
        Route::get('/pangkat/view_form_edit_data_pangkat/{pangkat}', [PangkatController::class, 'edit']);
        Route::put('/pangkat/edit_data_pangkat/{pangkat}', [PangkatController::class, 'update']);
        Route::delete('/pangkat/delete_data_pangkat/{pangkat}', [PangkatController::class, 'destroy']);
        Route::post('/pangkat/cariPangkat', [PangkatController::class, 'cariPangkat']);

        // URL master pangkat dan master golongan dengan ajax
        Route::post('/master_pangkat/store', [MasterPangkatController::class, 'store']);
        Route::get('/master_pangkat/view_form_edit_master_pangkat/{masterPangkat}', [MasterPangkatController::class, 'edit']);
        Route::put('/master_pangkat/edit_master_pangkat/{masterPangkat}', [MasterPangkatController::class, 'update']);
        Route::delete('/master_pangkat/delete_master_pangkat/{masterPangkat}', [MasterPangkatController::class, 'destroy']);
        Route::post('/master_golongan/store', [MasterGolonganController::class, 'store']);
        Route::get('/master_golongan/view_form_edit_master_golongan/{masterGolongan}', [MasterGolonganController::class, 'edit']);
        Route::put('/master_golongan/edit_master_golongan/{masterGolongan}', [MasterGolonganController::class, 'update']);
        Route::delete('/master_golongan/delete_master_golongan/{masterGolongan}', [MasterGolonganController::class, 'destroy']);

        // URL Kepegawaian hukuman
        Route::get('/hukuman', [HukumanController::class, 'index']);
        Route::get('/hukuman/view_form_tambah_hukuman', [HukumanController::class, 'create']);
        Route::post('/hukuman/tambah_data_hukuman', [HukumanController::class, 'store']);
        Route::get('/hukuman/view_form_edit_hukuman/{hukuman}', [HukumanController::class, 'edit']);
        Route::put('/hukuman/edit_data_hukuman/{hukuman}', [HukumanController::class, 'update']);
        Route::delete('/hukuman/delete_data_hukuman/{hukuman}', [HukumanController::class, 'destroy']);
        Route::get('/hukuman/download_sk/{hukuman}', [HukumanController::class, 'downloadSK']);
        Route::post('/hukuman/cariHukuman', [HukumanController::class, 'cariHukuman']);

        // URL Kepegawaian Diklat
        Route::get('/diklat', [DiklatController::class, 'index']);
        Route::get('/diklat/view_form_tambah_diklat', [DiklatController::class, 'create']);
        Route::post('/diklat/tambah_diklat', [DiklatController::class, 'store']);
        Route::get('/diklat/view_form_edit_diklat/{diklat}', [DiklatController::class, 'edit']);
        Route::put('/diklat/edit_diklat/{diklat}', [DiklatController::class, 'update']);
        Route::delete('/diklat/delete_data_diklat/{diklat}', [DiklatController::class, 'destroy']);
        Route::get('/diklat/download_sertifikat_diklat/{diklat}', [DiklatController::class, 'downloadSertifikatDiklat']);
        Route::post('/diklat/cariDiklat', [DiklatController::class, 'cariDiklat']);

        // URL Kepegawaian Rencana Diklat
        Route::prefix('rencana_diklat')->name('rencana_diklat.')->group(function () {
            Route::get('/', [RencanaDiklatController::class, 'index'])->name('index');
            Route::get('/view_form_tambah_rencana_diklat', [RencanaDiklatController::class, 'create'])->name('create');
            Route::post('/tambah_rencana_diklat', [RencanaDiklatController::class, 'store'])->name('store');
            Route::get('/view_form_edit_rencana_diklat/{rencanaDiklat}', [RencanaDiklatController::class, 'edit'])->name('edit');
            Route::put('/edit_rencana_diklat/{rencanaDiklat}', [RencanaDiklatController::class, 'update'])->name('update');
            Route::delete('/delete_data_rencana_diklat/{rencanaDiklat}', [RencanaDiklatController::class, 'destroy'])->name('destroy');
            Route::post('/search', [RencanaDiklatController::class, 'search'])->name('search');
        });

        // URL Kepegawaian penghargaan
        Route::get('/penghargaan', [PenghargaanController::class, 'index']);
        Route::get('/penghargaan/view_form_tambah_penghargaan', [PenghargaanController::class, 'create']);
        Route::post('/penghargaan/tambah_penghargaan', [PenghargaanController::class, 'store']);
        Route::get('/penghargaan/view_form_edit_penghargaan/{penghargaan}', [PenghargaanController::class, 'edit']);
        Route::put('/penghargaan/edit_penghargaan/{penghargaan}', [PenghargaanController::class, 'update']);
        Route::delete('/penghargaan/delete_data_penghargaan/{penghargaan}', [PenghargaanController::class, 'destroy']);
        Route::get('/penghargaan/download_sertifikat/{penghargaan}', [PenghargaanController::class, 'downloadSertifikat']);
        Route::post('/penghargaan/cariPenghargaan', [PenghargaanController::class, 'cariPenghargaan']);

        // URL Kepegawaian Penugasan Luar Negeri
        Route::get('/penugasan_ln', [PenugasanLuarNegeriController::class, 'index']);
        Route::get('/penugasan_ln/view_form_tambah_penugasan', [PenugasanLuarNegeriController::class, 'create']);
        Route::post('/penugasan_ln/tambah_penugasan', [PenugasanLuarNegeriController::class, 'store']);
        Route::get('/penugasan_ln/view_form_edit_penugasan/{penugasanLuarNegeri}', [PenugasanLuarNegeriController::class, 'edit']);
        Route::put('/penugasan_ln/edit_penugasan/{penugasanLuarNegeri}', [PenugasanLuarNegeriController::class, 'update']);
        Route::delete('/penugasan_ln/delete_data_penghargaan/{penugasanLuarNegeri}', [PenugasanLuarNegeriController::class, 'destroy']);
        Route::get('/penugasan_ln/download_surat_tugas/{penugasanLuarNegeri}', [PenugasanLuarNegeriController::class, 'download']);
        Route::post('/penugasan_ln/cariPenugasanLn', [PenugasanLuarNegeriController::class, 'cariPenugasanLn']);

        // URL Kepegawaian Seminar
        Route::get('/seminar', [SeminarController::class, 'index']);
        Route::get('/seminar/view_form_tambah_seminar', [SeminarController::class, 'create']);
        Route::post('/seminar/tambah_seminar', [SeminarController::class, 'store']);
        Route::get('/seminar/view_form_edit_seminar/{seminar}', [SeminarController::class, 'edit']);
        Route::put('/seminar/edit_seminar/{seminar}', [SeminarController::class, 'update']);
        Route::get('/seminar/download_piagam/{seminar}', [SeminarController::class, 'downloadPiagam']);
        Route::delete('/seminar/delete_seminar/{seminar}', [SeminarController::class, 'destroy']);
        Route::post('/seminar/cariSeminar', [SeminarController::class, 'cariSeminar']);


        // URL Kepegawaian Cuti
        Route::get('/cuti', [CutiController::class, 'index']);
        Route::get('/cuti/view_form_tambah_cuti', [CutiController::class, 'create']);
        Route::post('/cuti/tambah_cuti', [CutiController::class, 'store']);
        Route::get('/cuti/view_form_edit_cuti/{cuti}', [CutiController::class, 'edit']);
        Route::put('/cuti/edit_riwayat_cuti/{cuti}', [CutiController::class, 'update']);
        Route::delete('/cuti/delete_cuti/{cuti}', [CutiController::class, 'destroy']);
        Route::post('/cuti/approve/{cuti}', [CutiController::class, 'approve'])->name('cuti.approve');
        Route::post('/cuti/reject/{cuti}', [CutiController::class, 'reject'])->name('cuti.reject');
        Route::get('/audit_trail/riwayat_aktivitas', [AktivitasLogController::class, 'index'])->name('audit_trail.index');
        Route::get('/cuti/download_surat_cuti/{cuti}', [CutiController::class, 'downloadSuratCuti']);
        Route::post('/cuti/cariCuti', [CutiController::class, 'cariCuti']);


        // URL Kepegawaian Latihan jabatan
        Route::get('/latihan_jabatan', [LatihanJabatanController::class, 'index']);
        Route::get('/latihan_jabatan/view_form_tambah_latihan_jabatan', [LatihanJabatanController::class, 'create']);
        Route::post('/latihan_jabatan/tambah_latihan_jabatan', [LatihanJabatanController::class, 'store']);
        Route::get('/latihan_jabatan/view_form_edit_latihan_jabatan/{latihanJabatan}', [LatihanJabatanController::class, 'edit']);
        Route::put('/latihan_jabatan/edit_latihan_jabatan/{latihanJabatan}', [LatihanJabatanController::class, 'update']);
        Route::get('/latihan_jabatan/download_sertifikat/{latihanJabatan}', [LatihanJabatanController::class, 'downloadSertifikat']);
        Route::delete('/latihan_jabatan/delete_latihan_jabatan/{latihanJabatan}', [LatihanJabatanController::class, 'destroy']);
        Route::post('/latihan_jabatan/cariLatihanJabatan', [LatihanJabatanController::class, 'cariLatihanJabatan']);

        // URL kepegawaian mutasi
        Route::get('/mutasi', [MutasiController::class, 'index']);
        Route::get('/mutasi/view_form_tambah_mutasi', [MutasiController::class, 'create']);
        Route::post('/mutasi/tambah_mutasi', [MutasiController::class, 'store']);
        Route::get('/mutasi/view_form_edit_mutasi/{mutasi}', [MutasiController::class, 'edit']);
        Route::put('/mutasi/edit_mutasi/{mutasi}', [MutasiController::class, 'update']);
        Route::delete('/mutasi/delete_mutasi/{mutasi}', [MutasiController::class, 'destroy']);
        Route::get('/mutasi/download_sk/{mutasi}', [MutasiController::class, 'downloadSkMutasi']);
        Route::post('/mutasi/cariMutasi', [MutasiController::class, 'cariMutasi']);

        // URL untuk kepegawaian tunjangan
        Route::get('/tunjangan', [TunjanganController::class, 'index']);
        Route::get('/tunjangan/view_form_tambah_tunjangan', [TunjanganController::class, 'create']);
        Route::post('/tunjangan/tambah_tunjangan', [TunjanganController::class, 'store']);
        Route::get('/tunjangan/view_form_edit_tunjangan/{tunjangan}', [TunjanganController::class, 'edit']);
        Route::put('/tunjangan/edit_tunjangan/{tunjangan}', [TunjanganController::class, 'update']);
        Route::delete('/tunjangan/delete_data_tunjangan/{tunjangan}', [TunjanganController::class, 'destroy']);
        Route::post('/tunjangan/cariTunjangan', [TunjanganController::class, 'cariTunjangan']);


        // URL untuk kepegawaian izin kawin
        Route::get('/izin_kawin', [IzinKawinController::class, 'index']);
        Route::get('/izin_kawin/view_form_tambah_izin_kawin', [IzinKawinController::class, 'create']);
        Route::post('/izin_kawin/tambah_izin_kawin', [IzinKawinController::class, 'store']);
        Route::get('/izin_kawin/view_form_edit_izin_kawin/{izin_kawin}', [IzinKawinController::class, 'edit']);
        Route::put('/izin_kawin/edit_izin_kawin/{izin_kawin}', [IzinKawinController::class, 'update']);
        Route::delete('/izin_kawin/delete_izin_kawin/{izin_kawin}', [IzinKawinController::class, 'destroy']);
        Route::post('/izin_kawin/cariIzinKawin', [IzinKawinController::class, 'cariIzinKawin']);
    });

    // SKP Prestasi Kerja
    Route::prefix('skp_prestasi_kerja')->group(function() {
        Route::get('/data_prestasi_kerja', [PrestasiKerjaController::class, 'index']);
        Route::get('/view_form_tambah_prestasi_kerja', [PrestasiKerjaController::class, 'create']);
        Route::post('/tambah_prestasi_kerja', [PrestasiKerjaController::class, 'store']);
        Route::get('/view_form_edit_prestasi_kerja/{prestasiKerja}', [PrestasiKerjaController::class, 'edit']);
        Route::put('/edit_prestasi_kerja/{prestasiKerja}', [PrestasiKerjaController::class, 'update']);
        Route::delete("/delete_data_prestasi_kerja/{prestasiKerja}", [PrestasiKerjaController::class, 'destroy']);
        Route::post('/cariPrestasiKerja', [PrestasiKerjaController::class, 'cariPrestasiKerja']);
    });

    // TPP
    Route::prefix('tpp')->group(function() {
        Route::get('/input_tpp', [TppController::class, 'index'])->name('tpp.index');
        Route::get('/tambah_tpp', [TppController::class, 'create'])->name('tpp.create');
        Route::post('/simpan_tpp', [TppController::class, 'store'])->name('tpp.store');
        Route::get('/edit_tpp/{tpp}', [TppController::class, 'edit'])->name('tpp.edit');
        Route::put('/update_tpp/{tpp}', [TppController::class, 'update'])->name('tpp.update');
        Route::delete('/hapus_tpp/{tpp}', [TppController::class, 'destroy'])->name('tpp.destroy');
        Route::get('/laporan_bulanan', [TppController::class, 'laporanBulanan'])->name('tpp.laporan_bulanan');
        Route::post('/cariTpp', [TppController::class, 'cariTpp'])->name('tpp.cari');
        Route::get('/exportExcelLaporanTpp', [TppController::class, 'exportExcelLaporanTpp'])->name('tpp.export_excel_laporan_tpp');
    });

    Route::prefix('notifikasi_kgb')->group(function () {
        Route::get('/data_notifikasi_kgb', [KGBController::class, 'index']);
        Route::get('/view_buat_kgb', [KGBController::class, 'create']);
        Route::post('/buat_kgb', [KGBController::class, 'store']);
        Route::get('/export_excel_kgb', [KGBController::class, 'exportExcelKGB'])->name('kgb.export_excel');
    });

    // Rekapitulasi
    Route::prefix('rekapitulasi')->group(function() {
        Route::get('/opd_skpd_unit_kerja', [RekapitulasiController::class, 'rekapUnitKerja']);
        Route::get('/golongan', [RekapitulasiController::class, 'rekapGolongan']);
        Route::get('/pangkat', [RekapitulasiController::class, 'rekapPangkat']);
        Route::get('/jabatan', [RekapitulasiController::class, 'rekapJabatan']);
        Route::get('/eselon', [RekapitulasiController::class, 'rekapEselon']);
        Route::get('/status_kepegawaian', [RekapitulasiController::class, 'rekapStatusKepegawaian']);
        Route::get('/agama', [RekapitulasiController::class, 'rekapAgama']);
        Route::get('/jenis_kelamin', [RekapitulasiController::class, 'rekapJenisKelamin']);
        Route::get('/status_pernikahan', [RekapitulasiController::class, 'rekapStatusPernikahan']);
        Route::get('/pendidikan_terakhir', [RekapitulasiController::class, 'rekapPendidikanAkhir']);
        Route::get('/pdf/{jenis}', [RekapitulasiPdfController::class, 'unduh'])
            ->whereIn('jenis', ['opd_skpd_unit_kerja', 'golongan', 'pangkat', 'jabatan', 'eselon',
                                'status_kepegawaian', 'agama', 'jenis_kelamin', 'status_pernikahan',
                                'pendidikan_terakhir'])
            ->name('rekapitulasi.pdf');
    });

    // Report
    Route::prefix('report')->group(function() {
        Route::get('/diklat_gap', [DiklatGapReportController::class, 'index'])->name('report.diklat_gap');
        Route::get('/diklat_gap/unit', [DiklatGapReportController::class, 'unitAggregate'])->name('report.diklat_gap.unit');
        Route::get('/diklat_gap/unit/print', [DiklatGapReportController::class, 'printUnitAggregate'])->name('report.diklat_gap.unit.print');
        Route::get('/diklat_gap/unit/export', [DiklatGapReportController::class, 'exportUnitAggregate'])->name('report.diklat_gap.unit.export');
        Route::get('/nominatif', [ReportController::class, 'reportNominatif'])->name('report.nominatif');
        Route::get('/nominatif/print', [ReportController::class, 'printNominatif'])->name('report.nominatif.print');
        Route::get('/duk', [ReportController::class, 'reportDUK'])->name('report.duk');
        Route::get('/duk/print', [ReportController::class, 'printDUK'])->name('report.duk.print');
        Route::get('/bezetting', [ReportController::class, 'reportBezetting'])->name('report.bezetting');
        Route::get('/bezetting/print', [ReportController::class, 'printBezetting'])->name('report.bezetting.print');
        Route::get('/keadaan_pegawai', [ReportController::class, 'reportKeadaanPegawai'])->name('report.keadaan_pegawai');
        Route::get('/keadaan_pegawai/print', [ReportController::class, 'printKeadaanPegawai'])->name('report.keadaan_pegawai.print');
        Route::get('/pensiun', [ReportController::class, 'reportPensiun'])->name('report.pensiun');
        Route::get('/pdf/{jenis}', [ReportPdfController::class, 'unduh'])
            ->whereIn('jenis', ['nominatif', 'duk', 'keadaan_pegawai', 'bezetting', 'pensiun'])
            ->name('report.pdf');
    });

    Route::middleware('role:superadmin')->group(function () {
        Route::get('/backup_data', [BackupDatabaseController::class, 'BackupDatabasePages']);
        Route::get('/backup_data/download', [BackupDatabaseController::class, 'download'])->name('backup_data.download');
    });

});
