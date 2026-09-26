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

        if ($user->role === 'pegawai') {
            // Pegawai hanya melihat cuti miliknya sendiri
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            $cuti = Cuti::with('pegawai')
                ->when($myPegawai, fn($q) => $q->where('pegawai_id', $myPegawai->id))
                ->paginate(10);
        } elseif ($user->role === 'admin') {
            $cuti = Cuti::with('pegawai')
                ->whereHas('pegawai', function($query) use ($user) {
                    $query->where('unit_kerja_id', $user->unit_kerja_id);
                })
                ->paginate(10);
        } else {
            $cuti = Cuti::with('pegawai')->paginate(10);
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
     * Setujui pengajuan cuti (admin/superadmin).
     */
    public function approve(Request $request, Cuti $cuti)
    {
        $user = Auth::user();
        abort_unless(in_array($user->role, ['admin', 'superadmin']), 403);

        // Admin hanya boleh menyetujui cuti pegawai di unit kerjanya
        if ($user->role === 'admin') {
            $pegawai = $cuti->pegawai;
            abort_unless($pegawai && $pegawai->unit_kerja_id === $user->unit_kerja_id, 403);
        }

        $cuti->update([
            'status' => 'disetujui',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'alasan_penolakan' => null,
        ]);

        return back()->with('success', 'Pengajuan cuti ' . $cuti->pegawai->nama . ' disetujui.');
    }

    /**
     * Tolak pengajuan cuti dengan alasan.
     */
    public function reject(Request $request, Cuti $cuti)
    {
        $user = Auth::user();
        abort_unless(in_array($user->role, ['admin', 'superadmin']), 403);

        $request->validate([
            'alasan_penolakan' => 'required|string|min:5|max:500',
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi.',
            'alasan_penolakan.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        if ($user->role === 'admin') {
            $pegawai = $cuti->pegawai;
            abort_unless($pegawai && $pegawai->unit_kerja_id === $user->unit_kerja_id, 403);
        }

        $cuti->update([
            'status' => 'ditolak',
            'alasan_penolakan' => $request->alasan_penolakan,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Pengajuan cuti ' . $cuti->pegawai->nama . ' ditolak.');
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

        return Storage::disk('public')->download($filePath);
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

        $cuti = $query->paginate(10);

        return view("pages.dashboard.kepegawaian.cuti.indexCuti", [
            'cuti' => $cuti
        ]);
    }
}
