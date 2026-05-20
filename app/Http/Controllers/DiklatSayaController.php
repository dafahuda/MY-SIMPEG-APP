<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\PengajuanDiklat;
use App\Models\RencanaDiklat;
use App\Models\User;
use App\Support\FileUploadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DiklatSayaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $this->ensurePegawai($user);
        $pegawai = $this->currentPegawai($user);

        $rencanaDiklat = RencanaDiklat::query()
            ->with(['diklat', 'pengajuanDiklat' => fn ($query) => $query->latest('updated_at')])
            ->where('pegawai_id', $pegawai->id)
            ->orderByDesc('tahun_rencana')
            ->orderBy('nama_diklat_rencana')
            ->get();

        $riwayatDiklat = $pegawai->diklat()
            ->with('rencanaDiklat')
            ->orderByDesc('tahun')
            ->orderBy('nama_diklat')
            ->get();

        $latestPengajuan = $rencanaDiklat
            ->map(fn (RencanaDiklat $rencana) => $rencana->pengajuanDiklat->first())
            ->filter();

        $summary = [
            'assigned_count' => $rencanaDiklat->count(),
            'pending_count' => $latestPengajuan
                ->where('status', PengajuanDiklat::STATUS_PENDING)
                ->count(),
            'revision_count' => $latestPengajuan
                ->where('status', PengajuanDiklat::STATUS_REVISION_REQUESTED)
                ->count(),
            'official_count' => $riwayatDiklat->count(),
        ];

        $actionItems = $rencanaDiklat
            ->filter(function (RencanaDiklat $rencana): bool {
                $pengajuan = $rencana->pengajuanDiklat->first();

                return $this->canSubmit($rencana, $pengajuan)
                    || $pengajuan?->needsRevision() === true;
            })
            ->values();

        return view('pages.dashboard.pegawai.diklat_saya.index', [
            'pegawai' => $pegawai,
            'rencanaDiklat' => $rencanaDiklat,
            'riwayatDiklat' => $riwayatDiklat,
            'summary' => $summary,
            'actionItems' => $actionItems,
        ]);
    }

    public function show(string $pengajuanDiklat)
    {
        $user = Auth::user();
        $this->ensurePegawai($user);
        $pegawai = $this->currentPegawai($user);
        $rencana = $this->resolveAssignedRencana($pengajuanDiklat, $pegawai);
        $pengajuan = $rencana->pengajuanDiklat()->latest('updated_at')->first();

        return view('pages.dashboard.pegawai.diklat_saya.show', [
            'pegawai' => $pegawai,
            'rencana' => $rencana,
            'pengajuan' => $pengajuan,
            'canSubmit' => $this->canSubmit($rencana, $pengajuan),
            'canRevise' => $pengajuan?->needsRevision() === true,
        ]);
    }

    public function store(Request $request, string $rencanaDiklat)
    {
        $user = Auth::user();
        $this->ensurePegawai($user);
        $pegawai = $this->currentPegawai($user);
        $payload = $this->validatePayload($request, $pegawai, false);
        $rencana = $this->resolveAssignedRencana($rencanaDiklat, $pegawai);

        $this->ensureRencanaCanReceiveSubmission($rencana);

        if ($rencana->pengajuanDiklat()->whereIn('status', PengajuanDiklat::ACTIVE_STATUSES)->exists()) {
            throw ValidationException::withMessages([
                'rencana_diklat_id' => 'Pengajuan diklat untuk rencana ini sudah ada.',
            ]);
        }

        $filePath = $this->storeEvidence($request);

        PengajuanDiklat::create([
            'rencana_diklat_id' => $rencana->id,
            'pegawai_id' => $pegawai->id,
            'status' => PengajuanDiklat::STATUS_PENDING,
            'file_bukti' => $filePath,
            'nomor_sertifikat' => $payload['nomor_sertifikat'] ?? null,
            'tanggal_sertifikat' => $payload['tanggal_sertifikat'] ?? null,
            'jumlah_jam_realisasi' => $payload['jumlah_jam_realisasi'] ?? null,
            'catatan_pegawai' => $payload['catatan_pegawai'] ?? null,
            'submitted_at' => now(),
        ]);

        return redirect()->route('diklat_saya.index')->with('success', 'Bukti diklat berhasil dikirim untuk diverifikasi.');
    }

    public function update(Request $request, string $pengajuanDiklat)
    {
        $user = Auth::user();
        $this->ensurePegawai($user);
        $pegawai = $this->currentPegawai($user);
        $pengajuan = PengajuanDiklat::query()
            ->with('rencanaDiklat')
            ->where('pegawai_id', $pegawai->id)
            ->findOrFail($pengajuanDiklat);
        $payload = $this->validatePayload($request, $pegawai, false);

        if (! $pengajuan->needsRevision()) {
            throw ValidationException::withMessages([
                'status' => 'Pengajuan hanya dapat direvisi setelah verifikator meminta revisi.',
            ]);
        }

        $this->ensureRencanaCanReceiveSubmission($pengajuan->rencanaDiklat);
        $this->deleteEvidence($pengajuan->file_bukti);
        $filePath = $this->storeEvidence($request);

        $pengajuan->update([
            'status' => PengajuanDiklat::STATUS_PENDING,
            'file_bukti' => $filePath,
            'nomor_sertifikat' => $payload['nomor_sertifikat'] ?? null,
            'tanggal_sertifikat' => $payload['tanggal_sertifikat'] ?? null,
            'jumlah_jam_realisasi' => $payload['jumlah_jam_realisasi'] ?? null,
            'catatan_pegawai' => $payload['catatan_pegawai'] ?? null,
            'catatan_verifikator' => null,
            'verified_by' => null,
            'verified_at' => null,
            'revision_count' => ((int) $pengajuan->revision_count) + 1,
            'submitted_at' => now(),
        ]);

        return redirect()->route('diklat_saya.index')->with('success', 'Revisi bukti diklat berhasil dikirim ulang.');
    }

    private function validatePayload(Request $request, Pegawai $pegawai, bool $requireRencana = true): array
    {
        $rules = [
            'file_bukti' => ['required', 'file', 'mimetypes:application/pdf,image/jpeg,image/png', 'max:2048'],
            'nomor_sertifikat' => ['nullable', 'string', 'max:255'],
            'tanggal_sertifikat' => ['nullable', 'date'],
            'jumlah_jam_realisasi' => ['nullable', 'integer', 'min:1'],
            'catatan_pegawai' => ['nullable', 'string'],
        ];

        if ($requireRencana) {
            $rules['rencana_diklat_id'] = [
                'required',
                Rule::exists('tb_rencana_diklat', 'id')->where(fn ($query) => $query->where('pegawai_id', $pegawai->id)),
            ];
        }

        return $request->validate($rules);
    }

    private function resolveAssignedRencana(string $id, Pegawai $pegawai): RencanaDiklat
    {
        return RencanaDiklat::query()
            ->with(['diklat', 'pengajuanDiklat'])
            ->where('pegawai_id', $pegawai->id)
            ->findOrFail($id);
    }

    private function ensureRencanaCanReceiveSubmission(RencanaDiklat $rencanaDiklat): void
    {
        if ($rencanaDiklat->status === 'cancelled') {
            throw ValidationException::withMessages([
                'rencana_diklat_id' => 'Rencana diklat yang dibatalkan tidak dapat diajukan.',
            ]);
        }

        if ($rencanaDiklat->diklat()->exists()) {
            throw ValidationException::withMessages([
                'rencana_diklat_id' => 'Rencana diklat ini sudah memiliki realisasi resmi.',
            ]);
        }
    }

    private function canSubmit(RencanaDiklat $rencanaDiklat, ?PengajuanDiklat $pengajuanDiklat): bool
    {
        return $rencanaDiklat->status !== 'cancelled'
            && ! $rencanaDiklat->diklat
            && ! $pengajuanDiklat;
    }

    private function storeEvidence(Request $request): string
    {
        $file = $request->file('file_bukti');
        $stored = FileUploadHelper::validateAndStore(
            $file,
            ['application/pdf', 'image/jpeg', 'image/png'],
            2048 * 1024,
            'public',
            'document',
            'file_bukti'
        );

        return $stored['file_path'];
    }

    private function deleteEvidence(?string $path): void
    {
        FileUploadHelper::delete($path, 'public');
    }

    private function currentPegawai(User $user): Pegawai
    {
        return Pegawai::query()->where('user_id', $user->id)->firstOrFail();
    }

    private function ensurePegawai(User $user): void
    {
        abort_unless($user->role === 'pegawai', 403);
    }
}
