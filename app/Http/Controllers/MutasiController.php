<?php

namespace App\Http\Controllers;

use App\Models\Mutasi;
use App\Models\Pegawai;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MutasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $mutasi = Mutasi::with('pegawai')
                ->whereHas('pegawai', function($query) use ($user) {
                    $query->where('unit_kerja_id', $user->unit_kerja_id);
                })
                ->paginate(5);
        } else {
            $mutasi = Mutasi::with('pegawai')->paginate(5);
        }

        return view("pages.dashboard.kepegawaian.mutasi.indexMutasi", [
            'mutasi' => $mutasi
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

        return view("pages.dashboard.kepegawaian.mutasi.tambahMutasi", [
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
            'jenis_mutasi' => 'required',
            'instansi_tujuan' => 'required|string',
            'no_sk_mutasi' => 'required|string',
            'tgl_sk_mutasi' => 'required|date',
            'file_sk_mutasi' => 'required|file|mimes:pdf,docx,txt|max:10240'
        ]);

        try {
            DB::beginTransaction();

            if($request->hasFile('file_sk_mutasi')) {
                $file = $request->file('file_sk_mutasi');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('document', $fileName, 'public');
                $validateData['file_sk_mutasi'] = '/storage/' . $path;
            }

            Mutasi::create($validateData);

            DB::commit();

            return redirect('/kepegawaian/mutasi')->with('success', 'Berhasil menambahkan data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal menambahkan data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mutasi $mutasi)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $pegawai = Pegawai::where('unit_kerja_id', $user->unit_kerja_id)->get();
        } else {
            $pegawai = Pegawai::all();
        }

        return view("pages.dashboard.kepegawaian.mutasi.editMutasi", [
            'pegawai' => $pegawai,
            'mutasi' => $mutasi
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mutasi $mutasi)
    {
        $validateData = $request->validate([
            'pegawai_id' => 'required|exists:tb_pegawai,id',
            'jenis_mutasi' => 'required',
            'instansi_tujuan' => 'required|string',
            'no_sk_mutasi' => 'required|string',
            'tgl_sk_mutasi' => 'required|date',
            'file_sk_mutasi' => 'file|mimes:pdf,docx,txt|max:10240'
        ]);

        try {
            DB::beginTransaction();

            $mutasi->update($validateData);

            if ($request->hasFile('file_sk_mutasi')) {

                if ($request->fileLama) {
                    Storage::disk('public')->delete(
                        str_replace('/storage/', '', $request->fileLama)
                    );
                }

                $file = $request->file('file_sk_mutasi');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('document', $fileName, 'public');

                $validateData['file_sk_mutasi'] = '/storage/' . $path;
            }

            $mutasi->update($validateData);

            DB::commit();

            return redirect('/kepegawaian/mutasi')->with('success', 'Berhasil menambahkan data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal mengubah data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mutasi $mutasi)
    {
        $mutasi->delete();

        return redirect("/kepegawaian/mutasi")->with('success', 'Berhasil menghapus data!');
    }

    public function downloadSkMutasi(Mutasi $mutasi)
    {
        $filePath = str_replace('/storage/', '', trim($mutasi->file_sk_mutasi));

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download(storage_path('app/public/' . $filePath));
    }

    public function cariMutasi(Request $request)
    {
        $user = Auth::user();

        $query = Mutasi::with('pegawai');

        // Filter berdasarkan role admin
        if ($user->role === 'admin') {
            $query->whereHas('pegawai', function($q) use ($user) {
                $q->where('unit_kerja_id', $user->unit_kerja_id);
            });
        }

        if($request->cariMutasi) {
            $query->where(function($q) use ($request) {

                // dari tabel latihan jabatan
                $q->where('jenis_mutasi', 'like', '%' . $request->cariMutasi . '%');

                $q->orWhere('instansi_tujuan', 'like', '%' . $request->cariMutasi . '%')

                // dari relasi pegawai
                ->orWhereHas('pegawai', function($q2) use ($request) {
                    $q2->where('nama', 'like', '%' . $request->cariMutasi . '%');
                });

            });
        }

        $mutasi = $query->paginate(5);

        return view("pages.dashboard.kepegawaian.mutasi.indexMutasi", [
            'mutasi' => $mutasi
        ]);
    }
}
