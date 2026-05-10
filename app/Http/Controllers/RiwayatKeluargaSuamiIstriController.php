<?php

namespace App\Http\Controllers;

use App\Models\RiwayatKeluargaSuamiIstri;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RiwayatKeluargaSuamiIstriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $riwayatkeluargaSuamiIstri = RiwayatKeluargaSuamiIstri::with('pegawai')
                ->whereHas('pegawai', function($query) use ($user) {
                    $query->where('unit_kerja_id', $user->unit_kerja_id);
                })
                ->paginate(5);
        } elseif ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            $riwayatkeluargaSuamiIstri = $myPegawai
                ? RiwayatKeluargaSuamiIstri::with('pegawai')->where('pegawai_id', $myPegawai->id)->paginate(5)
                : RiwayatKeluargaSuamiIstri::whereRaw('1=0')->paginate(5);
        } else {
            $riwayatkeluargaSuamiIstri = RiwayatKeluargaSuamiIstri::with('pegawai')->paginate(5);
        }

        return view('pages.dashboard.riwayat_keluarga.suami_istri.indexKeluargaSuami_Istri', [
            'riwayatkeluargaSuamiIstri' => $riwayatkeluargaSuamiIstri
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
        } elseif ($user->role === 'pegawai') {
            $pegawai = Pegawai::where('user_id', $user->id)->get();
        } else {
            $pegawai = Pegawai::all();
        }

        return view("pages.dashboard.riwayat_keluarga.suami_istri.tambahKeluargaSuami_Istri", [
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
            'no_ktp_nik' => 'required|string',
            'nama' => 'required|string',
            'tgl_lahir' => 'required|date',
            'tempat_lahir' => 'required|string',
            'pendidikan' => 'required|string',
            'pekerjaan' => 'required|string',
            'status_hubungan' => 'required'
        ]);

        // Pastikan pegawai role hanya bisa simpan data miliknya sendiri
        $user = Auth::user();
        if ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if (!$myPegawai || $validateData['pegawai_id'] != $myPegawai->id) {
                abort(403, 'Akses ditolak');
            }
        }

        try {
            DB::beginTransaction();

            RiwayatKeluargaSuamiIstri::create($validateData);

            DB::commit();

            return redirect('/riwayat_keluarga/suami_istri')->with('success', 'Berhasil menambahkan data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error("Gagal menyimpan data " . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan!');
        }

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RiwayatKeluargaSuamiIstri $riwayatKeluargaSuamiIstri)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $pegawai = Pegawai::where('unit_kerja_id', $user->unit_kerja_id)->get();
        } elseif ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if (!$myPegawai || $riwayatKeluargaSuamiIstri->pegawai_id != $myPegawai->id) {
                abort(403, 'Akses ditolak');
            }
            $pegawai = collect([$myPegawai]);
        } else {
            $pegawai = Pegawai::all();
        }

        return view("pages.dashboard.riwayat_keluarga.suami_istri.editKeluargaSuami_Istri", [
            'pegawai' => $pegawai,
            'riwayatKeluargaSuamiIstri' => $riwayatKeluargaSuamiIstri
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RiwayatKeluargaSuamiIstri $riwayatKeluargaSuamiIstri)
    {
        $validateData = $request->validate([
            'pegawai_id' => 'required|exists:tb_pegawai,id',
            'no_ktp_nik' => 'required|string',
            'nama' => 'required|string',
            'tgl_lahir' => 'required|date',
            'tempat_lahir' => 'required|string',
            'pendidikan' => 'required|string',
            'pekerjaan' => 'required|string',
            'status_hubungan' => 'required'
        ]);

        // Pastikan pegawai role hanya bisa update data miliknya sendiri
        $user = Auth::user();
        if ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if (!$myPegawai || $riwayatKeluargaSuamiIstri->pegawai_id != $myPegawai->id) {
                abort(403, 'Akses ditolak');
            }
        }

        try {
            DB::beginTransaction();

            $riwayatKeluargaSuamiIstri->update($validateData);

            DB::commit();

            return redirect('/riwayat_keluarga/suami_istri')->with('success', 'Berhasil mengubah data!');


        } catch(Exception $e) {
            DB::rollBack();

            Log::error("Gagal mengubah data : " . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan sistem!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RiwayatKeluargaSuamiIstri $riwayatKeluargaSuamiIstri)
    {
        // Pastikan pegawai role hanya bisa hapus data miliknya sendiri
        $user = Auth::user();
        if ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if (!$myPegawai || $riwayatKeluargaSuamiIstri->pegawai_id != $myPegawai->id) {
                abort(403, 'Akses ditolak');
            }
        }

        $riwayatKeluargaSuamiIstri->delete();

        return redirect('/riwayat_keluarga/suami_istri')->with('success', 'Berhasil menghapus data');
    }

    public function cariPegawaiSuamiIstri(Request $request)
    {
        $user = Auth::user();

        $query = RiwayatKeluargaSuamiIstri::with('pegawai');

        // Filter berdasarkan role admin
        if ($user->role === 'admin') {
            $query->whereHas('pegawai', function($q) use ($user) {
                $q->where('unit_kerja_id', $user->unit_kerja_id);
            });
        } elseif ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if ($myPegawai) {
                $query->where('pegawai_id', $myPegawai->id);
            } else {
                $query->whereRaw('1=0');
            }
        }

        // Filter search
        if($request->cariPegawaiSuamiIstri) {
            $query->where(function($q) use ($request) {

                // dari tabel latihan jabatan
                $q->where('nama', 'like', '%' . $request->cariPegawaiSuamiIstri . '%');

                $q->orWhere('pendidikan', 'like', '%' . $request->cariPegawaiSuamiIstri . '%');

                $q->orWhere('pekerjaan', 'like', '%' . $request->cariPegawaiSuamiIstri . '%')

                // dari relasi pegawai
                ->orWhereHas('pegawai', function($q2) use ($request) {
                    $q2->where('nama', 'like', '%' . $request->cariPegawaiSuamiIstri . '%');
                });

            });
        }

        $riwayatkeluargaSuamiIstri = $query->paginate(5);

        return view("pages.dashboard.riwayat_keluarga.suami_istri.indexKeluargaSuami_Istri", [
            'riwayatkeluargaSuamiIstri' => $riwayatkeluargaSuamiIstri
        ]);
    }
}
