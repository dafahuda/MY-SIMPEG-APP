<?php

namespace App\Http\Controllers;

use App\Models\Diklat;
use App\Models\Pegawai;
use App\Models\RencanaDiklat;
use App\Models\User;
use App\Services\DiklatScopeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DiklatController extends Controller
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

        $diklat = $this->scopeService->scopeDiklatQuery(
            Diklat::with(['pegawai', 'rencanaDiklat']),
            $user
        )->paginate(5);

        return view('pages.dashboard.kepegawaian.diklat.indexDiklat', [
            'diklat' => $diklat,
            'canMutate' => $this->canMutate($user),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $this->ensureCanMutate($user);

        return view('pages.dashboard.kepegawaian.diklat.tambahDiklat', [
            'pegawai' => $this->scopeService->scopePegawaiQuery(Pegawai::query()->orderBy('nama'), $user)->get(),
            'rencanaDiklatOptions' => $this->eligibleRencanaDiklatOptions(
                $request->old('pegawai_id'),
                $request->old('tahun'),
                $user
            ),
            'currentRencanaDiklat' => null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $this->ensureCanMutate($user);

        $validateData = $this->validatePayload($request, $user, true);
        $rencanaDiklat = $this->resolveEligibleRencanaDiklat($validateData, $user);

        try {
            DB::beginTransaction();

            if ($request->hasFile('file_sertifikat_diklat')) {
                $file = $request->file('file_sertifikat_diklat');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('document', $fileName, 'public');
                $validateData['file_sertifikat_diklat'] = '/storage/' . $path;
            }

            $validateData['rencana_diklat_id'] = $rencanaDiklat?->id;

            Diklat::create($validateData);

            if ($rencanaDiklat) {
                $rencanaDiklat->update(['status' => 'realized']);
            }

            DB::commit();

            return redirect('/kepegawaian/diklat')->with('success', 'Berhasil menambahkan data!');
        } catch (ValidationException $e) {
            DB::rollBack();

            throw $e;
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Gagal menambahkan data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Diklat $diklat)
    {
        $user = Auth::user();
        $this->ensureCanMutate($user);
        $this->ensureAccessible($diklat, $user);

        $diklat->loadMissing(['pegawai', 'rencanaDiklat']);

        return view('pages.dashboard.kepegawaian.diklat.editDiklat', [
            'diklat' => $diklat,
            'pegawai' => $this->scopeService->scopePegawaiQuery(Pegawai::query()->orderBy('nama'), $user)->get(),
            'rencanaDiklatOptions' => $this->filteredEligibleRencanaDiklatOptions(
                $request->old('pegawai_id', $diklat->pegawai_id),
                $request->old('tahun', $diklat->tahun),
                $user
            ),
            'currentRencanaDiklat' => $diklat->rencanaDiklat,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Diklat $diklat)
    {
        $user = Auth::user();
        $this->ensureCanMutate($user);
        $this->ensureAccessible($diklat, $user);

        $validateData = $this->validatePayload($request, $user, false);
        $rencanaDiklat = $this->resolveEligibleRencanaDiklat($validateData, $user, $diklat);
        $previousRencanaDiklatId = $diklat->rencana_diklat_id;

        try {
            DB::beginTransaction();

            if ($request->hasFile('file_sertifikat_diklat')) {
                if ($diklat->file_sertifikat_diklat) {
                    $oldPath = str_replace('/storage/', '', $diklat->file_sertifikat_diklat);
                    Storage::disk('public')->delete($oldPath);
                }

                $file = $request->file('file_sertifikat_diklat');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('document', $fileName, 'public');
                $validateData['file_sertifikat_diklat'] = '/storage/' . $path;
            } else {
                unset($validateData['file_sertifikat_diklat']);
            }

            $validateData['rencana_diklat_id'] = $rencanaDiklat?->id;

            $diklat->update($validateData);

            if ($previousRencanaDiklatId && (int) $previousRencanaDiklatId !== (int) $validateData['rencana_diklat_id']) {
                RencanaDiklat::query()->whereKey($previousRencanaDiklatId)->update(['status' => 'planned']);
            }

            if ($rencanaDiklat) {
                RencanaDiklat::query()->whereKey($rencanaDiklat->id)->update(['status' => 'realized']);
            }

            DB::commit();

            return redirect('/kepegawaian/diklat')->with('success', 'Berhasil mengubah data!');
        } catch (ValidationException $e) {
            DB::rollBack();

            throw $e;
        } catch (Exception $e) {
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
        $this->ensureCanMutate($user);
        $this->ensureAccessible($diklat, $user);

        try {
            if ($diklat->rencana_diklat_id) {
                RencanaDiklat::query()->whereKey($diklat->rencana_diklat_id)->update(['status' => 'planned']);
            }

            $diklat->delete();

            return redirect('/kepegawaian/diklat')->with('success', 'Berhasil menghapus data!');
        } catch (Exception $e) {
            Log::error('Gagal menghapus data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    public function downloadSertifikatDiklat(Diklat $diklat)
    {
        $user = Auth::user();
        $this->ensureAccessible($diklat, $user);

        $filePath = ltrim(str_replace('/storage/', '', trim($diklat->file_sertifikat_diklat)), '/');

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk('public')->download($filePath);
    }

    public function cariDiklat(Request $request)
    {
        $user = Auth::user();

        $query = $this->scopeService->scopeDiklatQuery(
            Diklat::with(['pegawai', 'rencanaDiklat']),
            $user
        );

        if ($request->cariDiklat) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_diklat', 'like', '%' . $request->cariDiklat . '%')
                    ->orWhere('penyelenggara', 'like', '%' . $request->cariDiklat . '%')
                    ->orWhere('tahun', 'like', '%' . $request->cariDiklat . '%')
                    ->orWhereHas('pegawai', function ($q2) use ($request) {
                        $q2->where('nama', 'like', '%' . $request->cariDiklat . '%');
                    });
            });
        }

        $diklat = $query->paginate(5);

        return view('pages.dashboard.kepegawaian.diklat.indexDiklat', [
            'diklat' => $diklat,
            'canMutate' => $this->canMutate($user),
        ]);
    }

    private function validatePayload(Request $request, User $user, bool $fileRequired): array
    {
        $pegawaiRule = Rule::exists('tb_pegawai', 'id');

        if ($user->role === 'admin') {
            $pegawaiRule = $pegawaiRule->where(function ($query) use ($user): void {
                $query->where('unit_kerja_id', $user->unit_kerja_id);
            });
        }

        return $request->validate([
            'pegawai_id' => ['required', $pegawaiRule],
            'nama_diklat' => ['required', 'string'],
            'jumlah_jam' => ['required', 'string'],
            'penyelenggara' => ['required', 'string'],
            'tempat' => ['required', 'string'],
            'angkatan' => ['required', 'string'],
            'tahun' => ['required', 'string'],
            'rencana_diklat_id' => ['nullable', 'integer', Rule::exists('tb_rencana_diklat', 'id')],
            'no_sttpp' => ['required', 'string'],
            'tgl_sttpp' => ['required', 'date'],
            'file_sertifikat_diklat' => $fileRequired
                ? ['required', 'file', 'mimes:pdf,docx,txt', 'max:10240']
                : ['nullable', 'file', 'mimes:pdf,docx,txt', 'max:10240'],
        ]);
    }

    private function eligibleRencanaDiklatOptions($pegawaiId, $tahun, User $user)
    {
        return $this->scopeService->scopeRencanaDiklatQuery(
            RencanaDiklat::query()->with('pegawai'),
            $user
        )
            ->where('status', 'planned')
            ->orderBy('pegawai_id')
            ->orderBy('tahun_rencana')
            ->orderBy('nama_diklat_rencana')
            ->get();
    }

    private function filteredEligibleRencanaDiklatOptions($pegawaiId, $tahun, User $user)
    {
        if (blank($pegawaiId) || blank($tahun)) {
            return collect();
        }

        $pegawaiId = (int) $pegawaiId;
        $tahun = (int) $tahun;

        return $this->scopeService->scopeRencanaDiklatQuery(
            RencanaDiklat::query()->with('pegawai'),
            $user
        )
            ->where('pegawai_id', $pegawaiId)
            ->where('status', 'planned')
            ->where(function ($query) use ($tahun): void {
                $query->where('tahun_rencana', $tahun)
                    ->orWhereRaw('(tahun_rencana + 1) = ?', [$tahun]);
            })
            ->orderBy('tahun_rencana')
            ->orderBy('nama_diklat_rencana')
            ->get();
    }

    private function resolveEligibleRencanaDiklat(array $validateData, User $user, ?Diklat $diklat = null): ?RencanaDiklat
    {
        if (blank($validateData['rencana_diklat_id'] ?? null)) {
            return null;
        }

        $rencanaDiklat = $this->scopeService->scopeRencanaDiklatQuery(
            RencanaDiklat::query()->with('diklat'),
            $user
        )->find($validateData['rencana_diklat_id']);

        if (! $rencanaDiklat) {
            throw ValidationException::withMessages([
                'rencana_diklat_id' => 'Rencana diklat tidak ditemukan.',
            ]);
        }

        if ((int) $rencanaDiklat->pegawai_id !== (int) $validateData['pegawai_id']) {
            throw ValidationException::withMessages([
                'rencana_diklat_id' => 'Rencana diklat harus milik pegawai yang sama.',
            ]);
        }

        $tahunDiklat = (int) $validateData['tahun'];
        $tahunRencana = (int) $rencanaDiklat->tahun_rencana;

        if ($tahunDiklat !== $tahunRencana && $tahunDiklat !== ($tahunRencana + 1)) {
            throw ValidationException::withMessages([
                'rencana_diklat_id' => 'Rencana diklat hanya bisa dipilih pada tahun yang sama atau satu tahun setelahnya.',
            ]);
        }

        if ($rencanaDiklat->status === 'cancelled') {
            throw ValidationException::withMessages([
                'rencana_diklat_id' => 'Rencana diklat berstatus cancelled tidak bisa dipakai.',
            ]);
        }

        if ($diklat && (int) $diklat->rencana_diklat_id === (int) $rencanaDiklat->id) {
            return $rencanaDiklat;
        }

        if ($rencanaDiklat->status !== 'planned') {
            throw ValidationException::withMessages([
                'rencana_diklat_id' => 'Rencana diklat harus berstatus planned sebelum di-link.',
            ]);
        }

        if ($rencanaDiklat->diklat) {
            throw ValidationException::withMessages([
                'rencana_diklat_id' => 'Satu rencana diklat hanya boleh memiliki satu realisasi.',
            ]);
        }

        return $rencanaDiklat;
    }

    private function ensureCanMutate(User $user): void
    {
        abort_unless($this->canMutate($user), 403);
    }

    private function ensureAccessible(Diklat $diklat, User $user): void
    {
        $accessible = $this->scopeService->scopeDiklatQuery(
            Diklat::query()->whereKey($diklat->getKey()),
            $user
        )->exists();

        abort_unless($accessible, 403);
    }

    private function canMutate(User $user): bool
    {
        return in_array($user->role, ['admin', 'superadmin'], true);
    }
}
