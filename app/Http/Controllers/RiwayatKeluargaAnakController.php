<?php

namespace App\Http\Controllers;

use App\Models\RiwayatKeluargaAnak;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use Exception;
use App\Support\Access;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RiwayatKeluargaAnakController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        if (Access::isAdmin()) {
            $riwayatKeluargaAnak = Access::scopeToUnit(RiwayatKeluargaAnak::with('pegawai'))->paginate(simpeg_per_page());
        } elseif (Access::isPegawai()) {
            $riwayatKeluargaAnak = Access::scopeToSelf(RiwayatKeluargaAnak::with('pegawai'))->paginate(simpeg_per_page());
        } else {
            $riwayatKeluargaAnak = RiwayatKeluargaAnak::with('pegawai')->paginate(simpeg_per_page());
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

        if (Access::isAdmin()) {
            $pegawai = Pegawai::where('unit_kerja_id', Access::unitKerjaId())->get();
        } elseif (Access::isPegawai()) {
            $pegawai = Pegawai::where('user_id', Access::user()->id)->get();
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
        Access::ensureOwnRecord((object) ['pegawai_id' => $validateData['pegawai_id']]);

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

        if (Access::isAdmin()) {
            $pegawai = Pegawai::where('unit_kerja_id', Access::unitKerjaId())->get();
        } elseif (Access::isPegawai()) {
            Access::ensureOwnRecord($riwayatKeluargaAnak);
            $pegawai = collect([Access::myPegawai()]);
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
        Access::ensureOwnRecord($riwayatKeluargaAnak);

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
        Access::ensureOwnRecord($riwayatKeluargaAnak);

        $riwayatKeluargaAnak->delete();

        return redirect('/riwayat_keluarga/anak')->with('success', 'Berhasil menghapus data!');
    }

    public function cariPegawaiAnak(Request $request)
    {

        $query = RiwayatKeluargaAnak::with('pegawai');

        // Filter berdasarkan role
        if (Access::isAdmin()) {
            Access::scopeToUnit($query);
        } elseif (Access::isPegawai()) {
            Access::scopeToSelf($query);
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

        $riwayatKeluargaAnak = $query->paginate(simpeg_per_page());

        return view("pages.dashboard.riwayat_keluarga.anak.indexKeluargaAnak", [
            'riwayatKeluargaAnak' => $riwayatKeluargaAnak
        ]);

    }
}
