<?php

namespace App\Http\Controllers;

use App\Models\LatihanJabatan;
use App\Models\Pegawai;
use App\Services\DiklatScopeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class LatihanJabatanController extends Controller
{
    public function __construct(private readonly DiklatScopeService $scopeService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $latihaJabatan = $this->scopeService->scopeRelatedToPegawai(
            LatihanJabatan::with('pegawai'),
            $user
        )->paginate(simpeg_per_page());

        return view("pages.dashboard.kepegawaian.latihanJabatan.indexLatihanJabatan", [
            'latihanJabatan' => $latihaJabatan
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

        return view("pages.dashboard.kepegawaian.latihanJabatan.tambahLatihanJabatan", [
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
            'tempat_latihan' => 'required|string',
            'waktu_latihan' => 'required|date',
            'tahun_latihan' => 'required|string',
            'nama_pelatih' => 'required|string',
            'jumlah_jam' => 'required|string',
            'nomor_sertifikat' => 'required|string',
            'tgl_sertifikat' => 'required|date',
            'file_sertifikat' => 'required|file|mimes:pdf,docx,txt|max:10240'
        ]);

        try {
            DB::beginTransaction();

            if($request->hasFile('file_sertifikat')) {
                $file = $request->file('file_sertifikat');
                $fileName = time() . '_' . (\Illuminate\Support\Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'file') . '.' . strtolower($file->getClientOriginalExtension());
                $path = $file->storeAs('document', $fileName, 'public');
                $validateData['file_sertifikat'] = '/storage/' . $path;
            }

            LatihanJabatan::create($validateData);

            DB::commit();

            return redirect("/kepegawaian/latihan_jabatan")->with('success', 'Berhasil menambahkan data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal menambahkan data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LatihanJabatan $latihanJabatan)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $pegawai = Pegawai::where('unit_kerja_id', $user->unit_kerja_id)->get();
        } else {
            $pegawai = Pegawai::all();
        }

        return view("pages.dashboard.kepegawaian.latihanJabatan.editLatihanJabatan", [
            'pegawai' => $pegawai,
            'latihanJabatan' => $latihanJabatan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LatihanJabatan $latihanJabatan)
    {
        $validateData = $request->validate([
            'pegawai_id' => 'required|exists:tb_pegawai,id',
            'tempat_latihan' => 'required|string',
            'waktu_latihan' => 'required|date',
            'tahun_latihan' => 'required|string',
            'nama_pelatih' => 'required|string',
            'jumlah_jam' => 'required|string',
            'nomor_sertifikat' => 'required|string',
            'tgl_sertifikat' => 'required|date',
            'file_sertifikat' => 'file|mimes:pdf,docx,txt|max:10240'
        ]);

        try {
            DB::beginTransaction();

            if($request->hasFile('file_sertifikat')) {

                if($request->fileLama) {
                    Storage::disk('public')->delete($request->fileLama);
                }

                $file = $request->file('file_sertifikat');
                $fileName = time() . '_' . (\Illuminate\Support\Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'file') . '.' . strtolower($file->getClientOriginalExtension());
                $path = $file->storeAs('document', $fileName, 'public');
                $validateData['file_sertifikat'] = '/storage/' . $path;
            }

            $latihanJabatan->update($validateData);

            DB::commit();

            return redirect("/kepegawaian/latihan_jabatan")->with('success', 'Berhasil mengubah data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal mengubah data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LatihanJabatan $latihanJabatan)
    {
        $latihanJabatan->delete();

        return redirect('/kepegawaian/latihan_jabatan')->with('success', 'Berhasil menghapus data!');
    }

    public function downloadSertifikat(LatihanJabatan $latihanJabatan)
    {
        $filePath = str_replace('/storage/', '', trim($latihanJabatan->file_sertifikat));

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk('public')->download($filePath);
    }

    public function cariLatihanJabatan(Request $request)
    {
        $user = Auth::user();

        $query = $this->scopeService->scopeRelatedToPegawai(
            LatihanJabatan::with('pegawai'),
            $user
        );

        if($request->cariLatihanJabatan) {
            $query->where(function($q) use ($request) {

                // dari tabel latihan jabatan
                $q->where('nama_pelatih', 'like', '%' . $request->cariLatihanJabatan . '%');

                $q->orWhere('tempat_latihan', 'like', '%' . $request->cariLatihanJabatan . '%')

                // dari relasi pegawai
                ->orWhereHas('pegawai', function($q2) use ($request) {
                    $q2->where('nama', 'like', '%' . $request->cariLatihanJabatan . '%');
                });

            });
        }

        $latihanJabatan = $query->paginate(simpeg_per_page());

        return view("pages.dashboard.kepegawaian.latihanJabatan.indexLatihanJabatan", [
            'latihanJabatan' => $latihanJabatan
        ]);
    }
}
