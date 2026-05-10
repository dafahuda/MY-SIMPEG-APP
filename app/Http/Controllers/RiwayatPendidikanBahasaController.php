<?php

namespace App\Http\Controllers;

use App\Models\RiwayatPendidikanBahasa;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RiwayatPendidikanBahasaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $riwayatPendidikanBahasa = RiwayatPendidikanBahasa::with('pegawai')
                ->whereHas('pegawai', function($query) use ($user) {
                    $query->where('unit_kerja_id', $user->unit_kerja_id);
                })
                ->paginate(5);
        } elseif ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            $riwayatPendidikanBahasa = $myPegawai
                ? RiwayatPendidikanBahasa::with('pegawai')->where('pegawai_id', $myPegawai->id)->paginate(5)
                : RiwayatPendidikanBahasa::whereRaw('1=0')->paginate(5);
        } else {
            $riwayatPendidikanBahasa = RiwayatPendidikanBahasa::with('pegawai')->paginate(5);
        }

        return view("pages.dashboard.riwayat_pendidikan.pendidikan_bahasa.indexPendidikanBahasa", [
            'riwayatPendidikanBahasa' => $riwayatPendidikanBahasa
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

        return view("pages.dashboard.riwayat_pendidikan.pendidikan_bahasa.tambahPendidikanBahasa", [
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
            'jenis_bahasa' => 'required|string',
            'bahasa' => 'required|string',
            'kemampuan_bicara' => 'required'
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

            RiwayatPendidikanBahasa::create($validateData);

            DB::commit();

            return redirect("/riwayat_pendidikan/pendidikan_bahasa")->with('success', 'Berhasil menambahkan data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal menambahkan data : ' . $e->getMessage());

            return back()->withInput()->with('error', "Error terjadi kesalahan pada sistem!");
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RiwayatPendidikanBahasa $riwayatPendidikanBahasa)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $pegawai = Pegawai::where('unit_kerja_id', $user->unit_kerja_id)->get();
        } elseif ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if (!$myPegawai || $riwayatPendidikanBahasa->pegawai_id != $myPegawai->id) {
                abort(403, 'Akses ditolak');
            }
            $pegawai = collect([$myPegawai]);
        } else {
            $pegawai = Pegawai::all();
        }

        return view("pages.dashboard.riwayat_pendidikan.pendidikan_bahasa.editPendidikanBahasa", [
            'pegawai' => $pegawai,
            'riwayatPendidikanBahasa' => $riwayatPendidikanBahasa
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RiwayatPendidikanBahasa $riwayatPendidikanBahasa)
    {
        $validateData = $request->validate([
            'pegawai_id' => 'required|exists:tb_pegawai,id',
            'jenis_bahasa' => 'required|string',
            'bahasa' => 'required|string',
            'kemampuan_bicara' => 'required'
        ]);

        // Pastikan pegawai role hanya bisa update data miliknya sendiri
        $user = Auth::user();
        if ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if (!$myPegawai || $riwayatPendidikanBahasa->pegawai_id != $myPegawai->id) {
                abort(403, 'Akses ditolak');
            }
        }

        try {
            DB::beginTransaction();

            $riwayatPendidikanBahasa->update($validateData);

            DB::commit();

            return redirect('/riwayat_pendidikan/pendidikan_bahasa')->with('success', 'Berhasil mengubah data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal mengubah data : ' . $e->getMessage());

            return back()->withInput()->with('error', "Error, terjadi kesalahan pada sistem!");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RiwayatPendidikanBahasa $riwayatPendidikanBahasa)
    {
        try {
            // Pastikan pegawai role hanya bisa hapus data miliknya sendiri
            $user = Auth::user();
            if ($user->role === 'pegawai') {
                $myPegawai = Pegawai::where('user_id', $user->id)->first();
                if (!$myPegawai || $riwayatPendidikanBahasa->pegawai_id != $myPegawai->id) {
                    abort(403, 'Akses ditolak');
                }
            }

            $riwayatPendidikanBahasa->delete();

            return redirect('/riwayat_pendidikan/pendidikan_bahasa')->with('success', 'Berhasil menghapus data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal menghapus data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    public function cariPendidikanBahasa(Request $request)
    {
        $user = Auth::user();

        $query = RiwayatPendidikanBahasa::with('pegawai');

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

        if($request->cariPendidikanBahasa) {
            $query->where(function($q) use ($request) {

                // dari tabel latihan jabatan
                $q->where('jenis_bahasa', 'like', '%' . $request->cariPendidikanBahasa . '%');

                $q->orWhere('kemampuan_bicara', 'like', '%' . $request->cariPendidikanBahasa . '%');

                $q->orWhere('bahasa', 'like', '%' . $request->cariPendidikanBahasa . '%')

                // dari relasi pegawai
                ->orWhereHas('pegawai', function($q2) use ($request) {
                    $q2->where('nama', 'like', '%' . $request->cariPendidikanBahasa . '%');
                });

            });
        }

        $riwayatPendidikanBahasa = $query->paginate(5);

        return view("pages.dashboard.riwayat_pendidikan.pendidikan_bahasa.indexPendidikanBahasa", [
            'riwayatPendidikanBahasa' => $riwayatPendidikanBahasa
        ]);
    }
}
