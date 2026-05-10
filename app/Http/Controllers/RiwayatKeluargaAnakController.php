<?php

namespace App\Http\Controllers;

use App\Models\RiwayatKeluargaAnak;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RiwayatKeluargaAnakController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $riwayatKeluargaAnak = RiwayatKeluargaAnak::with('pegawai')
                ->whereHas('pegawai', function($query) use ($user) {
                    $query->where('unit_kerja_id', $user->unit_kerja_id);
                })
                ->get();
        } elseif ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            $riwayatKeluargaAnak = $myPegawai
                ? RiwayatKeluargaAnak::with('pegawai')->where('pegawai_id', $myPegawai->id)->paginate(5)
                : collect()->paginate(5);
        } else {
            $riwayatKeluargaAnak = RiwayatKeluargaAnak::with('pegawai')->paginate(5);
        }

        return view("pages.dashboard.riwayat_keluarga.anak.IndexRiwayatKeluargaAnak", [
            'riwayatKeluargaAnak' => $riwayatKeluargaAnak
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

        return view("pages.dashboard.riwayat_keluarga.anak.tambahRiwayatKeluargaAnak", [
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

            RiwayatKeluargaAnak::create($validateData);

            DB::commit();

            return redirect('/riwayat_keluarga/anak')->with('success', 'Berhasil menambahkan data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error("Gagal menyimpan data : " . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RiwayatKeluargaAnak $riwayatKeluargaAnak)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $pegawai = Pegawai::where('unit_kerja_id', $user->unit_kerja_id)->get();
        } elseif ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if (!$myPegawai || $riwayatKeluargaAnak->pegawai_id != $myPegawai->id) {
                abort(403, 'Akses ditolak');
            }
            $pegawai = collect([$myPegawai]);
        } else {
            $pegawai = Pegawai::all();
        }

        return view("pages.dashboard.riwayat_keluarga.anak.EditRiwayatKeluargaAnak", [
            'pegawai' => $pegawai,
            'riwayatKeluargaAnak' => $riwayatKeluargaAnak
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RiwayatKeluargaAnak $riwayatKeluargaAnak)
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
            if (!$myPegawai || $riwayatKeluargaAnak->pegawai_id != $myPegawai->id) {
                abort(403, 'Akses ditolak');
            }
        }

        try {
            DB::beginTransaction();

            $riwayatKeluargaAnak->update($validateData);

            DB::commit();

            return redirect("/riwayat_keluarga/anak")->with('success', 'Berhasil menambahkan data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error("Gagal menyimpan data : " . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RiwayatKeluargaAnak $riwayatKeluargaAnak)
    {
        // Pastikan pegawai role hanya bisa hapus data miliknya sendiri
        $user = Auth::user();
        if ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if (!$myPegawai || $riwayatKeluargaAnak->pegawai_id != $myPegawai->id) {
                abort(403, 'Akses ditolak');
            }
        }

        $riwayatKeluargaAnak->delete();

        return redirect('/riwayat_keluarga/anak')->with('success', 'Berhasil menghapus data!');
    }

    public function cariPegawaiAnak(Request $request)
    {
        $user = Auth::user();

        $query = RiwayatKeluargaAnak::with('pegawai');

        // Filter berdasarkan role
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

        if($request->cariPegawaiAnak) {
            $query->where(function($q) use ($request) {

                // dari tabel latihan jabatan
                $q->where('nama', 'like', '%' . $request->cariPegawaiAnak . '%');

                $q->orWhere('pendidikan', 'like', '%' . $request->cariPegawaiAnak . '%');

                $q->orWhere('pekerjaan', 'like', '%' . $request->cariPegawaiAnak . '%');

                 $q->orWhere('status_hubungan', 'like', '%' . $request->cariPegawaiAnak . '%')

                // dari relasi pegawai
                ->orWhereHas('pegawai', function($q2) use ($request) {
                    $q2->where('nama', 'like', '%' . $request->cariPegawaiAnak . '%');
                });

            });
        }

        $riwayatKeluargaAnak = $query->paginate(5);

        return view("pages.dashboard.riwayat_keluarga.anak.indexKeluargaAnak", [
            'riwayatKeluargaAnak' => $riwayatKeluargaAnak
        ]);

    }
}
