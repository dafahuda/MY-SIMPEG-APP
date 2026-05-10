<?php

namespace App\Http\Controllers;

use App\Models\RiwayatPendidikanSekolah;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RiwayatPendidikanSekolahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $riwayanPendidikanSekolah = RiwayatPendidikanSekolah::with('pegawai')
                ->whereHas('pegawai', function($query) use ($user) {
                    $query->where('unit_kerja_id', $user->unit_kerja_id);
                })
                ->paginate(5);
        } elseif ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            $riwayanPendidikanSekolah = $myPegawai
                ? RiwayatPendidikanSekolah::with('pegawai')->where('pegawai_id', $myPegawai->id)->paginate(5)
                : RiwayatPendidikanSekolah::whereRaw('1=0')->paginate(5);
        } else {
            $riwayanPendidikanSekolah = RiwayatPendidikanSekolah::with('pegawai')->paginate(5);
        }

        return view("pages.dashboard.riwayat_pendidikan.pendidikan_sekolah.indexPendidikanSekolah", [
            'riwayatPendidikanSekolah' => $riwayanPendidikanSekolah
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

        return view("pages.dashboard.riwayat_pendidikan.pendidikan_sekolah.tambahPendidikanSekolah", [
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
            'nama_sekolah_universitas' => 'required|string',
            'lokasi' => 'required|string',
            'jurusan' => 'required|string',
            'no_ijazah' => 'required|string',
            'tgl_ijazah' => 'required|date',
            'nama_kepsek_rektor' => 'required|string'
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

            RiwayatPendidikanSekolah::create($validateData);

            DB::commit();

            return redirect('/riwayat_pendidikan/sekolah')->with('success', 'Berhasil menyimpan data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error("Gagal menyimpan data : " . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan');
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RiwayatPendidikanSekolah $riwayatPendidikanSekolah)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $pegawai = Pegawai::where('unit_kerja_id', $user->unit_kerja_id)->get();
        } elseif ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if (!$myPegawai || $riwayatPendidikanSekolah->pegawai_id != $myPegawai->id) {
                abort(403, 'Akses ditolak');
            }
            $pegawai = collect([$myPegawai]);
        } else {
            $pegawai = Pegawai::all();
        }

        return view("pages.dashboard.riwayat_pendidikan.pendidikan_sekolah.editPendidikanSekolah", [
            'pegawai' => $pegawai,
            'riwayatPendidikanSekolah' => $riwayatPendidikanSekolah
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RiwayatPendidikanSekolah $riwayatPendidikanSekolah)
    {
        $validateData = $request->validate([
            'pegawai_id' => 'required|exists:tb_pegawai,id',
            'jenjang_pendidikan' => 'required',
            'nama_sekolah_universitas' => 'required|string',
            'lokasi' => 'required|string',
            'jurusan' => 'required|string',
            'no_ijazah' => 'required|string',
            'tgl_ijazah' => 'required|date',
            'nama_kepsek_rektor' => 'required|string'
        ]);

        // Pastikan pegawai role hanya bisa update data miliknya sendiri
        $user = Auth::user();
        if ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if (!$myPegawai || $riwayatPendidikanSekolah->pegawai_id != $myPegawai->id) {
                abort(403, 'Akses ditolak');
            }
        }

        try {
            DB::beginTransaction();

            $riwayatPendidikanSekolah->update($validateData);

            DB::commit();

            return redirect('/riwayat_pendidikan/sekolah')->with('success', 'Berhasil menambahkan data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal mengubah data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RiwayatPendidikanSekolah $riwayatPendidikanSekolah)
    {
        try {
            // Pastikan pegawai role hanya bisa hapus data miliknya sendiri
            $user = Auth::user();
            if ($user->role === 'pegawai') {
                $myPegawai = Pegawai::where('user_id', $user->id)->first();
                if (!$myPegawai || $riwayatPendidikanSekolah->pegawai_id != $myPegawai->id) {
                    abort(403, 'Akses ditolak');
                }
            }

            $riwayatPendidikanSekolah->delete();

            return redirect('/riwayat_pendidikan/sekolah')->with('success', 'Berhasil menghapus data!');
        } catch(Exception $e) {
            Log::error('Gagal menghapus data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    public function cariPendidikanSekolah(Request $request)
    {
        $user = Auth::user();

        $query = RiwayatPendidikanSekolah::with('pegawai');

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

        if($request->cariPendidikanSekolah) {
            $query->where(function($q) use ($request) {

                // dari tabel latihan jabatan
                $q->where('nama_sekolah_universitas', 'like', '%' . $request->cariPendidikanSekolah . '%');

                $q->orWhere('jenjang_pendidikan', 'like', '%' . $request->cariPendidikanSekolah . '%');

                $q->orWhere('lokasi', 'like', '%' . $request->cariPendidikanSekolah . '%')

                // dari relasi pegawai
                ->orWhereHas('pegawai', function($q2) use ($request) {
                    $q2->where('nama', 'like', '%' . $request->cariPendidikanSekolah . '%');
                });

            });
        }

        $riwayanPendidikanSekolah = $query->paginate(5);

        return view("pages.dashboard.riwayat_pendidikan.pendidikan_sekolah.indexPendidikanSekolah", [
            'riwayatPendidikanSekolah' => $riwayanPendidikanSekolah
        ]);
    }
}
