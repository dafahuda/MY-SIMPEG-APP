<?php

namespace App\Http\Controllers;

use App\Models\RiwayatPendidikanLanjut;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr;
use Illuminate\Support\Facades\Auth;

class RiwayatPendidikanLanjutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $riwayatPendidikanLanjut = RiwayatPendidikanLanjut::with('pegawai')
                ->whereHas('pegawai', function($query) use ($user) {
                    $query->where('unit_kerja_id', $user->unit_kerja_id);
                })
                ->paginate(5);
        } elseif ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            $riwayatPendidikanLanjut = $myPegawai
                ? RiwayatPendidikanLanjut::with('pegawai')->where('pegawai_id', $myPegawai->id)->paginate(5)
                : RiwayatPendidikanLanjut::whereRaw('1=0')->paginate(5);
        } else {
            $riwayatPendidikanLanjut = RiwayatPendidikanLanjut::with('pegawai')->paginate(5);
        }

        return view("pages.dashboard.riwayat_pendidikan.pendidikan_lanjut.indexPendidikanLanjut", [
            'riwayatPendidikanLanjut' => $riwayatPendidikanLanjut
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

        return view("pages.dashboard.riwayat_pendidikan.pendidikan_lanjut.tambahPendidikanLanjut", [
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
            'jenjang_pendidikan' => 'required',
            'nama_sekolah_universitas' => 'required',
            'jurusan' => 'required',
            'thn_mulai' => 'required',
            'thn_selesai' => 'required',
            'status' => 'required'
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

            RiwayatPendidikanLanjut::create($validateData);

            DB::commit();

            return redirect('/riwayat_pendidikan/sekolah_lanjut')->with('success', 'Berhasil menambahkan data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal menambahkan data : ' . $e->getMessage());

            return back()->withInput()->with('error', "Error, terjadi kesalahan!");
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RiwayatPendidikanLanjut $riwayatPendidikanLanjut)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $pegawai = Pegawai::where('unit_kerja_id', $user->unit_kerja_id)->get();
        } elseif ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if (!$myPegawai || $riwayatPendidikanLanjut->pegawai_id != $myPegawai->id) {
                abort(403, 'Akses ditolak');
            }
            $pegawai = collect([$myPegawai]);
        } else {
            $pegawai = Pegawai::all();
        }

        return view("pages.dashboard.riwayat_pendidikan.pendidikan_lanjut.editPendidikanLanjut", [
            'pegawai' => $pegawai,
            'riwayatPendidikanLanjut' => $riwayatPendidikanLanjut
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RiwayatPendidikanLanjut $riwayatPendidikanLanjut)
    {
        $validateData = $request->validate([
            'pegawai_id' => 'required|exists:tb_pegawai,id',
            'jenjang_pendidikan' => 'required',
            'jurusan' => 'required',
            'thn_mulai' => 'required',
            'thn_selesai' => 'required',
            'status' => 'required'
        ]);

        // Pastikan pegawai role hanya bisa update data miliknya sendiri
        $user = Auth::user();
        if ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if (!$myPegawai || $riwayatPendidikanLanjut->pegawai_id != $myPegawai->id) {
                abort(403, 'Akses ditolak');
            }
        }

        try {
            DB::beginTransaction();

            $riwayatPendidikanLanjut->update($validateData);

            DB::commit();

            return redirect('/riwayat_pendidikan/sekolah_lanjut')->with('success', 'Berhasil mengubah data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal mengubah data : ' . $e->getMessage());

            return back()->withInput()->with('error', "Error, terjadi kesalahan pada sistem!");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RiwayatPendidikanLanjut $riwayatPendidikanLanjut)
    {
        // Pastikan pegawai role hanya bisa hapus data miliknya sendiri
        $user = Auth::user();
        if ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if (!$myPegawai || $riwayatPendidikanLanjut->pegawai_id != $myPegawai->id) {
                abort(403, 'Akses ditolak');
            }
        }

        $riwayatPendidikanLanjut->delete();

        return redirect('/riwayat_pendidikan/pendidikan_lanjut')->with('success', 'Berhasil menghapus data!');
    }

    public function cariPendidikanLanjut(Request $request)
    {
        $user = Auth::user();

        $query = RiwayatPendidikanLanjut::with('pegawai');

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

        if($request->cariPendidikanLanjut) {
            $query->where(function($q) use ($request) {

                // dari tabel latihan jabatan
                $q->where('nama_sekolah_universitas', 'like', '%' . $request->cariPendidikanLanjut . '%');

                $q->orWhere('jenjang_pendidikan', 'like', '%' . $request->cariPendidikanLanjut . '%');

                $q->orWhere('thn_mulai', 'like', '%' . $request->cariPendidikanLanjut . '%');

                $q->orWhere('thn_selesai', 'like', '%' . $request->cariPendidikanLanjut . '%');

                $q->orWhere('status', 'like', '%' . $request->cariPendidikanLanjut . '%')

                // dari relasi pegawai
                ->orWhereHas('pegawai', function($q2) use ($request) {
                    $q2->where('nama', 'like', '%' . $request->cariPendidikanLanjut . '%');
                });

            });
        }

        $riwayatPendidikanLanjut = $query->paginate(5);

        return view("pages.dashboard.riwayat_pendidikan.pendidikan_lanjut.indexPendidikanLanjut", [
            'riwayatPendidikanLanjut' => $riwayatPendidikanLanjut
        ]);
    }
}
