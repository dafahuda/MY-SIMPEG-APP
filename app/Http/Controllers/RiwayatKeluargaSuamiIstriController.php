<?php

namespace App\Http\Controllers;

use App\Models\RiwayatKeluargaSuamiIstri;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use Exception;
use App\Support\Access;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RiwayatKeluargaSuamiIstriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Access::isAdmin()) {
            $riwayatkeluargaSuamiIstri = Access::scopeToUnit(RiwayatKeluargaSuamiIstri::with('pegawai'))->paginate(simpeg_per_page());
        } elseif (Access::isPegawai()) {
            $riwayatkeluargaSuamiIstri = Access::scopeToSelf(RiwayatKeluargaSuamiIstri::with('pegawai'))->paginate(simpeg_per_page());
        } else {
            $riwayatkeluargaSuamiIstri = RiwayatKeluargaSuamiIstri::with('pegawai')->paginate(simpeg_per_page());
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
        if (Access::isAdmin()) {
            $pegawai = Pegawai::where('unit_kerja_id', Access::unitKerjaId())->get();
        } elseif (Access::isPegawai()) {
            $pegawai = Pegawai::where('user_id', Access::user()->id)->get();
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
        Access::ensureOwnRecord((object) ['pegawai_id' => $validateData['pegawai_id']]);

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
        if (Access::isAdmin()) {
            $pegawai = Pegawai::where('unit_kerja_id', Access::unitKerjaId())->get();
        } elseif (Access::isPegawai()) {
            Access::ensureOwnRecord($riwayatKeluargaSuamiIstri);
            $pegawai = collect([Access::myPegawai()]);
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
        Access::ensureOwnRecord($riwayatKeluargaSuamiIstri);

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
        Access::ensureOwnRecord($riwayatKeluargaSuamiIstri);

        $riwayatKeluargaSuamiIstri->delete();

        return redirect('/riwayat_keluarga/suami_istri')->with('success', 'Berhasil menghapus data');
    }

    public function cariPegawaiSuamiIstri(Request $request)
    {
        $query = RiwayatKeluargaSuamiIstri::with('pegawai');

        // Filter berdasarkan role admin
        if (Access::isAdmin()) {
            Access::scopeToUnit($query);
        } elseif (Access::isPegawai()) {
            Access::scopeToSelf($query);
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

        $riwayatkeluargaSuamiIstri = $query->paginate(simpeg_per_page());

        return view("pages.dashboard.riwayat_keluarga.suami_istri.indexKeluargaSuami_Istri", [
            'riwayatkeluargaSuamiIstri' => $riwayatkeluargaSuamiIstri
        ]);
    }
}
