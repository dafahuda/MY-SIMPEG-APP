<?php

namespace App\Http\Controllers;

use App\Models\RiwayatKeluargaOrangtua;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use Exception;
use App\Support\Access;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RiwayatKeluargaOrangTuaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        if (Access::isAdmin()) {
            $riwayatKeluargaOrangTua = Access::scopeToUnit(RiwayatKeluargaOrangtua::with('pegawai'))->paginate(simpeg_per_page());
        } elseif (Access::isPegawai()) {
            $riwayatKeluargaOrangTua = Access::scopeToSelf(RiwayatKeluargaOrangtua::with('pegawai'))->paginate(simpeg_per_page());
        } else {
            $riwayatKeluargaOrangTua = RiwayatKeluargaOrangtua::with('pegawai')->paginate(simpeg_per_page());
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

        if (Access::isAdmin()) {
            $pegawai = Pegawai::where('unit_kerja_id', Access::unitKerjaId())->get();
        } elseif (Access::isPegawai()) {
            $pegawai = Pegawai::where('user_id', Access::user()->id)->get();
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
        Access::ensureOwnRecord((object) ['pegawai_id' => $validateData['pegawai_id']]);

        try {
            DB::beginTransaction();

            RiwayatKeluargaOrangtua::create($validateData);

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
    public function edit(RiwayatKeluargaOrangtua $riwayatKeluargaOrangTua)
    {

        if (Access::isAdmin()) {
            $pegawai = Pegawai::where('unit_kerja_id', Access::unitKerjaId())->get();
        } elseif (Access::isPegawai()) {
            Access::ensureOwnRecord($riwayatKeluargaOrangTua);
            $pegawai = collect([Access::myPegawai()]);
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
    public function update(Request $request, RiwayatKeluargaOrangtua $riwayatKeluargaOrangTua)
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
        Access::ensureOwnRecord($riwayatKeluargaOrangTua);

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
    public function destroy(RiwayatKeluargaOrangtua $riwayatKeluargaOrangTua)
    {
        // Pastikan pegawai role hanya bisa hapus data miliknya sendiri
        Access::ensureOwnRecord($riwayatKeluargaOrangTua);

        $riwayatKeluargaOrangTua->delete();

        return redirect('/riwayat_keluarga/orang_tua')->with('success', 'Berhasil menghapus data!');
    }

    public function cariPegawaiOrangTua(Request $request)
    {

        $query = RiwayatKeluargaOrangtua::with('pegawai');

        // Filter berdasarkan role admin
        if (Access::isAdmin()) {
            Access::scopeToUnit($query);
        } elseif (Access::isPegawai()) {
            Access::scopeToSelf($query);
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

        $riwayatKeluargaOrangTua = $query->paginate(simpeg_per_page());

        return view("pages.dashboard.riwayat_keluarga.orang_tua.IndexKeluargaOrangtua", [
            'riwayatKeluargaOrangTua' => $riwayatKeluargaOrangTua
        ]);
    }
}
