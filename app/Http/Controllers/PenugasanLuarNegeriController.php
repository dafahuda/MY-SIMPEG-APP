<?php

namespace App\Http\Controllers;

use App\Models\PenugasanLuarNegeri;
use Illuminate\Http\Request;
use Exception;
use App\Models\Pegawai;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class PenugasanLuarNegeriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $penugasaLuarNegeri = PenugasanLuarNegeri::with('pegawai')
                ->whereHas('pegawai', function($query) use ($user) {
                    $query->where('unit_kerja_id', $user->unit_kerja_id);
                })
                ->paginate(5);
        } else {
            $penugasaLuarNegeri = PenugasanLuarNegeri::with('pegawai')->paginate(5);
        }

        return view("pages.dashboard.kepegawaian.penugasanln.indexPenugasan_luar_negri", [
            'penugasanLuarNegeri' => $penugasaLuarNegeri
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

        return view("pages.dashboard.kepegawaian.penugasanln.tambahPenugasan_ln", [
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
            'alasan_penugasan' => 'required|string',
            'negara_tujuan' => 'required|string',
            'tahun' => 'required|string',
            'durasi_hari' => 'required|string',
            'no_st' => 'required|string',
            'tgl_st' => 'required|date',
            'st' => 'required|file|mimes:pdf,docx,txt|max:10240'
        ]);

        try {
            DB::beginTransaction();

            if($request->hasFile('st')) {
                $file = $request->file('st');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('document', $fileName, 'public');
                $validateData['st'] = '/storage/' . $path;
            }

            PenugasanLuarNegeri::create($validateData);


            DB::commit();

            return redirect("/kepegawaian/penugasan_ln")->with('success', 'Berhasil menambahkan data!');

        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal menambahkan data : ' . $e->getMessage());

            return back()->withInput()->with('error', "Error, terjadi kesalahan pada sistem!");
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PenugasanLuarNegeri $penugasanLuarNegeri)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $pegawai = Pegawai::where('unit_kerja_id', $user->unit_kerja_id)->get();
        } else {
            $pegawai = Pegawai::all();
        }

        return view("pages.dashboard.kepegawaian.penugasanln.editPenugasan", [
            'pegawai' => $pegawai,
            'penugasanLuarNegeri' => $penugasanLuarNegeri
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PenugasanLuarNegeri $penugasanLuarNegeri)
    {
        $validateData = $request->validate([
            'pegawai_id' => 'required|exists:tb_pegawai,id',
            'alasan_penugasan' => 'required|string',
            'negara_tujuan' => 'required|string',
            'tahun' => 'required|string',
            'durasi_hari' => 'required|string',
            'no_st' => 'required|string',
            'tgl_st' => 'required|date',
            'st' => 'file|mimes:pdf,docx,txt|max:10240'
        ]);

        try {
            DB::beginTransaction();

            if($request->hasFile('st')) {

                if($request->fileLama) {
                    Storage::disk('public')->delete($request->fileLama);
                }

                $file = $request->file('st');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('images', $fileName, 'public');
                $validateData['st'] = '/storage/' . $path;
            }

            $penugasanLuarNegeri->update($validateData);

            DB::commit();

            return redirect('/kepegawaian/penugasan_ln')->with('success', 'Berhasil mengubah data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal mengubah data : ' . $e->getMessage());

            return back()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PenugasanLuarNegeri $penugasanLuarNegeri)
    {
        $penugasanLuarNegeri->delete();

        return redirect('/kepegawaian/penugasan_ln')->with('success', 'Berhasil menghapus data!');
    }

    // download file
    public function download(PenugasanLuarNegeri $penugasanLuarNegeri)
    {
        $filePath = str_replace('/storage/', '', trim($penugasanLuarNegeri->st));

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download(storage_path('app/public/' . $filePath));
    }

    public function cariPenugasanLn(Request $request)
    {
        $user = Auth::user();

        $query = PenugasanLuarNegeri::with('pegawai');

        // Filter berdasarkan role admin
        if ($user->role === 'admin') {
            $query->whereHas('pegawai', function($q) use ($user) {
                $q->where('unit_kerja_id', $user->unit_kerja_id);
            });
        }

        if($request->cariPenugasanLn) {
            $query->where(function($q) use ($request) {

                // dari tabel penugasan luar negeri
                $q->where('negara_tujuan', 'like', '%' . $request->cariPenugasanLn . '%');

                $q->orWhere('tahun', 'like', '%' . $request->cariPenugasanLn . '%')

                // dari relasi pegawai
                ->orWhereHas('pegawai', function($q2) use ($request) {
                    $q2->where('nama', 'like', '%' . $request->cariPenugasanLn . '%');
                });

            });
        }

        $penugasanLuarNegeri = $query->paginate(5);

        return view("pages.dashboard.kepegawaian.penugasanln.indexPenugasan_luar_negri", [
            'penugasanLuarNegeri' => $penugasanLuarNegeri
        ]);
    }
}
