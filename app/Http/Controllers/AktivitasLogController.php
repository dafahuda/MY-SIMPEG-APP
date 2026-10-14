<?php

namespace App\Http\Controllers;

use App\Models\AktivitasLog;
use App\Support\Access;
use Illuminate\Http\Request;

class AktivitasLogController extends Controller
{
    /**
     * Riwayat aktivitas (audit trail) — hanya superadmin.
     */
    public function index(Request $request)
    {
        if (! Access::isSuperadmin()) {
            abort(403, 'Hanya superadmin yang dapat melihat riwayat aktivitas.');
        }

        $query = AktivitasLog::with('user')->orderByDesc('created_at');

        // Filter pencarian
        if ($search = $request->input('cariLog')) {
            $query->where(function ($q) use ($search) {
                $q->where('deskripsi', 'like', "%{$search}%")
                    ->orWhere('modul', 'like', "%{$search}%")
                    ->orWhere('username_snapshot', 'like', "%{$search}%");
            });
        }

        if ($aksi = $request->input('aksi')) {
            $query->where('aksi', $aksi);
        }

        if ($modul = $request->input('modul')) {
            $query->where('modul', $modul);
        }

        $logs = $query->paginate(simpeg_per_page())->withQueryString();
        $daftarModul = AktivitasLog::select('modul')->distinct()->orderBy('modul')->pluck('modul');

        return view('pages.dashboard.audit_trail.indexAktivitasLog', [
            'logs' => $logs,
            'daftarModul' => $daftarModul,
        ]);
    }
}
