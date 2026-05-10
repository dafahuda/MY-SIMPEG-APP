<?php

namespace App\Http\Controllers;

use App\Models\Hukuman;
use App\Models\Pegawai;
use App\Models\InstansiLembaga;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HukumanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $hukuman = Hukuman::with('pegawai')
                ->whereHas('pegawai', function($query) use ($user) {
                    $query->where('unit_kerja_id', $user->unit_kerja_id);
                })
                ->paginate(5);
        } else {
            $hukuman = Hukuman::with('pegawai')->paginate(5);
        }

        return view("pages.dashboard.kepegawaian.hukuman.indexHukuman", [
            'hukuman' => $hukuman
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

        return view("pages.dashboard.kepegawaian.hukuman.tambahHukuman", [
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
            'pelanggaran_yg_dilakukan' => 'required|string',
            'tingkat_hukuman' => 'required',
            'jenis_hukuman' => 'required',
            'isi_teguran' => 'required|string',
            'pejabat_pengesahan_sk_hukuman' => 'required|string',
            'no_sk' => 'required|string',
            'file_sk_hukuman' => 'required|file|mimes:pdf,docx,txt|max:10240',
            'tgl_pengesahan_sk' => 'required|date',
            'tmt_hukuman_mulai' => 'required|date',
            'tmt_hukuman_pemulihan' => 'required|date',
            'pejabat_pemulihan_hukuman' => 'required|string',
            'no_pemulihan_hukuman' => 'required|string',
            'tgl_pemulihan_hukuman' => 'required|date'
        ]);

        try {
            DB::beginTransaction();

            if($request->hasFile('file_sk_hukuman')) {
                $file = $request->file('file_sk_hukuman');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('document', $fileName, 'public');
                $validateData['file_sk_hukuman'] = '/storage/' . $path;
            }

            Hukuman::create($validateData);

            DB::commit();

            return redirect('/kepegawaian/hukuman')->with('success', 'Berhasil menambahkan data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal menambahkan data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hukuman $hukuman)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $pegawai = Pegawai::where('unit_kerja_id', $user->unit_kerja_id)->get();
        } else {
            $pegawai = Pegawai::all();
        }

        return view("pages.dashboard.kepegawaian.hukuman.editHukuman", [
            'pegawai' => $pegawai,
            'hukuman' => $hukuman
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hukuman $hukuman)
    {
        $validateData = $request->validate([
            'pegawai_id' => 'required|exists:tb_pegawai,id',
            'pelanggaran_yg_dilakukan' => 'required|string',
            'tingkat_hukuman' => 'required',
            'jenis_hukuman' => 'required',
            'isi_teguran' => 'required|string',
            'pejabat_pengesahan_sk_hukuman' => 'required|string',
            'no_sk' => 'required|string',
            'file_sk_hukuman' => 'nullable|file|mimes:pdf,docx,txt|max:10240',
            'tgl_pengesahan_sk' => 'required|date',
            'tmt_hukuman_mulai' => 'required|date',
            'tmt_hukuman_pemulihan' => 'required|date',
            'pejabat_pemulihan_hukuman' => 'required|string',
            'no_pemulihan_hukuman' => 'required|string',
            'tgl_pemulihan_hukuman' => 'required|date'
        ]);

        try {
            DB::beginTransaction();

            if($request->hasFile('file_sk_hukuman')) {

                if($hukuman->file_sk_hukuman) {
                    $oldPath = str_replace('/storage/', '', $hukuman->file_sk_hukuman);
                    Storage::disk('public')->delete($oldPath);
                }

                $file = $request->file('file_sk_hukuman');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('document', $fileName, 'public');
                $validateData['file_sk_hukuman'] = '/storage/' . $path;
            } else {
                unset($validateData['file_sk_hukuman']);
            }

            $hukuman->update($validateData);

            DB::commit();

            return redirect("/kepegawaian/hukuman")->with('success', 'Berhasil mengubah data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal mengubah data : ' . $e->getMessage());

            return back()->withInput()->with('error', "Error, terjadi kesalahan pada sistem!");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hukuman $hukuman)
    {
        try {
            $hukuman->delete();

            return redirect('/kepegawaian/hukuman')->with('success', 'Berhasil menghapus data!');
        } catch(Exception $e) {
            Log::error('Gagal menghapus data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    /**
     * Download / cetak SK Hukuman.
     */
    public function downloadSK(Hukuman $hukuman)
    {
        $filePath = str_replace('/storage/', '', trim($hukuman->file_sk_hukuman));

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download(storage_path('app/public/' . $filePath));
    }

    public function cariHukuman(Request $request)
    {
        $user = Auth::user();

        $query = Hukuman::with("pegawai");

        // Filter berdasarkan role admin
        if ($user->role === 'admin') {
            $query->whereHas('pegawai', function($q) use ($user) {
                $q->where('unit_kerja_id', $user->unit_kerja_id);
            });
        }

        if($request->cariHukuman) {
            $query->where(function($q) use ($request) {

                // dari tabel izin kawin
                $q->where('jenis_hukuman', 'like', '%' . $request->cariHukuman . '%');

                $q->orWhere('tingkat_hukuman', 'like', '%' . $request->cariHukuman . '%')

                // dari relasi pegawai
                ->orWhereHas('pegawai', function($q2) use ($request) {
                    $q2->where('nama', 'like', '%' . $request->cariHukuman . '%');
                });

            });
        }


        $hukuman = $query->paginate(5);

        return view("pages.dashboard.kepegawaian.hukuman.indexHukuman", [
            'hukuman' => $hukuman
        ]);
    }
}
