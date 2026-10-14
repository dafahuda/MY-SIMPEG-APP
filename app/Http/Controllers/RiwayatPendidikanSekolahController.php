<?php

namespace App\Http\Controllers;

use App\Models\RiwayatPendidikanSekolah;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Support\Access;

class RiwayatPendidikanSekolahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Access::isAdmin()) {
            $riwayanPendidikanSekolah = Access::scopeToUnit(RiwayatPendidikanSekolah::with('pegawai'))->paginate(simpeg_per_page());
        } elseif (Access::isPegawai()) {
            $riwayanPendidikanSekolah = Access::scopeToSelf(RiwayatPendidikanSekolah::with('pegawai'))->paginate(simpeg_per_page());
        } else {
            $riwayanPendidikanSekolah = RiwayatPendidikanSekolah::with('pegawai')->paginate(simpeg_per_page());
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
        if (Access::isAdmin()) {
            $pegawai = Pegawai::where('unit_kerja_id', Access::unitKerjaId())->get();
        } elseif (Access::isPegawai()) {
            $pegawai = Pegawai::where('user_id', Access::user()->id)->get();
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
        Access::ensureOwnRecord((object) ['pegawai_id' => $validateData['pegawai_id']]);

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
        if (Access::isAdmin()) {
            $pegawai = Pegawai::where('unit_kerja_id', Access::unitKerjaId())->get();
        } elseif (Access::isPegawai()) {
            Access::ensureOwnRecord($riwayatPendidikanSekolah);
            $pegawai = collect([Access::myPegawai()]);
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
        Access::ensureOwnRecord($riwayatPendidikanSekolah);

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
            Access::ensureOwnRecord($riwayatPendidikanSekolah);

            $riwayatPendidikanSekolah->delete();

            return redirect('/riwayat_pendidikan/sekolah')->with('success', 'Berhasil menghapus data!');
        } catch(Exception $e) {
            Log::error('Gagal menghapus data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    public function cariPendidikanSekolah(Request $request)
    {
        $query = RiwayatPendidikanSekolah::with('pegawai');

        // Filter berdasarkan role admin
        if (Access::isAdmin()) {
            Access::scopeToUnit($query);
        } elseif (Access::isPegawai()) {
            Access::scopeToSelf($query);
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

        $riwayanPendidikanSekolah = $query->paginate(simpeg_per_page());

        return view("pages.dashboard.riwayat_pendidikan.pendidikan_sekolah.indexPendidikanSekolah", [
            'riwayatPendidikanSekolah' => $riwayanPendidikanSekolah
        ]);
    }
}
