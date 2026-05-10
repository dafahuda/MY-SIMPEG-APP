<?php

namespace App\Http\Controllers;

use App\Models\Tunjangan;
use App\Models\Pegawai;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TunjanganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $tunjangan = Tunjangan::with('pegawai')
                ->whereHas('pegawai', function($query) use ($user) {
                    $query->where('unit_kerja_id', $user->unit_kerja_id);
                })
                ->paginate(5);
        } else {
            $tunjangan = Tunjangan::with('pegawai')->paginate(5);
        }

        return view("pages.dashboard.kepegawaian.tunjangan.indexTunjangan", [
            'tunjangan' => $tunjangan
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

        return view("pages.dashboard.kepegawaian.tunjangan.tambahTunjangan", [
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
            'no_tunjangan' => 'required|string',
            'jenis_tunjangan_anak' => 'required|string',
            'tgl_tunjangan' => 'required|date',
            'terhitung_mulai' => 'required|date',
            'akta_perkawinan_dari' => 'required|string',
            'no_akta_perkawinan' => 'required|string',
            'tgl_akta_perkawinan' => 'required|date',
            'akta_kelahiran_dari' => 'required|string',
            'no_akta_kelahiran' => 'required|string',
            'tgl_akta_kelahiran' => 'required|date',
            'tebusan' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            Tunjangan::create($validateData);

            DB::commit();

            return redirect('/kepegawaian/tunjangan')->with('success', 'Berhasil menambahkan data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal menambahkan data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tunjangan $tunjangan)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $pegawai = Pegawai::where('unit_kerja_id', $user->unit_kerja_id)->get();
        } else {
            $pegawai = Pegawai::all();
        }

        return view("pages.dashboard.kepegawaian.tunjangan.editTunjangan", [
            'pegawai' => $pegawai,
            'tunjangan' => $tunjangan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tunjangan $tunjangan)
    {
        $validateData = $request->validate([
            'pegawai_id' => 'required|exists:tb_pegawai,id',
            'no_tunjangan' => 'required|string',
            'jenis_tunjangan_anak' => 'required|string',
            'tgl_tunjangan' => 'required|date',
            'terhitung_mulai' => 'required|date',
            'akta_perkawinan_dari' => 'required|string',
            'no_akta_perkawinan' => 'required|string',
            'tgl_akta_perkawinan' => 'required|date',
            'akta_kelahiran_dari' => 'required|string',
            'no_akta_kelahiran' => 'required|string',
            'tgl_akta_kelahiran' => 'required|date',
            'tebusan' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $tunjangan->update($validateData);

            DB::commit();

            return redirect('/kepegawaian/tunjangan')->with('success', 'Berhasil mengubah data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal mengubah data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tunjangan $tunjangan)
    {
        try {
            $tunjangan->delete();
            return redirect('/kepegawaian/tunjangan')->with('success', 'Berhasil menghapus data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal menghapus data : ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan pada sistem, silakan coba lagi.');
        }
    }

    public function cariTunjangan(Request $request)
    {
        $user = Auth::user();

        $query = Tunjangan::with('pegawai');

        // Filter berdasarkan role admin
        if ($user->role === 'admin') {
            $query->whereHas('pegawai', function($q) use ($user) {
                $q->where('unit_kerja_id', $user->unit_kerja_id);
            });
        }

        $query->where(function($q) use ($request) {

            // dari tabel tunjangan
            $q->where('jenis_tunjangan_anak', 'like', '%' . $request->cariTunjangan . '%')

            // dari relasi pegawai
            ->orWhereHas('pegawai', function($q2) use ($request) {
                $q2->where('nama', 'like', '%' . $request->cariTunjangan . '%');
            });

        });

        $tunjangan = $query->paginate(5);

        return view("pages.dashboard.kepegawaian.tunjangan.indexTunjangan", [
            'tunjangan' => $tunjangan
        ]);
    }
}
