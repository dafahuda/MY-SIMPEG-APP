<?php

namespace App\Http\Controllers;

use App\Models\DokumenPegawai;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;

class DokumenPegawaiController extends Controller
{
    private const MAX_SIZE_KB = 5120; // 5 MB

    public function index(Pegawai $pegawai)
    {
        $this->authorizeAkses($pegawai);

        return view('pages.dashboard.data_pegawai.dokumenPegawai', [
            'pegawai' => $pegawai->load('unit_kerja'),
            'dokumen' => $pegawai->dokumen()->latest()->get(),
        ]);
    }

    public function store(Request $request, Pegawai $pegawai)
    {
        $this->authorizeAkses($pegawai, tulis: true);

        $validated = $request->validate([
            'jenis_dokumen' => ['required', 'string', 'in:' . implode(',', array_keys(DokumenPegawai::JENIS_DOKUMEN))],
            'nama_dokumen' => ['required', 'string', 'max:150'],
            'file' => [
                'required',
                File::types(['pdf', 'jpg', 'jpeg', 'png'])->max(self::MAX_SIZE_KB),
            ],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ], [
            'jenis_dokumen.in' => 'Jenis dokumen tidak valid.',
            'file.max' => 'Ukuran file maksimal 5 MB.',
            'file.types' => 'File harus berupa PDF, JPG, atau PNG.',
        ]);

        $file = $request->file('file');
        $path = $file->store("dokumen-pegawai/{$pegawai->id}", 'private');

        $pegawai->dokumen()->create([
            'jenis_dokumen' => $validated['jenis_dokumen'],
            'nama_dokumen' => $validated['nama_dokumen'],
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => Auth::id(),
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return back()->with('success', 'Dokumen berhasil diunggah.');
    }

    public function show(DokumenPegawai $dokumen)
    {
        $this->authorizeAkses($dokumen->pegawai);

        if (! Storage::disk('private')->exists($dokumen->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('private')->response($dokumen->file_path);
    }

    public function download(DokumenPegawai $dokumen)
    {
        $this->authorizeAkses($dokumen->pegawai);

        if (! Storage::disk('private')->exists($dokumen->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('private')->download($dokumen->file_path, $dokumen->file_name);
    }

    public function destroy(DokumenPegawai $dokumen)
    {
        $this->authorizeAkses($dokumen->pegawai, tulis: true);

        // Hanya admin/superadmin yang boleh menghapus dokumen
        if (! in_array(Auth::user()->role, ['admin', 'superadmin'])) {
            abort(403, 'Hanya admin yang dapat menghapus dokumen.');
        }

        Storage::disk('private')->delete($dokumen->file_path);
        $dokumen->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }

    /**
     * Pegawai hanya boleh mengakses dokumen dirinya sendiri;
     * admin hanya di unit kerjanya; superadmin semua.
     */
    private function authorizeAkses(Pegawai $pegawai, bool $tulis = false): void
    {
        $user = Auth::user();

        if ($user->role === 'superadmin') {
            return;
        }

        if ($user->role === 'admin' && $pegawai->unit_kerja_id === $user->unit_kerja_id) {
            return;
        }

        if ($user->role === 'pegawai' && $user->pegawai?->id === $pegawai->id) {
            return;
        }

        abort(403, 'Anda tidak berhak mengakses dokumen pegawai ini.');
    }
}
