<?php

namespace App\Http\Controllers;

use App\Models\Penghargaan;
use App\Models\Pegawai;
use App\Models\InstansiLembaga;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PenghargaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $penghargaan = Penghargaan::with('pegawai')
                ->whereHas('pegawai', function($query) use ($user) {
                    $query->where('unit_kerja_id', $user->unit_kerja_id);
                })
                ->paginate(5);
        } else {
            $penghargaan = Penghargaan::with('pegawai')->paginate(5);
        }

        return view("pages.dashboard.kepegawaian.penghargaan.indexPenghargaan", [
            'penghargaan' => $penghargaan
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

        return view("pages.dashboard.kepegawaian.penghargaan.tambahPenghargaan", [
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
            'nama_penghargaan' => 'required|string',
            'instansi_pemberi' => 'required|string',
            'tingkat_kegiatan' => 'required',
            'tempat_penghargaan' => 'required',
            'tgl_penghargaan' => 'required|date',
            'tahun' => 'required|string',
            'no_sertifikat' => 'required',
            'file_sertifikat_penghargaan' => 'required|file|mimes:pdf,docx,txt|max:10240'
        ]);

        try {
            DB::beginTransaction();

            if($request->hasFile('file_sertifikat_penghargaan')) {
                $file = $request->file('file_sertifikat_penghargaan');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('document', $fileName, 'public');
                $validateData['file_sertifikat_penghargaan'] = '/storage/' . $path;
            }

            Penghargaan::create($validateData);

            DB::commit();

            return redirect("/kepegawaian/penghargaan")->with('success', 'Berhasil menambahkan data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error("Gagal menyimpan data : " . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan');
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penghargaan $penghargaan)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $pegawai = Pegawai::where('unit_kerja_id', $user->unit_kerja_id)->get();
        } else {
            $pegawai = Pegawai::all();
        }

        return view("pages.dashboard.kepegawaian.penghargaan.editPenghargaan", [
            'penghargaan' => $penghargaan,
            'pegawai' => $pegawai
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Penghargaan $penghargaan)
    {
        $validateData = $request->validate([
            'pegawai_id' => 'required|exists:tb_pegawai,id',
            'nama_penghargaan' => 'required|string',
            'instansi_pemberi' => 'required|string',
            'tingkat_kegiatan' => 'required',
            'tempat_penghargaan' => 'required',
            'tgl_penghargaan' => 'required|date',
            'tahun' => 'required|string',
            'no_sertifikat' => 'required',
            'file_sertifikat_penghargaan' => 'nullable|mimes:pdf,docx,txt|max:10240'
        ]);

        try {
            DB::beginTransaction();

            if($request->hasFile('file_sertifikat_penghargaan')) {

                if($penghargaan->file_sertifikat_penghargaan) {
                    $oldPath = str_replace('/storage/', '', $penghargaan->file_sertifikat_penghargaan);
                    Storage::disk('public')->delete($oldPath);
                }

                $file = $request->file('file_sertifikat_penghargaan');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('document', $fileName, 'public');
                $validateData['file_sertifikat_penghargaan'] = '/storage/' . $path;
            }

            $penghargaan->update($validateData);

            DB::commit();

            return redirect("/kepegawaian/penghargaan")->with('success', 'Berhasil mengubah data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error("Gagal mengubah data : " . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Penghargaan $penghargaan)
    {
        $penghargaan->delete();

        return redirect("/kepegawaian/penghargaan")->with('success', 'Berhasil menghapus data!');
    }

    /**
     * Download / cetak sertifikat penghargaan.
     */
    public function downloadSertifikat(Penghargaan $penghargaan)
    {
        $filePath = str_replace('/storage/', '', trim($penghargaan->file_sertifikat_penghargaan));

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download(storage_path('app/public/' . $filePath));
    }

    public function cariPenghargaan(Request $request)
    {
        $user = Auth::user();

        $query = Penghargaan::with('pegawai');

        // Filter berdasarkan role admin
        if ($user->role === 'admin') {
            $query->whereHas('pegawai', function($q) use ($user) {
                $q->where('unit_kerja_id', $user->unit_kerja_id);
            });
        }

        if($request->cariPenghargaan) {
            $query->where(function($q) use ($request) {

                // dari tabel izin kawin
                $q->where('nama_penghargaan', 'like', '%' . $request->cariPenghargaan . '%');

                $q->orWhere('tingkat_kegiatan', 'like', '%' . $request->cariPenghargaan . '%');

                $q->orWhere('tahun', 'like', '%' . $request->cariPenghargaan . '%')

                // dari relasi pegawai
                ->orWhereHas('pegawai', function($q2) use ($request) {
                    $q2->where('nama', 'like', '%' . $request->cariPenghargaan . '%');
                });

            });
        }

        $penghargaan = $query->paginate(5);

        return view("pages.dashboard.kepegawaian.penghargaan.indexPenghargaan", [
            'penghargaan' => $penghargaan
        ]);
    }
}
