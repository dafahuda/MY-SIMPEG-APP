<?php

namespace App\Http\Controllers;

use App\Models\Diklat;
use App\Models\PengajuanDiklat;
use App\Models\RencanaDiklat;
use App\Models\User;
use App\Services\DiklatScopeService;
use App\Support\DiklatGlossary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DiklatVerifikasiController extends Controller
{
    public function __construct(private readonly DiklatScopeService $scopeService)
    {
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $this->ensureVerifier($user);
        $filters = [
            'search' => $request->string('search')->toString(),
            'status' => $request->string('status')->toString(),
        ];

        $query = $this->scopeService->scopePengajuanDiklatQuery(
            PengajuanDiklat::query()->with(['pegawai.unit_kerja', 'rencanaDiklat']),
            $user
        );

        if (filled($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (filled($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search): void {
                $builder->whereHas('pegawai', function ($pegawaiQuery) use ($search): void {
                    $pegawaiQuery->where('nama', 'like', '%' . $search . '%')
                        ->orWhere('nip', 'like', '%' . $search . '%');
                })->orWhereHas('rencanaDiklat', function ($rencanaQuery) use ($search): void {
                    $rencanaQuery->where('nama_diklat_rencana', 'like', '%' . $search . '%')
                        ->orWhere('tahun_rencana', 'like', '%' . $search . '%');
                });
            });
        }

        $pengajuanDiklat = $query
            ->orderByRaw("case when status = 'pending' then 0 when status = 'revision_requested' then 1 else 2 end")
            ->latest('submitted_at')
            ->latest('updated_at')
            ->paginate(10)
            ->withQueryString();

        return view('pages.dashboard.kepegawaian.diklat_verifikasi.index', [
            'pengajuanDiklat' => $pengajuanDiklat,
            'filters' => $filters,
            'statusOptions' => DiklatGlossary::submissionStatusOptions(),
        ]);
    }

    public function show(string $pengajuanDiklat)
    {
        $user = Auth::user();
        $this->ensureVerifier($user);
        $pengajuan = $this->resolveScopedSubmission($pengajuanDiklat, $user);

        return view('pages.dashboard.kepegawaian.diklat_verifikasi.show', [
            'pengajuan' => $pengajuan,
            'canVerify' => $pengajuan->isPending(),
        ]);
    }

    public function approve(Request $request, string $pengajuanDiklat)
    {
        $user = Auth::user();
        $this->ensureVerifier($user);

        DB::transaction(function () use ($pengajuanDiklat, $user): void {
            $pengajuan = $this->resolveScopedSubmissionForUpdate($pengajuanDiklat, $user);
            $rencanaDiklat = RencanaDiklat::query()->lockForUpdate()->findOrFail($pengajuan->rencana_diklat_id);

            $this->ensurePending($pengajuan);
            $this->ensureApprovableRencana($pengajuan, $rencanaDiklat);

            $tanggalRealisasi = $pengajuan->tanggal_sertifikat?->format('Y-m-d')
                ?? $rencanaDiklat->tahun_rencana . '-12-31';

            $diklat = Diklat::create([
                'pegawai_id' => $pengajuan->pegawai_id,
                'rencana_diklat_id' => $rencanaDiklat->id,
                'nama_diklat' => $rencanaDiklat->nama_diklat_rencana,
                'jumlah_jam' => $pengajuan->jumlah_jam_realisasi ?? $rencanaDiklat->target_jam,
                'penyelenggara' => $rencanaDiklat->target_penyelenggara ?? 'Tidak tercatat',
                'tempat' => 'Tidak tercatat',
                'angkatan' => 'Tidak tercatat',
                'tahun' => substr($tanggalRealisasi, 0, 4),
                'no_sttpp' => $pengajuan->nomor_sertifikat ?? 'PENGAJUAN-' . $pengajuan->id,
                'tgl_sttpp' => $tanggalRealisasi,
                'file_sertifikat_diklat' => $pengajuan->file_bukti,
            ]);

            $pengajuan->update([
                'status' => PengajuanDiklat::STATUS_APPROVED,
                'diklat_id' => $diklat->id,
                'verified_by' => $user->id,
                'verified_at' => now(),
            ]);

            $rencanaDiklat->update(['status' => 'realized']);
        });

        return redirect()->route('diklat_verifikasi.index')->with('success', 'Pengajuan diklat berhasil disetujui.');
    }

    public function reject(Request $request, string $pengajuanDiklat)
    {
        $user = Auth::user();
        $this->ensureVerifier($user);

        $validated = $request->validate([
            'catatan_verifikator' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($pengajuanDiklat, $user, $validated): void {
            $pengajuan = $this->resolveScopedSubmissionForUpdate($pengajuanDiklat, $user);
            $this->ensurePending($pengajuan);

            $pengajuan->update([
                'status' => PengajuanDiklat::STATUS_REVISION_REQUESTED,
                'catatan_verifikator' => $validated['catatan_verifikator'] ?? null,
                'verified_by' => $user->id,
                'verified_at' => now(),
            ]);
        });

        return redirect()->route('diklat_verifikasi.index')->with('success', 'Pengajuan diklat dikembalikan untuk revisi.');
    }

    private function resolveScopedSubmission(string $id, User $user): PengajuanDiklat
    {
        return $this->scopeService->scopePengajuanDiklatQuery(
            PengajuanDiklat::query()->with(['pegawai.unit_kerja', 'rencanaDiklat', 'diklat', 'verifier']),
            $user
        )->findOrFail($id);
    }

    private function resolveScopedSubmissionForUpdate(string $id, User $user): PengajuanDiklat
    {
        return $this->scopeService->scopePengajuanDiklatQuery(
            PengajuanDiklat::query()->lockForUpdate(),
            $user
        )->findOrFail($id);
    }

    private function ensurePending(PengajuanDiklat $pengajuan): void
    {
        if (! $pengajuan->isPending()) {
            throw ValidationException::withMessages([
                'status' => 'Hanya pengajuan yang menunggu verifikasi yang dapat diproses.',
            ]);
        }
    }

    private function ensureApprovableRencana(PengajuanDiklat $pengajuan, RencanaDiklat $rencanaDiklat): void
    {
        if ($rencanaDiklat->status === 'cancelled') {
            throw ValidationException::withMessages([
                'rencana_diklat_id' => 'Rencana diklat yang dibatalkan tidak dapat disetujui.',
            ]);
        }

        if ((int) $pengajuan->pegawai_id !== (int) $rencanaDiklat->pegawai_id) {
            throw ValidationException::withMessages([
                'rencana_diklat_id' => 'Pengajuan diklat tidak sesuai dengan pegawai pada rencana.',
            ]);
        }

        if ($rencanaDiklat->diklat()->exists()) {
            throw ValidationException::withMessages([
                'rencana_diklat_id' => 'Rencana diklat ini sudah memiliki realisasi resmi.',
            ]);
        }

        if ($pengajuan->diklat_id !== null) {
            throw ValidationException::withMessages([
                'diklat_id' => 'Pengajuan diklat ini sudah terhubung ke realisasi resmi.',
            ]);
        }
    }

    private function ensureVerifier(User $user): void
    {
        abort_unless(in_array($user->role, ['admin', 'superadmin'], true), 403);
    }
}
