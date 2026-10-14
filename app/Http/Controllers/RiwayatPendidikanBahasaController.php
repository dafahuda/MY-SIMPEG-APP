<?php

namespace App\Http\Controllers;

use App\Models\RiwayatPendidikanBahasa;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Support\Access;

class RiwayatPendidikanBahasaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Access::isAdmin()) {
            $riwayatPendidikanBahasa = Access::scopeToUnit(RiwayatPendidikanBahasa::with('pegawai'))->paginate(simpeg_per_page());
        } elseif (Access::isPegawai()) {
            $riwayatPendidikanBahasa = Access::scopeToSelf(RiwayatPendidikanBahasa::with('pegawai'))->paginate(simpeg_per_page());
        } else {
            $riwayatPendidikanBahasa = RiwayatPendidikanBahasa::with('pegawai')->paginate(simpeg_per_page());
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
        if (Access::isAdmin()) {
            $pegawai = Pegawai::where('unit_kerja_id', Access::unitKerjaId())->get();
        } elseif (Access::isPegawai()) {
            $pegawai = Pegawai::where('user_id', Access::user()->id)->get();
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
        Access::ensureOwnRecord((object) ['pegawai_id' => $validateData['pegawai_id']]);

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
        if (Access::isAdmin()) {
            $pegawai = Pegawai::where('unit_kerja_id', Access::unitKerjaId())->get();
        } elseif (Access::isPegawai()) {
            Access::ensureOwnRecord($riwayatPendidikanBahasa);
            $pegawai = collect([Access::myPegawai()]);
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
        Access::ensureOwnRecord($riwayatPendidikanBahasa);

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
            Access::ensureOwnRecord($riwayatPendidikanBahasa);

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
        $query = RiwayatPendidikanBahasa::with('pegawai');

        // Filter berdasarkan role admin
        if (Access::isAdmin()) {
            Access::scopeToUnit($query);
        } elseif (Access::isPegawai()) {
            Access::scopeToSelf($query);
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

        $riwayatPendidikanBahasa = $query->paginate(simpeg_per_page());

        return view("pages.dashboard.riwayat_pendidikan.pendidikan_bahasa.indexPendidikanBahasa", [
            'riwayatPendidikanBahasa' => $riwayatPendidikanBahasa
        ]);
    }
}
