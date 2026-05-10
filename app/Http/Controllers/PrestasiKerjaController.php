<?php

namespace App\Http\Controllers;

use App\Models\PrestasiKerja;
use App\Models\Pegawai;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrestasiKerjaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $prestasiKerja = PrestasiKerja::with('pegawai')
                ->whereHas('pegawai', function($query) use ($user) {
                    $query->where('unit_kerja_id', $user->unit_kerja_id);
                })
                ->paginate(5);
        } elseif ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            $prestasiKerja = $myPegawai
                ? PrestasiKerja::with('pegawai')->where('pegawai_id', $myPegawai->id)->paginate(5)
                : collect()->paginate(5);
        } else {
            $prestasiKerja = PrestasiKerja::with('pegawai')->paginate(5);
        }

        return view("pages.dashboard.skp_prestasi_kerja.indexPrestasiKerja", [
            'prestasiKerja' => $prestasiKerja
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

        return view("pages.dashboard.skp_prestasi_kerja.tambahPrestasiKerja", [
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
            'periode_nilai_dari' => 'required|date',
            'periode_nilai_sampai' => 'required|date',
            'tahun_periode' => 'required|string',
            'nama_pejabat_nilai' => 'required|string',
            'nama_atasan_pejabat_penilai' => 'required|string',
            'skp' => 'required|numeric',
            'orientasi_pelayanan' => 'required|numeric',
            'integritas' => 'required|numeric',
            'komitmen' => 'required|numeric',
            'disiplin' => 'required|numeric',
            'kerjasama' => 'required|numeric',
            'kepemimpinan' => 'required|numeric',
            'tgl_keberatan_pegawai' => 'required|date',
            'isi_keberatan' => 'required|string',
            'tgl_keputusan_atasan_pejabat_penilai' => 'required|date',
            'isi_keputusan' => 'required|string',
            'rekomendasi' => 'required|string',
            'tgl_diterima_pegawai' => 'required|date',
            'tgl_diterima_atasan' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $perilaku = [
                $request->orientasi_pekerjaan,
                $request->integritas,
                $request->komitmen,
                $request->disiplin,
                $request->kerjasama,
                $request->kepemimpinan
            ];

            // Filter nilai yang lebih dari 0 untuk pembagi rata-rata (menghindari division by zero)
            $rataPerilaku = array_sum($perilaku) / count($perilaku);

            // Hitung Total Nilai (Contoh Rumus Standar PNS: 60% SKP + 40% Perilaku)
            $totalNilai = ($request->skp * 0.6) + ($rataPerilaku * 0.4);

            // Simpan ke Database
            $validateData = $request->all();
            $validateData['total_nilai'] = $totalNilai;

            PrestasiKerja::create($validateData);

            DB::commit();

            return redirect("/skp_prestasi_kerja/data_prestasi_kerja")->with('success', 'Berhasil menambahkan data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal menambahkan data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PrestasiKerja $prestasiKerja)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $pegawai = Pegawai::where('unit_kerja_id', $user->unit_kerja_id)->get();
        } elseif ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if (!$myPegawai || $prestasiKerja->pegawai_id != $myPegawai->id) {
                abort(403, 'Akses ditolak');
            }
            $pegawai = collect([$myPegawai]);
        } else {
            $pegawai = Pegawai::all();
        }

        return view("pages.dashboard.skp_prestasi_kerja.editPrestasiKerja", [
            'pegawai' => $pegawai,
            'prestasiKerja' => $prestasiKerja
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PrestasiKerja $prestasiKerja)
    {
        $validateData = $request->validate([
            'pegawai_id' => 'required|exists:tb_pegawai,id',
            'periode_nilai_dari' => 'required|date',
            'periode_nilai_sampai' => 'required|date',
            'tahun_periode' => 'required|string',
            'nama_pejabat_nilai' => 'required|string',
            'nama_atasan_pejabat_penilai' => 'required|string',
            'skp' => 'required|numeric',
            'orientasi_pelayanan' => 'required|numeric',
            'integritas' => 'required|numeric',
            'komitmen' => 'required|numeric',
            'disiplin' => 'required|numeric',
            'kerjasama' => 'required|numeric',
            'kepemimpinan' => 'required|numeric',
            'tgl_keberatan_pegawai' => 'required|date',
            'isi_keberatan' => 'required|string',
            'tgl_keputusan_atasan_pejabat_penilai' => 'required|date',
            'isi_keputusan' => 'required|string',
            'rekomendasi' => 'required|string',
            'tgl_diterima_pegawai' => 'required|date',
            'tgl_diterima_atasan' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $perilaku = [
                $request->orientasi_pelayanan,
                $request->integritas,
                $request->komitmen,
                $request->disiplin,
                $request->kerjasama,
                $request->kepemimpinan
            ];

            // Filter nilai yang lebih dari 0 untuk pembagi rata-rata (menghindari division by zero)
            $rataPerilaku = array_sum($perilaku) / count($perilaku);

            // Hitung Total Nilai (Contoh Rumus Standar PNS: 60% SKP + 40% Perilaku)
            $totalNilai = ($request->skp * 0.6) + ($rataPerilaku * 0.4);

            // Simpan ke Database
            $validateData = $request->all();
            $validateData['total_nilai'] = $totalNilai;

            $prestasiKerja->update($validateData);

            DB::commit();

            return redirect('/skp_prestasi_kerja/data_prestasi_kerja')->with('success', 'Berhasil mengubah data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal mengubah data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PrestasiKerja $prestasiKerja)
    {
        // Pastikan pegawai role hanya bisa hapus data miliknya sendiri
        $user = Auth::user();
        if ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if (!$myPegawai || $prestasiKerja->pegawai_id != $myPegawai->id) {
                abort(403, 'Akses ditolak');
            }
        }

        $prestasiKerja->delete();

        return redirect('/skp_prestasi_kerja/data_prestasi_kerja')->with('success', 'Berhasil menghapus data!');
    }

    public function cariPrestasiKerja(Request $request)
    {
        $user = Auth::user();

        $query = PrestasiKerja::with('pegawai');

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

        if($request->cariPrestasiKerja) {
            $query->where(function($q) use ($request) {

                // dari tabel izin kawin
                $q->where('periode_nilai_dari', 'like', '%' . $request->cariPrestasiKerja . '%');

                $q->orWhere('periode_nilai_sampai', 'like', '%' . $request->cariPrestasiKerja . '%');

                $q->orWhere('tahun_periode', 'like', '%' . $request->cariPrestasiKerja . '%')

                // dari relasi pegawai
                ->orWhereHas('pegawai', function($q2) use ($request) {
                    $q2->where('nama', 'like', '%' . $request->cariPrestasiKerja . '%');
                });

            });
        }

        $prestasiKerja = $query->paginate(5);

        return view("pages.dashboard.skp_prestasi_kerja.indexPrestasiKerja", [
            'prestasiKerja' => $prestasiKerja
        ]);
    }
}
