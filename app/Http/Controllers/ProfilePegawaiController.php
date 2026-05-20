<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Pangkat;
use App\Models\Jabatan;
use App\Models\RiwayatPendidikanSekolah;
use App\Models\RiwayatPendidikanLanjut;
use App\Models\RiwayatPendidikanBahasa;
use App\Models\RiwayatKeluargaSuamiIstri;
use App\Models\RiwayatKeluargaAnak;
use App\Models\RiwayatKeluargaOrangtua;
use App\Models\Hukuman;
use App\Models\Penghargaan;
use App\Models\Diklat;
use App\Models\RencanaDiklat;
use App\Models\Seminar;
use App\Models\LatihanJabatan;
use App\Models\Cuti;
use App\Models\Tunjangan;
use App\Models\Mutasi;
use App\Models\IzinKawin;
use App\Models\PrestasiKerja;
use App\Models\Tpp;
use App\Models\UnitKerja;
use App\Models\InstansiLembaga;
use App\Models\PenugasanLuarNegeri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Support\ProfilePegawaiUi;

class ProfilePegawaiController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $pegawai = Pegawai::with(['unit_kerja'])
            ->where('user_id', $user->id)
            ->first();

        if (!$pegawai) {
            return view('pages.dashboard.profile_pegawai.indexProfilePegawai', [
                'pegawai'     => null,
                'user'        => $user,
                'pangkat'     => null,
                'jabatan'     => null,
                'pendidikan'  => collect(),
                'keluarga'    => [],
                'kepegawaian' => [],
            ]);
        }

        // Pangkat terakhir
        $pangkat = Pangkat::with(['master_pangkat', 'master_golongan'])
            ->where('pegawai_id', $pegawai->id)
            ->latest('tmt_pangkat_mulai')
            ->first();

        // Jabatan aktif
        $jabatan = Jabatan::with(['master_jabatan', 'master_eselon'])
            ->where('pegawai_id', $pegawai->id)
            ->latest('tmt_jabatan_mulai')
            ->first();

        // Semua riwayat pendidikan
        $pendidikanSekolah = RiwayatPendidikanSekolah::where('pegawai_id', $pegawai->id)->get();
        $pendidikanLanjut  = RiwayatPendidikanLanjut::where('pegawai_id', $pegawai->id)->get();
        $pendidikanBahasa  = RiwayatPendidikanBahasa::where('pegawai_id', $pegawai->id)->get();

        // Keluarga
        $suamiIstri = RiwayatKeluargaSuamiIstri::where('pegawai_id', $pegawai->id)->get();
        $anak       = RiwayatKeluargaAnak::where('pegawai_id', $pegawai->id)->get();
        $orangTua   = RiwayatKeluargaOrangtua::where('pegawai_id', $pegawai->id)->get();

        // Kepegawaian
        $hukuman      = Hukuman::where('pegawai_id', $pegawai->id)->get();
        $penghargaan  = Penghargaan::where('pegawai_id', $pegawai->id)->get();
        $diklat       = Diklat::with('rencanaDiklat')->where('pegawai_id', $pegawai->id)->get();
        $rencanaDiklat = RencanaDiklat::with('diklat')->where('pegawai_id', $pegawai->id)->get();
        $seminar      = Seminar::where('pegawai_id', $pegawai->id)->get();
        $latihanJab   = LatihanJabatan::where('pegawai_id', $pegawai->id)->get();
        $cuti         = Cuti::where('pegawai_id', $pegawai->id)->get();
        $tunjangan    = Tunjangan::where('pegawai_id', $pegawai->id)->get();
        $mutasi       = Mutasi::where('pegawai_id', $pegawai->id)->get();
        $izinKawin    = IzinKawin::where('pegawai_id', $pegawai->id)->get();

        // SKP & TPP
        $skp          = PrestasiKerja::where('pegawai_id', $pegawai->id)->orderBy('tahun_periode', 'desc')->get();
        $tpp          = Tpp::where('pegawai_id', $pegawai->id)->orderBy('tahun', 'desc')->orderBy('periode', 'desc')->get();

        // Semua riwayat pangkat (KGB)
        $allPangkat   = Pangkat::with(['master_pangkat', 'master_golongan'])
            ->where('pegawai_id', $pegawai->id)
            ->orderBy('tmt_pangkat_mulai', 'desc')
            ->get();

        // Usia
        $usia = $pegawai->tgl_lahir
            ? \Carbon\Carbon::parse($pegawai->tgl_lahir)->diff(now())
            : null;

        $profileTabs = ProfilePegawaiUi::tabs();
        $initialTab = ProfilePegawaiUi::resolveTab(request('tab'));
        $toneClasses = ProfilePegawaiUi::toneClasses();
        $summaryCards = ProfilePegawaiUi::summaryCards($pegawai, $pangkat, $jabatan);
        $kepegawaianLinks = ProfilePegawaiUi::kepegawaianLinks([
            'pangkat' => $pangkat ? 1 : 0,
            'jabatan' => $jabatan ? 1 : 0,
            'allPangkat' => $allPangkat->count(),
            'hukuman' => $hukuman->count(),
            'penghargaan' => $penghargaan->count(),
            'seminar' => $seminar->count(),
            'latihanJab' => $latihanJab->count(),
            'diklat' => $diklat->count(),
            'cuti' => $cuti->count(),
            'tunjangan' => $tunjangan->count(),
            'mutasi' => $mutasi->count(),
            'izinKawin' => $izinKawin->count(),
        ]);
        $biodataFields = ProfilePegawaiUi::biodataFields($pegawai, $usia);

        return view('pages.dashboard.profile_pegawai.indexProfilePegawai', compact(
            'user', 'pegawai', 'pangkat', 'jabatan', 'usia',
            'pendidikanSekolah', 'pendidikanLanjut', 'pendidikanBahasa',
            'suamiIstri', 'anak', 'orangTua',
            'hukuman', 'penghargaan', 'diklat', 'rencanaDiklat', 'seminar',
            'latihanJab', 'cuti', 'tunjangan', 'mutasi', 'izinKawin',
            'skp', 'tpp', 'allPangkat',
            'profileTabs', 'initialTab', 'toneClasses', 'summaryCards',
            'kepegawaianLinks', 'biodataFields'
        ));
    }

    /**
     * Print biodata pegawai.
     */
    public function print()
    {
        $user    = Auth::user();
        $pegawai = Pegawai::with(['unit_kerja'])
            ->where('user_id', $user->id)
            ->first();

        if (!$pegawai) {
            return redirect('/profile_saya')->with('error', 'Data pegawai tidak ditemukan!');
        }

        // Pangkat terakhir
        $pangkat = Pangkat::with(['master_pangkat', 'master_golongan'])
            ->where('pegawai_id', $pegawai->id)
            ->latest('tmt_pangkat_mulai')
            ->first();

        // Jabatan aktif
        $jabatan = Jabatan::with(['master_jabatan', 'master_eselon'])
            ->where('pegawai_id', $pegawai->id)
            ->latest('tmt_jabatan_mulai')
            ->first();

        // Semua riwayat jabatan
        $allJabatan = Jabatan::with(['master_jabatan'])
            ->where('pegawai_id', $pegawai->id)
            ->orderBy('tmt_jabatan_mulai', 'desc')
            ->get();

        // Semua riwayat pangkat
        $allPangkat = Pangkat::with(['master_pangkat', 'master_golongan'])
            ->where('pegawai_id', $pegawai->id)
            ->orderBy('tmt_pangkat_mulai', 'desc')
            ->get();

        // Pendidikan
        $pendidikanSekolah = RiwayatPendidikanSekolah::where('pegawai_id', $pegawai->id)->get();
        $pendidikanLanjut  = RiwayatPendidikanLanjut::where('pegawai_id', $pegawai->id)->get();
        $pendidikanBahasa  = RiwayatPendidikanBahasa::where('pegawai_id', $pegawai->id)->get();

        // Keluarga
        $suamiIstri = RiwayatKeluargaSuamiIstri::where('pegawai_id', $pegawai->id)->get();
        $anak       = RiwayatKeluargaAnak::where('pegawai_id', $pegawai->id)->get();
        $orangTua   = RiwayatKeluargaOrangtua::where('pegawai_id', $pegawai->id)->get();

        // Kepegawaian
        $hukuman     = Hukuman::where('pegawai_id', $pegawai->id)->get();
        $penghargaan = Penghargaan::where('pegawai_id', $pegawai->id)->get();
        $penugasanLN = PenugasanLuarNegeri::where('pegawai_id', $pegawai->id)->get();

        // Instansi
        $instansi = InstansiLembaga::first();

        // Diklat
        $rencanaDiklat = RencanaDiklat::where('pegawai_id', $pegawai->id)->get();
        $diklat = Diklat::with('rencanaDiklat')->where('pegawai_id', $pegawai->id)->get();
        $diklatPrintSummary = [
            'planned_count' => $rencanaDiklat->count(),
            'realized_linked_count' => $diklat->whereNotNull('rencana_diklat_id')->count(),
            'not_realized_count' => RencanaDiklat::where('pegawai_id', $pegawai->id)
                ->whereDoesntHave('diklat')
                ->count(),
            'out_of_plan_count' => $diklat->whereNull('rencana_diklat_id')->count(),
        ];

        return view('pages.dashboard.profile_pegawai.printBiodataPegawai', compact(
            'pegawai', 'pangkat', 'jabatan', 'allJabatan', 'allPangkat',
            'pendidikanSekolah', 'pendidikanLanjut', 'pendidikanBahasa',
            'suamiIstri', 'anak', 'orangTua',
            'hukuman', 'penghargaan', 'penugasanLN',
            'instansi', 'rencanaDiklat', 'diklat', 'diklatPrintSummary'
        ));
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit()
    {
        $user    = Auth::user();
        $pegawai = Pegawai::with(['unit_kerja'])
            ->where('user_id', $user->id)
            ->first();

        if (!$pegawai) {
            return redirect('/profile_saya')->with('error', 'Data pegawai tidak ditemukan!');
        }

        $unitKerja = UnitKerja::all();

        return view('pages.dashboard.profile_pegawai.editProfilePegawai', [
            'pegawai'   => $pegawai,
            'unitKerja' => $unitKerja,
        ]);
    }

    /**
     * Update the profile.
     */
    public function update(Request $request)
    {
        $user    = Auth::user();
        $pegawai = Pegawai::where('user_id', $user->id)->first();

        if (!$pegawai) {
            return redirect('/profile_saya')->with('error', 'Data pegawai tidak ditemukan!');
        }

        $validateData = $request->validate([
            'nip' => 'required|string',
            'nama' => 'required|string',
            'unit_kerja_id' => 'required|exists:tb_unit_kerja,id',
            'gelar' => 'nullable|string',
            'tmpt_lahir' => 'required|string',
            'tgl_lahir' => 'required|date',
            'jenis_kelamin' => 'required',
            'agama' => 'required',
            'golongan_darah' => 'required',
            'status_pernikahan' => 'required',
            'nik' => 'required|string',
            'alamat' => 'required|string',
            'no_hp' => 'required|string',
            'email' => 'required|email',
            'email_gov' => 'required|email',
            'no_npwp' => 'required|string',
            'no_bpjs' => 'required|string',
            'status_kepegawaian' => 'required',
            'karpeg' => 'required|string',
            'no_sk_cpns' => 'nullable',
            'tmt_cpns' => 'required|date',
            'no_sk_pns' => 'nullable',
            'tmt_pns' => 'required|date',
            'gol_awal' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nilai_tpp' => 'required'
        ]);

        try {
            DB::beginTransaction();

            // Handle foto upload
            if($request->hasFile('foto')) {
                // Delete old photo if exists
                if($pegawai->foto && $pegawai->foto !== '/storage/images/default.jpg') {
                    $oldPath = str_replace('/storage/', '', $pegawai->foto);
                    if(Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }

                $file = $request->file('foto');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('images', $fileName, 'public');
                $validateData['foto'] = '/storage/' . $path;
            }

            $pegawai->update($validateData);

            DB::commit();

            return redirect('/profile_saya')->with('success', 'Berhasil mengubah data profile!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error("Gagal mengubah data profile : " . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan dengan sistem!');
        }
    }

    /**
     * Upload/Update foto profile.
     */
    public function uploadFoto(Request $request)
    {
        $user    = Auth::user();
        $pegawai = Pegawai::where('user_id', $user->id)->first();

        if (!$pegawai) {
            return back()->with('error', 'Data pegawai tidak ditemukan!');
        }

        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Delete old photo if exists
            if($pegawai->foto && $pegawai->foto !== '/storage/images/default.jpg') {
                $oldPath = str_replace('/storage/', '', $pegawai->foto);
                if(Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $file = $request->file('foto');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('images', $fileName, 'public');

            $pegawai->update([
                'foto' => '/storage/' . $path
            ]);

            DB::commit();

            return back()->with('success', 'Berhasil mengupload foto!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error("Gagal mengupload foto : " . $e->getMessage());

            return back()->with('error', 'Error, terjadi kesalahan dengan sistem!');
        }
    }
}
