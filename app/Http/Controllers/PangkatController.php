<?php

namespace App\Http\Controllers;

use App\Models\Pangkat;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Pegawai;
use App\Models\MasterPangkat;
use App\Models\MasterGolongan;
use App\Support\Access;

class PangkatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Access::isAdmin()) {
            $pangkat = Access::scopeToUnit(Pangkat::with(['master_pangkat', 'master_golongan', 'pegawai']))->paginate(simpeg_per_page());
        } elseif (Access::isPegawai()) {
            $pangkat = Access::scopeToSelf(Pangkat::with(['master_pangkat', 'master_golongan', 'pegawai']))->paginate(simpeg_per_page());
        } else {
            $pangkat = Pangkat::with(['master_pangkat', 'master_golongan'])->paginate(simpeg_per_page());
        }

        return view("pages.dashboard.kepegawaian.pangkat.indexPangkat", [
            'pangkat' => $pangkat
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Access::isAdmin()) {
            $pegawai = Pegawai::where('unit_kerja_id', Access::unitKerjaId())->get();
        } elseif (Access::isPegawai()) {
            $pegawai = Pegawai::where('user_id', Access::user()->id)->get();
        } else {
            $pegawai = Pegawai::all();
        }

        $masterPangkat = MasterPangkat::all();
        $masterGolongan = MasterGolongan::all();

        return view("pages.dashboard.kepegawaian.pangkat.tambahPangkat", [
            'pegawai' => $pegawai,
            'masterPangkat' => $masterPangkat,
            'masterGolongan' => $masterGolongan
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'pegawai_id' => 'required|exists:tb_pegawai,id',
            'master_pangkat_id' => 'required|exists:tb_master_pangkat,id',
            'master_golongan_id' => 'required|exists:tb_master_golongan,id',
            'jenis_pangkat' => 'required|string',
            'tmt_pangkat_mulai' => 'required|date',
            'tmt_pangkat_selesai' => 'required|date',
            'no_sk' => 'required|string',
            'tgl_sk' => 'required|date',
            'pejabat_pengesah_sk' => 'required|string'
        ]);

        // Pastikan pegawai role hanya bisa simpan data miliknya sendiri
        Access::ensureOwnRecord((object) ['pegawai_id' => $validateData['pegawai_id']]);

        try {
            DB::beginTransaction();

            Pangkat::create($validateData);

            DB::commit();

            return redirect('/kepegawaian/pangkat')->with('success', 'Berhasil menambahkan data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal menambahkan data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pangkat $pangkat)
    {
        if (Access::isAdmin()) {
            $pegawai = Pegawai::where('unit_kerja_id', Access::unitKerjaId())->get();
        } elseif (Access::isPegawai()) {
            Access::ensureOwnRecord($pangkat);
            $pegawai = collect([Access::myPegawai()]);
        } else {
            $pegawai = Pegawai::all();
        }

        $masterPangkat = MasterPangkat::all();
        $masterGolongan = MasterGolongan::all();

        return view("pages.dashboard.kepegawaian.pangkat.editPangkat", [
            'pegawai' => $pegawai,
            'masterPangkat' => $masterPangkat,
            'masterGolongan' => $masterGolongan,
            'pangkat' => $pangkat
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pangkat $pangkat)
    {
        $validateData = $request->validate([
            'pegawai_id' => 'required|exists:tb_pegawai,id',
            'master_pangkat_id' => 'required|exists:tb_master_pangkat,id',
            'master_golongan_id' => 'required|exists:tb_master_golongan,id',
            'jenis_pangkat' => 'required|string',
            'tmt_pangkat_mulai' => 'required|date',
            'tmt_pangkat_selesai' => 'required|date',
            'no_sk' => 'required|string',
            'tgl_sk' => 'required|date',
            'pejabat_pengesah_sk' => 'required|string'
        ]);

        // Pastikan pegawai role hanya bisa update data miliknya sendiri
        Access::ensureOwnRecord($pangkat);

        try {
            DB::beginTransaction();

            $pangkat->update($validateData);

            DB::commit();

            return redirect("/kepegawaian/pangkat")->with('success', 'Berhasil mengubah data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal mengubah data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pangkat $pangkat)
    {
        // Pastikan pegawai role hanya bisa hapus data miliknya sendiri
        Access::ensureOwnRecord($pangkat);

        try {
            $pangkat->delete();

            return redirect('/kepegawaian/pangkat')->with('success', 'Berhasil menghapus data!');
        } catch(Exception $e) {
            Log::error('Gagal menghapus data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    public function cariPangkat(Request $request)
    {
        $query = Pangkat::with(['master_pangkat', 'master_golongan', 'pegawai']);

        // Filter berdasarkan role
        if (Access::isAdmin()) {
            Access::scopeToUnit($query);
        } elseif (Access::isPegawai()) {
            Access::scopeToSelf($query);
        }

        if ($request->cariPangkat) {
            $query->where(function($q) use ($request) {

                // cari dari nama jabatan (master)
                $q->whereHas('master_pangkat', function($q2) use ($request) {
                    $q2->where('nama_pangkat', 'like', '%' . $request->cariPangkat . '%');
                });

                $q->orWhereHas('master_golongan', function($q2) use ($request) {
                    $q2->where('nama_golongan', 'like', '%' . $request->cariPangkat . '%');
                });

                // atau dari nama pegawai
                $q->orWhereHas('pegawai', function($q2) use ($request) {
                    $q2->where('nama', 'like', '%' . $request->cariPangkat . '%');
                });

            });
        }

        $pangkat = $query->paginate(simpeg_per_page());

        return view('pages.dashboard.kepegawaian.pangkat.indexPangkat', [
            'pangkat' => $pangkat
        ]);
    }
}
