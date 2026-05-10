<?php

namespace App\Http\Controllers;

use App\Models\RiwayatKeluargaOrangTua;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RiwayatKeluargaOrangTuaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $riwayatKeluargaOrangTua = RiwayatKeluargaOrangtua::with('pegawai')
                ->whereHas('pegawai', function($query) use ($user) {
                    $query->where('unit_kerja_id', $user->unit_kerja_id);
                })
                ->paginate(5);
        } elseif ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            $riwayatKeluargaOrangTua = $myPegawai
                ? RiwayatKeluargaOrangtua::with('pegawai')->where('pegawai_id', $myPegawai->id)->paginate(5)
                : RiwayatKeluargaOrangtua::whereRaw('1=0')->paginate(5);
        } else {
            $riwayatKeluargaOrangTua = RiwayatKeluargaOrangtua::with('pegawai')->paginate(5);
        }

        return view("pages.dashboard.riwayat_keluarga.orang_tua.IndexKeluargaOrangtua", [
            'riwayatKeluargaOrangTua' => $riwayatKeluargaOrangTua
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

        return view("pages.dashboard.riwayat_keluarga.orang_tua.tambahKeluargaOrangtua", [
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
            'nik' => 'required|string',
            'nama' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tgl_lahir' => 'required|date',
            'jenis_kelamin' => 'required',
            'pendidikan' => 'required',
            'pekerjaan' => 'required',
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

            RiwayatKeluargaOrangTua::create($validateData);

            DB::commit();

            return redirect("/riwayat_keluarga/orang_tua")->with('success', "Berhasil menambahkan data!");
        } catch(Exception $e) {
            DB::rollBack();

            Log::error("Gagal menyimpan data : " . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RiwayatKeluargaOrangTua $riwayatKeluargaOrangTua)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $pegawai = Pegawai::where('unit_kerja_id', $user->unit_kerja_id)->get();
        } elseif ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if (!$myPegawai || $riwayatKeluargaOrangTua->pegawai_id != $myPegawai->id) {
                abort(403, 'Akses ditolak');
            }
            $pegawai = collect([$myPegawai]);
        } else {
            $pegawai = Pegawai::all();
        }

        return view("pages.dashboard.riwayat_keluarga.orang_tua.editKeluargaOrangtua", [
            "pegawai" => $pegawai,
            'riwayatKeluargaOrangTua' => $riwayatKeluargaOrangTua
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RiwayatKeluargaOrangTua $riwayatKeluargaOrangTua)
    {
        $validateData = $request->validate([
            'pegawai_id' => 'required|exists:tb_pegawai,id',
            'nik' => 'required|string',
            'nama' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tgl_lahir' => 'required|date',
            'jenis_kelamin' => 'required',
            'pendidikan' => 'required',
            'pekerjaan' => 'required',
            'status_hubungan' => 'required'
        ]);

        // Pastikan pegawai role hanya bisa update data miliknya sendiri
        $user = Auth::user();
        if ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if (!$myPegawai || $riwayatKeluargaOrangTua->pegawai_id != $myPegawai->id) {
                abort(403, 'Akses ditolak');
            }
        }

        try {
            DB::beginTransaction();

            $riwayatKeluargaOrangTua->update($validateData);

            DB::commit();

            return redirect('/riwayat_keluarga/orang_tua')->with('success', 'Berhasil mengubah data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error("Gagal mengubah data : " . $e->getMessage());

            return back()->withInput()->with('error', "Error, terjadi kesalahan pada sistem!");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RiwayatKeluargaOrangTua $riwayatKeluargaOrangTua)
    {
        // Pastikan pegawai role hanya bisa hapus data miliknya sendiri
        $user = Auth::user();
        if ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if (!$myPegawai || $riwayatKeluargaOrangTua->pegawai_id != $myPegawai->id) {
                abort(403, 'Akses ditolak');
            }
        }

        $riwayatKeluargaOrangTua->delete();

        return redirect('/riwayat_keluarga/orang_tua')->with('success', 'Berhasil menghapus data!');
    }

    public function cariPegawaiOrangTua(Request $request)
    {
        $user = Auth::user();

        $query = RiwayatKeluargaOrangTua::with('pegawai');

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

        if($request->cariPegawaiOrangTua) {
            $query->where(function($q) use ($request) {

                // dari tabel latihan jabatan
                $q->where('nama', 'like', '%' . $request->cariPegawaiOrangTua . '%');

                $q->orWhere('pendidikan', 'like', '%' . $request->cariPegawaiOrangTua . '%');

                $q->orWhere('pekerjaan', 'like', '%' . $request->cariPegawaiOrangTua . '%')

                // dari relasi pegawai
                ->orWhereHas('pegawai', function($q2) use ($request) {
                    $q2->where('nama', 'like', '%' . $request->cariPegawaiOrangTua . '%');
                });

            });
        }

        $riwayatKeluargaOrangTua = $query->paginate(5);

        return view("pages.dashboard.riwayat_keluarga.orang_tua.IndexKeluargaOrangtua", [
            'riwayatKeluargaOrangTua' => $riwayatKeluargaOrangTua
        ]);
    }
}
