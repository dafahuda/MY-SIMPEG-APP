<?php

namespace App\Http\Controllers;

use App\Models\IzinKawin;
use App\Models\Pegawai;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class IzinKawinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $izinKawin = IzinKawin::with('pegawai')
                ->whereHas('pegawai', function($query) use ($user) {
                    $query->where('unit_kerja_id', $user->unit_kerja_id);
                })
                ->paginate(5);
        } else {
            $izinKawin = IzinKawin::with('pegawai')->paginate(5);
        }

        return view("pages.dashboard.kepegawaian.izinKawin.indexIzinKawin", [
            'izinKawin' => $izinKawin
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $pegawai = Pegawai::where('unit_kerja_id', $user->unit_kerja_id)->get();
        } else {
            $pegawai = Pegawai::all();
        }

        return view("pages.dashboard.kepegawaian.izinKawin.tambahIzinKawin", [
            'pegawai' => $pegawai
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'pegawai_id' => 'required|exists:tb_pegawai,id',
            'no_surat_izin_perkawinan' => 'required|string',
            'tgl_izin_surat_perkawinan' => 'required|date',
            'kebangsaan_pegawai' => 'required|string',
            'nama_wali_bapak_pegawai' => 'required|string',
            'pekerjaan_wali_bapak_pegawai' => 'required|string',
            'alamat_wali_bapak' => 'required|string',
            'nama_wali_ibu_pegawai' => 'required|string',
            'pekerjaan_wali_ibu_pegawai' => 'required|string',
            'alamat_wali_ibu_pegawai' => 'required|string',
            'nama_calon_suami_istri' => 'required|string',
            'tempat_lahir_calon_suami_istri' => 'required|string',
            'tgl_lahir_calon_suami_istri' => 'required|string',
            'pekerjaan_calon_suami_istri' => 'required|string',
            'nip_nik_calon_suami_istri' => 'required|string',
            'pangkat_golongan_calon_suami_istri' => 'required|string',
            'jabatan_calon_suami_istri' => 'required|string',
            'instansi_calon_suami_istri' => 'required|string',
            'kebangsaan_calon_suami_istri' => 'required|string',
            'agama_calon_suami_istri' => 'required|string',
            'alamat_calon_suami_istri' => 'required|string',
            'nama_wali_bapak_calon_suami_istri' => 'required|string',
            'pekerjaan_wali_bapak_calon_suami_istri' => 'required|string',
            'alamat_wali_bapak_calon_suami_istri' => 'required|string',
            'nama_wali_ibu_calon_suami_istri' => 'required|string',
            'pekerjaan_wali_ibu_calon_suami_istri' => 'required|string',
            'alamat_wali_ibu_calon_suami_istri' => 'required|string',
            'tempat_perkawinan' => 'required|string',
            'tgl_perkawinan' => 'required|date',
            'tgl_ditetapkan_perkawinan' => 'required|date'
        ]);

        try {
            DB::beginTransaction();

            IzinKawin::create($validateData);

            DB::commit();

            return redirect('/kepegawaian/izin_kawin')->with('success', 'Berhasil menambahkan data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal menambahkan data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IzinKawin $izinKawin)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $pegawai = Pegawai::where('unit_kerja_id', $user->unit_kerja_id)->get();
        } else {
            $pegawai = Pegawai::all();
        }

        return view("pages.dashboard.kepegawaian.izinKawin.editIzinKawin", [
            'pegawai' => $pegawai,
            'izinKawin' => $izinKawin
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, IzinKawin $izinKawin)
    {
        $validateData = $request->validate([
            'pegawai_id' => 'required|exists:tb_pegawai,id',
            'no_surat_izin_perkawinan' => 'required|string',
            'tgl_izin_surat_perkawinan' => 'required|date',
            'kebangsaan_pegawai' => 'required|string',
            'nama_wali_bapak_pegawai' => 'required|string',
            'pekerjaan_wali_bapak_pegawai' => 'required|string',
            'alamat_wali_bapak' => 'required|string',
            'nama_wali_ibu_pegawai' => 'required|string',
            'pekerjaan_wali_ibu_pegawai' => 'required|string',
            'alamat_wali_ibu_pegawai' => 'required|string',
            'nama_calon_suami_istri' => 'required|string',
            'tempat_lahir_calon_suami_istri' => 'required|string',
            'tgl_lahir_calon_suami_istri' => 'required|string',
            'pekerjaan_calon_suami_istri' => 'required|string',
            'nip_nik_calon_suami_istri' => 'required|string',
            'pangkat_golongan_calon_suami_istri' => 'required|string',
            'jabatan_calon_suami_istri' => 'required|string',
            'instansi_calon_suami_istri' => 'required|string',
            'kebangsaan_calon_suami_istri' => 'required|string',
            'agama_calon_suami_istri' => 'required|string',
            'alamat_calon_suami_istri' => 'required|string',
            'nama_wali_bapak_calon_suami_istri' => 'required|string',
            'pekerjaan_wali_bapak_calon_suami_istri' => 'required|string',
            'alamat_wali_bapak_calon_suami_istri' => 'required|string',
            'nama_wali_ibu_calon_suami_istri' => 'required|string',
            'pekerjaan_wali_ibu_calon_suami_istri' => 'required|string',
            'alamat_wali_ibu_calon_suami_istri' => 'required|string',
            'tempat_perkawinan' => 'required|string',
            'tgl_perkawinan' => 'required|date',
            'tgl_ditetapkan_perkawinan' => 'required|date'
        ]);

        try {
            DB::beginTransaction();

            $izinKawin->update($validateData);

            DB::commit();

            return redirect('/kepegawaian/izin_kawin')->with('success', 'Berhasil mengubah data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal menambahkan data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IzinKawin $izinKawin)
    {
        $izinKawin->delete();

        return redirect('/kepegawaian/izin_kawin')->with('success', 'Berhasil menghapus data!');
    }

    public function cariIzinKawin(Request $request)
    {
        $user = Auth::user();

        $query = IzinKawin::with('pegawai');

        // Filter berdasarkan role admin
        if ($user->role === 'admin') {
            $query->whereHas('pegawai', function($q) use ($user) {
                $q->where('unit_kerja_id', $user->unit_kerja_id);
            });
        }

        if($request->cariIzinKawin) {
            $query->where(function($q) use ($request) {

                // dari tabel latihan jabatan
                $q->where('nama_calon_suami_istri', 'like', '%' . $request->cariIzinKawin . '%');

                $q->orWhere('tempat_perkawinan', 'like', '%' . $request->cariIzinKawin . '%')

                // dari relasi pegawai
                ->orWhereHas('pegawai', function($q2) use ($request) {
                    $q2->where('nama', 'like', '%' . $request->cariIzinKawin . '%');
                });

            });
        }

        $izinKawin = $query->paginate(5);

        return view("pages.dashboard.kepegawaian.izinKawin.indexIzinKawin", [
            'izinKawin' => $izinKawin
        ]);
    }
}
