<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\Pegawai;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class CutiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $cuti = Cuti::with('pegawai')
                ->whereHas('pegawai', function($query) use ($user) {
                    $query->where('unit_kerja_id', $user->unit_kerja_id);
                })
                ->paginate(5);
        } else {
            $cuti = Cuti::with('pegawai')->paginate(5);
        }

        return view("pages.dashboard.kepegawaian.cuti.indexCuti", [
            'cuti' => $cuti
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

        return view("pages.dashboard.kepegawaian.cuti.tambahCuti", [
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
            'jenis_cuti' => 'required',
            'no_surat_cuti' => 'required|string',
            'tgl_surat_cuti' => 'required|date',
            'pelaksanaan_cuti_mulai' => 'required|date',
            'pelaksanaan_cuti_selesai' => 'required|date',
            'durasi_cuti' => 'required|string',
            'ketentuan_a' => 'required|string',
            'ketentuan_b' => 'required|string',
            'ketentuan_c' => 'required|string',
            'file_surat_cuti' => 'required|file|mimes:pdf,docx,txt|max:10240',
            'tebusan' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            if($request->hasFile('file_surat_cuti')) {
                $file = $request->file('file_surat_cuti');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('document', $fileName, 'public');
                $validateData['file_surat_cuti'] = '/storage/' . $path;
            }

            Cuti::create($validateData);

            DB::commit();

            return redirect('/kepegawaian/cuti')->with('success', 'Berhasil menambahkan data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal menambahkan data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cuti $cuti)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $pegawai = Pegawai::where('unit_kerja_id', $user->unit_kerja_id)->get();
        } else {
            $pegawai = Pegawai::all();
        }

        return view("pages.dashboard.kepegawaian.cuti.editCuti", [
            'pegawai' => $pegawai,
            'cuti' => $cuti
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cuti $cuti)
    {
        $validateData = $request->validate([
            'pegawai_id' => 'required|exists:tb_pegawai,id',
            'jenis_cuti' => 'required',
            'no_surat_cuti' => 'required|string',
            'tgl_surat_cuti' => 'required|date',
            'pelaksanaan_cuti_mulai' => 'required|date',
            'pelaksanaan_cuti_selesai' => 'required|date',
            'durasi_cuti' => 'required|string',
            'ketentuan_a' => 'required|string',
            'ketentuan_b' => 'required|string',
            'ketentuan_c' => 'required|string',
            'file_surat_cuti' => 'nullable|file|mimes:pdf,docx,txt|max:10240',
            'tebusan' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            if($request->hasFile('file_surat_cuti')) {

                if($cuti->file_surat_cuti) {
                    $oldPath = str_replace('/storage/', '', $cuti->file_surat_cuti);
                    Storage::disk('public')->delete($oldPath);
                }

                $file = $request->file('file_surat_cuti');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('document', $fileName, 'public');
                $validateData['file_surat_cuti'] = '/storage/' . $path;
            } else {
                unset($validateData['file_surat_cuti']);
            }

            $cuti->update($validateData);

            DB::commit();

            return redirect("/kepegawaian/cuti")->with('success', 'Berhasil mengubah data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal mengubah data : ' . $e->getMessage());

            return back()->withInput()->with('error', "Error, terjadi kesalahan pada sistem!");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cuti $cuti)
    {
        $cuti->delete();

        return redirect('/kepegawaian/cuti')->with('success', 'Berhasil menghapus data!');
    }

    /**
     * Download / cetak form surat cuti.
     */
    public function downloadSuratCuti(Cuti $cuti)
    {
        $filePath = str_replace('/storage/', '', trim($cuti->file_surat_cuti));

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download(storage_path('app/public/' . $filePath));
    }

    public function cariCuti(Request $request)
    {
        $user = Auth::user();

        $query = Cuti::with('pegawai');

        // Filter berdasarkan role admin
        if ($user->role === 'admin') {
            $query->whereHas('pegawai', function($q) use ($user) {
                $q->where('unit_kerja_id', $user->unit_kerja_id);
            });
        }

        if($request->cariCuti) {
            $query->where('jenis_cuti', 'like', '%' . $request->cariCuti . '%')
                  ->orWhere('durasi_cuti', 'like', '%' . $request->cariCuti . '%');
        }

        $cuti = $query->paginate(5);

        return view("pages.dashboard.kepegawaian.cuti.indexCuti", [
            'cuti' => $cuti
        ]);
    }
}
