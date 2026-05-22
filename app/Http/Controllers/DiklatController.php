<?php

namespace App\Http\Controllers;

use App\Models\Diklat;
use App\Models\Pegawai;
use App\Models\RencanaDiklat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Support\FileUploadHelper;

class DiklatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'pegawai') {
            return redirect()->route('diklat_saya.index');
        }

        if ($user->role === 'admin') {
            $diklat = Diklat::with(['pegawai', 'rencanaDiklat'])
                ->whereHas('pegawai', function($query) use ($user) {
                    $query->where('unit_kerja_id', $user->unit_kerja_id);
                })
                ->paginate(5);
        } else {
            $diklat = Diklat::with(['pegawai', 'rencanaDiklat'])->paginate(5);
        }

        return view("pages.dashboard.kepegawaian.diklat.indexDiklat", [
            'diklat' => $diklat
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        if ($user->role === 'pegawai') {
            abort(403);
        }

        if ($user->role === 'admin') {
            $pegawai = Pegawai::where('unit_kerja_id', $user->unit_kerja_id)->get();
        } else {
            $pegawai = Pegawai::all();
        }

        return view("pages.dashboard.kepegawaian.diklat.tambahDiklat", [
            'pegawai' => $pegawai
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'pegawai') {
            abort(403);
        }

        $validateData = $request->validate([
            'pegawai_id' => 'required|exists:tb_pegawai,id',
            'rencana_diklat_id' => 'nullable|exists:tb_rencana_diklat,id',
            'nama_diklat' => 'required|string',
            'jumlah_jam' => 'required|string',
            'penyelenggara' => 'required|string',
            'tempat' => 'required|string',
            'angkatan' => 'required|string',
            'tahun' => 'required|string',
            'no_sttpp' => 'required|string',
            'tgl_sttpp' => 'required|date',
            'file_sertifikat_diklat' => 'required|file|mimetypes:application/pdf,image/jpeg,image/png|max:2048'
        ]);

        if ($user->role === 'admin') {
            $pegawai = Pegawai::findOrFail($validateData['pegawai_id']);

            if ($pegawai->unit_kerja_id !== $user->unit_kerja_id) {
                return back()->withInput()->withErrors([
                    'pegawai_id' => 'Pegawai yang dipilih berada di luar unit kerja Anda.',
                ]);
            }
        }

        try {
            DB::beginTransaction();

            if ($request->hasFile('file_sertifikat_diklat')) {
                $file = $request->file('file_sertifikat_diklat');
                $upload = FileUploadHelper::validateAndStore(
                    $file,
                    ['application/pdf', 'image/jpeg', 'image/png'],
                    2048 * 1024,
                    'public',
                    'document',
                    'file_sertifikat_diklat'
                );
                $validateData['file_sertifikat_diklat'] = $upload['file_path'];
                $validateData['original_filename'] = $upload['original_name'];
            }

            Diklat::create($validateData);

            if (!empty($validateData['rencana_diklat_id'])) {
                RencanaDiklat::where('id', $validateData['rencana_diklat_id'])
                    ->update(['status' => 'realized']);
            }

            DB::commit();

            return redirect('/kepegawaian/diklat')->with('success', 'Berhasil menambahkan data!');
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal menambahkan data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Diklat $diklat)
    {
        $user = Auth::user();

        if ($user->role === 'pegawai') {
            abort(403);
        }

        $diklat->load('pegawai');

        if ($user->role === 'admin' && $diklat->pegawai?->unit_kerja_id !== $user->unit_kerja_id) {
            abort(403);
        }

        if ($user->role === 'admin') {
            $pegawai = Pegawai::where('unit_kerja_id', $user->unit_kerja_id)->get();
        } else {
            $pegawai = Pegawai::all();
        }

        $eligibleRencana = RencanaDiklat::where('pegawai_id', $diklat->pegawai_id)
            ->whereIn('status', ['planned', 'realized'])
            ->where(function ($q) use ($diklat) {
                $q->where('tahun_rencana', $diklat->tahun)
                    ->orWhere('tahun_rencana', (string) ((int) $diklat->tahun - 1));
            })
            ->orderBy('tahun_rencana', 'desc')
            ->orderBy('nama_diklat_rencana')
            ->get();

        return view("pages.dashboard.kepegawaian.diklat.editDiklat", [
            'diklat' => $diklat,
            'pegawai' => $pegawai,
            'eligibleRencana' => $eligibleRencana,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Diklat $diklat)
    {
        $user = Auth::user();

        if ($user->role === 'pegawai') {
            abort(403);
        }

        $diklat->load('pegawai');

        if ($user->role === 'admin' && $diklat->pegawai?->unit_kerja_id !== $user->unit_kerja_id) {
            abort(403);
        }

        $validateData = $request->validate([
            'pegawai_id' => 'required|exists:tb_pegawai,id',
            'rencana_diklat_id' => 'nullable|exists:tb_rencana_diklat,id',
            'nama_diklat' => 'required|string',
            'jumlah_jam' => 'required|string',
            'penyelenggara' => 'required|string',
            'tempat' => 'required|string',
            'angkatan' => 'required|string',
            'tahun' => 'required|string',
            'no_sttpp' => 'required|string',
            'tgl_sttpp' => 'required|date',
            'file_sertifikat_diklat' => 'nullable|file|mimetypes:application/pdf,image/jpeg,image/png|max:2048'
        ]);

        if ($user->role === 'admin') {
            $pegawai = Pegawai::findOrFail($validateData['pegawai_id']);

            if ($pegawai->unit_kerja_id !== $user->unit_kerja_id) {
                return back()->withInput()->withErrors([
                    'pegawai_id' => 'Pegawai yang dipilih berada di luar unit kerja Anda.',
                ]);
            }
        }

        try {
            DB::beginTransaction();

            if ($request->hasFile('file_sertifikat_diklat')) {
                FileUploadHelper::delete($diklat->file_sertifikat_diklat, 'public');

                $file = $request->file('file_sertifikat_diklat');
                $upload = FileUploadHelper::validateAndStore(
                    $file,
                    ['application/pdf', 'image/jpeg', 'image/png'],
                    2048 * 1024,
                    'public',
                    'document',
                    'file_sertifikat_diklat'
                );
                $validateData['file_sertifikat_diklat'] = $upload['file_path'];
                $validateData['original_filename'] = $upload['original_name'];
            } else {
                unset($validateData['file_sertifikat_diklat']);
                unset($validateData['original_filename']);
            }

            $previousRencanaId = $diklat->rencana_diklat_id;

            $diklat->update($validateData);

            if ($previousRencanaId && $previousRencanaId !== ($validateData['rencana_diklat_id'] ?? null)) {
                RencanaDiklat::where('id', $previousRencanaId)
                    ->whereDoesntHave('diklat')
                    ->update(['status' => 'planned']);
            }

            if (!empty($validateData['rencana_diklat_id'])) {
                RencanaDiklat::where('id', $validateData['rencana_diklat_id'])
                    ->update(['status' => 'realized']);
            }

            DB::commit();

            return redirect('/kepegawaian/diklat')->with('success', 'Berhasil mengubah data!');
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal mengubah data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Diklat $diklat)
    {
        $user = Auth::user();

        if ($user->role === 'pegawai') {
            abort(403);
        }

        $diklat->load('pegawai');

        if ($user->role === 'admin' && $diklat->pegawai?->unit_kerja_id !== $user->unit_kerja_id) {
            abort(403);
        }

        try {
            $rencanaId = $diklat->rencana_diklat_id;
            $certificatePath = $diklat->file_sertifikat_diklat;

            $diklat->delete();

            if ($certificatePath) {
                FileUploadHelper::delete($certificatePath, 'public');
            }

            if ($rencanaId) {
                RencanaDiklat::where('id', $rencanaId)
                    ->whereDoesntHave('diklat')
                    ->update(['status' => 'planned']);
            }

            return redirect('/kepegawaian/diklat')->with('success', 'Berhasil menghapus data!');
        } catch(Exception $e) {
            Log::error('Gagal menghapus data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    public function downloadSertifikatDiklat(Diklat $diklat)
    {
        $user = Auth::user();

        $diklat->load('pegawai');

        if ($user->role === 'pegawai') {
            if ($diklat->pegawai?->user_id !== $user->id) {
                abort(403);
            }
        }

        if ($user->role === 'admin' && $diklat->pegawai?->unit_kerja_id !== $user->unit_kerja_id) {
            abort(403);
        }

        $filePath = str_replace('/storage/', '', trim($diklat->file_sertifikat_diklat));

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download(storage_path('app/public/' . $filePath));
    }

    public function cariDiklat(Request $request)
    {
        $user = Auth::user();

        $query = Diklat::with('pegawai');

        // Filter berdasarkan role admin
        if ($user->role === 'admin') {
            $query->whereHas('pegawai', function($q) use ($user) {
                $q->where('unit_kerja_id', $user->unit_kerja_id);
            });
        }

        if($request->cariDiklat) {
            $query->where(function($q) use ($request) {

                // dari tabel diklat
                $q->where('nama_diklat', 'like', '%' . $request->cariDiklat . '%');

                $q->orWhere('penyelenggara', 'like', '%' . $request->cariDiklat . '%');

                $q->orWhere('tahun', 'like', '%' . $request->cariDiklat . '%')

                // dari relasi pegawai
                ->orWhereHas('pegawai', function($q2) use ($request) {
                    $q2->where('nama', 'like', '%' . $request->cariDiklat . '%');
                });

            });
        }

        $diklat = $query->paginate(5);

        return view("pages.dashboard.kepegawaian.diklat.indexDiklat", [
            'diklat' => $diklat
        ]);
    }
}
