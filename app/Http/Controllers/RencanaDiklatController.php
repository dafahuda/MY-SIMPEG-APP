<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\RencanaDiklat;
use App\Models\User;
use App\Services\DiklatScopeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class RencanaDiklatController extends Controller
{
    public function __construct(private readonly DiklatScopeService $scopeService)
    {
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $rencanaDiklat = $this->filteredQuery($request, $user)
            ->paginate(10)
            ->withQueryString();

        return view('pages.dashboard.kepegawaian.rencana_diklat.indexRencanaDiklat', [
            'rencanaDiklat' => $rencanaDiklat,
            'canMutate' => in_array($user->role, ['admin', 'superadmin'], true),
            'pegawaiFilterOptions' => $this->pegawaiListFor($user),
            'tahunFilterOptions' => $this->tahunFilterOptionsFor($user),
            'filters' => $this->resolvedFilters($request),
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        $this->ensureCanMutate($user);

        return view('pages.dashboard.kepegawaian.rencana_diklat.tambahRencanaDiklat', [
            'pegawaiList' => $this->pegawaiListFor($user),
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $this->ensureCanMutate($user);

        $validated = $this->validatePayload($request, $user);
        $validated['status'] = $this->resolveStatusForPersistence($request, null, $validated['status'] ?? 'planned');

        try {
            DB::beginTransaction();

            RencanaDiklat::create($validated);

            DB::commit();

            return redirect()->route('rencana_diklat.index')->with('success', 'Berhasil menambahkan rencana diklat!');
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Gagal menambahkan rencana diklat : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    public function edit(RencanaDiklat $rencanaDiklat)
    {
        $user = Auth::user();
        $this->ensureCanMutate($user);
        $this->ensureAccessible($rencanaDiklat, $user);

        return view('pages.dashboard.kepegawaian.rencana_diklat.editRencanaDiklat', [
            'rencanaDiklat' => $rencanaDiklat->load(['pegawai', 'diklat']),
            'pegawaiList' => $this->pegawaiListFor($user),
        ]);
    }

    public function update(Request $request, RencanaDiklat $rencanaDiklat)
    {
        $user = Auth::user();
        $this->ensureCanMutate($user);
        $this->ensureAccessible($rencanaDiklat, $user);

        $validated = $this->validatePayload($request, $user);
        $validated['status'] = $this->resolveStatusForPersistence($request, $rencanaDiklat, $validated['status'] ?? 'planned');

        if ($rencanaDiklat->diklat()->exists() && (
            (int) $validated['pegawai_id'] !== (int) $rencanaDiklat->pegawai_id
            || (string) $validated['tahun_rencana'] !== (string) $rencanaDiklat->tahun_rencana
            || $validated['nama_diklat_rencana'] !== $rencanaDiklat->nama_diklat_rencana
        )) {
            return back()->withInput()->with('error', 'Rencana diklat yang sudah terhubung tidak bisa mengubah identitasnya.');
        }

        try {
            DB::beginTransaction();

            $rencanaDiklat->update($validated);

            DB::commit();

            return redirect()->route('rencana_diklat.index')->with('success', 'Berhasil mengubah rencana diklat!');
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Gagal mengubah rencana diklat : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    public function destroy(RencanaDiklat $rencanaDiklat)
    {
        $user = Auth::user();
        $this->ensureCanMutate($user);
        $this->ensureAccessible($rencanaDiklat, $user);

        if ($rencanaDiklat->diklat()->exists()) {
            return back()->withInput()->with('error', 'Rencana diklat yang sudah terhubung tidak bisa dihapus.');
        }

        try {
            $rencanaDiklat->delete();

            return redirect()->route('rencana_diklat.index')->with('success', 'Berhasil menghapus rencana diklat!');
        } catch (Exception $e) {
            Log::error('Gagal menghapus rencana diklat : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    public function search(Request $request)
    {
        return redirect()->route('rencana_diklat.index', array_filter($this->resolvedFilters($request), static fn ($value) => filled($value)));
    }

    private function filteredQuery(Request $request, User $user)
    {
        $filters = $this->resolvedFilters($request);

        $query = $this->scopeService->scopeRencanaDiklatQuery(
            RencanaDiklat::with('pegawai')->orderByDesc('created_at'),
            $user
        );

        if (filled($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($builder) use ($search): void {
                $builder->where('tahun_rencana', 'like', '%' . $search . '%')
                    ->orWhere('nama_diklat_rencana', 'like', '%' . $search . '%')
                    ->orWhere('target_kompetensi', 'like', '%' . $search . '%')
                    ->orWhere('kategori_diklat', 'like', '%' . $search . '%')
                    ->orWhere('prioritas', 'like', '%' . $search . '%')
                    ->orWhere('target_penyelenggara', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhereHas('pegawai', function ($pegawaiQuery) use ($search): void {
                        $pegawaiQuery->where('nama', 'like', '%' . $search . '%');
                    });
            });
        }

        if (filled($filters['pegawai_id'])) {
            $query->where('pegawai_id', $filters['pegawai_id']);
        }

        if (filled($filters['tahun_rencana'])) {
            $query->where('tahun_rencana', $filters['tahun_rencana']);
        }

        if (filled($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    private function validatePayload(Request $request, User $user): array
    {
        abort_unless(in_array($user->role, ['admin', 'superadmin'], true), 403);

        $pegawaiRule = Rule::exists('tb_pegawai', 'id');

        if ($user->role === 'admin') {
            $pegawaiRule = $pegawaiRule->where(function ($query) use ($user): void {
                $query->where('unit_kerja_id', $user->unit_kerja_id);
            });
        }

        return $request->validate([
            'pegawai_id' => ['required', $pegawaiRule],
            'tahun_rencana' => ['required', 'digits:4'],
            'nama_diklat_rencana' => ['required', 'string'],
            'target_kompetensi' => ['required', 'string'],
            'kategori_diklat' => ['required', Rule::in(['Teknis', 'Manajerial', 'Sosial Kultural', 'Struktural'])],
            'prioritas' => ['required', Rule::in(['Rendah', 'Sedang', 'Tinggi'])],
            'target_jam' => ['required', 'integer', 'min:0'],
            'target_penyelenggara' => ['required', 'string'],
            'alasan_kebutuhan' => ['required', 'string'],
            'catatan' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'planned'])],
        ]);
    }

    private function ensureCanMutate(User $user): void
    {
        abort_unless(in_array($user->role, ['admin', 'superadmin'], true), 403);
    }

    private function ensureAccessible(RencanaDiklat $rencanaDiklat, User $user): void
    {
        $accessible = $this->scopeService->scopeRencanaDiklatQuery(
            RencanaDiklat::query()->whereKey($rencanaDiklat->getKey()),
            $user
        )->exists();

        abort_unless($accessible, 403);
    }

    private function pegawaiListFor(User $user)
    {
        return $this->scopeService->scopePegawaiQuery(
            Pegawai::query()->orderBy('nama'),
            $user
        )->get();
    }

    private function tahunFilterOptionsFor(User $user)
    {
        return $this->scopeService->scopeRencanaDiklatQuery(
            RencanaDiklat::query()->select('tahun_rencana')->distinct(),
            $user
        )
            ->orderByDesc('tahun_rencana')
            ->pluck('tahun_rencana');
    }

    private function resolvedFilters(Request $request): array
    {
        return [
            'search' => trim((string) $request->input('search', $request->input('cariRencanaDiklat', ''))),
            'pegawai_id' => (string) $request->input('pegawai_id', ''),
            'tahun_rencana' => (string) $request->input('tahun_rencana', ''),
            'status' => (string) $request->input('status', ''),
        ];
    }

    private function resolveStatusForPersistence(Request $request, ?RencanaDiklat $rencanaDiklat, string $fallbackStatus): string
    {
        if ($rencanaDiklat?->diklat()->exists()) {
            return $rencanaDiklat->status;
        }

        return $request->input('save_mode', $fallbackStatus) === 'draft'
            ? 'draft'
            : 'planned';
    }
}
