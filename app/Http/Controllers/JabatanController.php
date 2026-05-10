<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\MasterJabatan;
use App\Models\MasterEselon;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class JabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $jabatan = Jabatan::with(['master_jabatan', 'master_eselon', 'pegawai'])
                ->whereHas('pegawai', function($query) use ($user) {
                    $query->where('unit_kerja_id', $user->unit_kerja_id);
                })
                ->paginate(5);
        } elseif ($user->role === 'pegawai') {
            $pegawai = Pegawai::where('user_id', $user->id)->first();
            $jabatan = $pegawai
                ? Jabatan::with(['master_jabatan', 'master_eselon', 'pegawai'])
                    ->where('pegawai_id', $pegawai->id)
                    ->paginate(5)
                : collect()->paginate(5);
        } else {
            $jabatan = Jabatan::with(['master_jabatan', 'master_eselon'])->paginate(5);
        }

        return view("pages.dashboard.kepegawaian.jabatan.indexJabatan", [
            'jabatan' => $jabatan
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

        $masterJabatan = MasterJabatan::all();
        $masterEselon = MasterEselon::all();
        return view("pages.dashboard.kepegawaian.jabatan.tambahJabatan", [
            'pegawai' => $pegawai,
            'masterJabatan' => $masterJabatan,
            'masterEselon' => $masterEselon
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'pegawai_id' => 'required|exists:tb_pegawai,id',
            'master_jabatan_id' => 'required|exists:tb_master_jabatan,id',
            'master_eselon_id' => 'required|exists:tb_master_eselon,id',
            'jenis_jabatan' => 'required',
            'tmt_jabatan_mulai' => 'required|date',
            'tmt_jabatan_selesai' => 'required|date',
            'periode' => 'required',
            'tahun_ke' => 'required',
            'no_sk' => 'required|string',
            'tgl_sk' => 'required|date',
            'terbit' => 'required|string'
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

            Jabatan::create($validateData);

            DB::commit();

            return redirect('/kepegawaian/jabatan')->with('success', 'Berhasil menambahkan data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal menambahkan data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jabatan $jabatan)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $pegawai = Pegawai::where('unit_kerja_id', $user->unit_kerja_id)->get();
        } elseif ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if (!$myPegawai || $jabatan->pegawai_id != $myPegawai->id) {
                abort(403, 'Akses ditolak');
            }
            $pegawai = collect([$myPegawai]);
        } else {
            $pegawai = Pegawai::all();
        }

        $masterJabatan = MasterJabatan::all();
        $masterEselon = MasterEselon::all();

        return view("pages.dashboard.kepegawaian.jabatan.editJabatan", [
            'pegawai' => $pegawai,
            'masterJabatan' => $masterJabatan,
            'masterEselon' => $masterEselon,
            'jabatan' => $jabatan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jabatan $jabatan)
    {
        $validateData = $request->validate([
            'pegawai_id' => 'required|exists:tb_pegawai,id',
            'master_jabatan_id' => 'required|exists:tb_master_jabatan,id',
            'master_eselon_id' => 'required|exists:tb_master_eselon,id',
            'jenis_jabatan' => 'required',
            'tmt_jabatan_mulai' => 'required|date',
            'tmt_jabatan_selesai' => 'required|date',
            'periode' => 'required',
            'tahun_ke' => 'required',
            'no_sk' => 'required|string',
            'tgl_sk' => 'required|date',
            'terbit' => 'required|string'
        ]);

        // Pastikan pegawai role hanya bisa update data miliknya sendiri
        $user = Auth::user();
        if ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if (!$myPegawai || $jabatan->pegawai_id != $myPegawai->id) {
                abort(403, 'Akses ditolak');
            }
        }

        try {
            DB::beginTransaction();

            $jabatan->update($validateData);

            DB::commit();

            return redirect('/kepegawaian/jabatan')->with('success', 'Berhasil mengubah data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal mengubah data : ' . $e->getMessage());

            return back()->withInput()->with('error', "Error, terjadi kesalahan pada sistem!");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jabatan $jabatan)
    {
        // Pastikan pegawai role hanya bisa hapus data miliknya sendiri
        $user = Auth::user();
        if ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if (!$myPegawai || $jabatan->pegawai_id != $myPegawai->id) {
                abort(403, 'Akses ditolak');
            }
        }

        try {
            $jabatan->delete();

            return redirect('/kepegawaian/jabatan');
        } catch(Exception $e) {
            Log::error('Gagal menghapus data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    public function cariJabatan(Request $request)
    {
        $user = Auth::user();

        $query = Jabatan::with(['pegawai', 'master_jabatan', 'master_eselon']);

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

        if ($request->cariJabatan) {
            $query->where(function($q) use ($request) {

                // cari dari nama jabatan (master)
                $q->whereHas('master_jabatan', function($q2) use ($request) {
                    $q2->where('nama_jabatan', 'like', '%' . $request->cariJabatan . '%');
                });

                $q->orWhereHas('master_eselon', function($q2) use ($request) {
                    $q2->where('nama_eselon', 'like', '%' . $request->cariJabatan . '%');
                });

                // atau dari nama pegawai
                $q->orWhereHas('pegawai', function($q2) use ($request) {
                    $q2->where('nama', 'like', '%' . $request->cariJabatan . '%');
                });

            });
        }

        $jabatan = $query->paginate(5);

        return view("pages.dashboard.kepegawaian.jabatan.indexJabatan", [
            'jabatan' => $jabatan
        ]);
    }
}
