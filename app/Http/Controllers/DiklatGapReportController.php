<?php

namespace App\Http\Controllers;

use App\Exports\DiklatGapExport;
use App\Models\Diklat;
use App\Models\Pegawai;
use App\Models\RencanaDiklat;
use App\Models\UnitKerja;
use App\Services\DiklatGapAnalyticsService;
use App\Services\DiklatScopeService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class DiklatGapReportController extends Controller
{
    public function index(Request $request, DiklatScopeService $scopeService, DiklatGapAnalyticsService $analyticsService)
    {
        $user = $request->user();

        $selectedTahunRencana = $this->normalizeYear($request->input('tahun_rencana'));
        $selectedTahunRealisasi = $this->normalizeYear($request->input('tahun_realisasi')) ?? $selectedTahunRencana;

        $selectedPegawaiId = $this->resolvePegawaiId($request, $user);
        $selectedUnitKerjaId = $this->resolveUnitKerjaId($request, $user, $selectedPegawaiId);

        $unitKerjaOptions = $this->buildUnitKerjaOptions($user, $selectedUnitKerjaId);
        $pegawaiOptions = $this->buildPegawaiOptions($scopeService, $user, $selectedUnitKerjaId);

        $reportReady = $selectedTahunRencana !== null;

        $summary = $this->zeroSummary();
        $plannedRows = collect();
        $realizedRows = collect();
        $notRealizedRows = collect();
        $outOfPlanRows = collect();
        $crossYearRealizedRows = collect();
        $selectedPegawai = null;
        $selectedUnitKerja = null;

        if ($reportReady) {
            $pegawaiQuery = $scopeService->scopePegawaiQuery(Pegawai::query()->with('unit_kerja'), $user);

            if ($selectedUnitKerjaId) {
                $pegawaiQuery->where('unit_kerja_id', $selectedUnitKerjaId);
            }

            if ($selectedPegawaiId) {
                $pegawaiQuery->whereKey($selectedPegawaiId);
            }

            $pegawaiIds = $pegawaiQuery->pluck('id')->all();

            $rencanaQuery = $scopeService->scopeRencanaDiklatQuery(
                RencanaDiklat::with(['pegawai.unit_kerja', 'diklat.pegawai.unit_kerja']),
                $user
            )
                ->where('tahun_rencana', $selectedTahunRencana)
                ->whereIn('pegawai_id', $pegawaiIds)
                ->orderBy('pegawai_id')
                ->orderBy('nama_diklat_rencana');

            $diklatQuery = $scopeService->scopeDiklatQuery(
                Diklat::with(['pegawai.unit_kerja', 'rencanaDiklat.pegawai.unit_kerja']),
                $user
            )
                ->where('tahun', $selectedTahunRealisasi)
                ->whereIn('pegawai_id', $pegawaiIds)
                ->orderBy('pegawai_id')
                ->orderBy('nama_diklat');

            $rencanaDiklat = $rencanaQuery->get();
            $diklat = $diklatQuery->get();

            $summary = $analyticsService->summary($rencanaDiklat, $diklat);

            $plannedRows = $this->mapPlannedRows($rencanaDiklat);
            $realizedRows = $this->mapRealizedRows($rencanaDiklat);
            $notRealizedRows = $this->mapNotRealizedRows($rencanaDiklat);
            $outOfPlanRows = $this->mapOutOfPlanRows($diklat);
            $crossYearRealizedRows = $this->mapCrossYearRealizedRows($rencanaDiklat);

            if ($selectedPegawaiId) {
                $selectedPegawai = $pegawaiOptions->firstWhere('id', $selectedPegawaiId);
            }

            if ($selectedUnitKerjaId) {
                $selectedUnitKerja = $unitKerjaOptions->firstWhere('id', $selectedUnitKerjaId);
            }
        }

        return view('pages.dashboard.report.diklat_gap_report', [
            'reportReady' => $reportReady,
            'selectedTahunRencana' => $selectedTahunRencana,
            'selectedTahunRealisasi' => $selectedTahunRealisasi,
            'selectedPegawaiId' => $selectedPegawaiId,
            'selectedUnitKerjaId' => $selectedUnitKerjaId,
            'selectedPegawai' => $selectedPegawai,
            'selectedUnitKerja' => $selectedUnitKerja,
            'unitKerjaOptions' => $unitKerjaOptions,
            'pegawaiOptions' => $pegawaiOptions,
            'summary' => $summary,
            'plannedRows' => $plannedRows,
            'realizedRows' => $realizedRows,
            'notRealizedRows' => $notRealizedRows,
            'outOfPlanRows' => $outOfPlanRows,
            'crossYearRealizedRows' => $crossYearRealizedRows,
        ]);
    }

    public function unitAggregate(Request $request, DiklatScopeService $scopeService, DiklatGapAnalyticsService $analyticsService)
    {
        $data = $this->buildUnitAggregateData($request, $scopeService, $analyticsService);

        return view('pages.dashboard.report.diklat_gap_unit_report', $data);
    }

    public function printUnitAggregate(Request $request, DiklatScopeService $scopeService, DiklatGapAnalyticsService $analyticsService)
    {
        $data = $this->buildUnitAggregateData($request, $scopeService, $analyticsService);

        return view('pages.dashboard.report.diklat_gap_unit_print', $data);
    }

    public function exportUnitAggregate(Request $request, DiklatScopeService $scopeService, DiklatGapAnalyticsService $analyticsService)
    {
        $data = $this->buildUnitAggregateData($request, $scopeService, $analyticsService);
        $filename = 'DiklatGapUnitReport'
            . ($data['selectedTahunRencana'] ? '_' . $data['selectedTahunRencana'] : '')
            . ($data['selectedTahunRealisasi'] ? '_' . $data['selectedTahunRealisasi'] : '')
            . '.xlsx';

        return Excel::download(new DiklatGapExport($data['exportRows']), $filename);
    }

    private function normalizeYear(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return preg_match('/^\d{4}$/', $value) ? $value : null;
    }

    private function resolvePegawaiId(Request $request, $user): ?int
    {
        if ($user->role === 'pegawai') {
            return Pegawai::query()->where('user_id', $user->id)->value('id');
        }

        if ($user->role === 'admin') {
            if (! $request->filled('pegawai_id')) {
                return null;
            }

            return Pegawai::query()
                ->whereKey($request->input('pegawai_id'))
                ->where('unit_kerja_id', $user->unit_kerja_id)
                ->value('id');
        }

        return $request->filled('pegawai_id') ? (int) $request->input('pegawai_id') : null;
    }

    private function resolveUnitKerjaId(Request $request, $user, ?int $selectedPegawaiId): ?int
    {
        if ($user->role === 'pegawai') {
            if ($selectedPegawaiId) {
                return Pegawai::query()->whereKey($selectedPegawaiId)->value('unit_kerja_id');
            }

            return Pegawai::query()->where('user_id', $user->id)->value('unit_kerja_id');
        }

        if ($user->role === 'admin') {
            return $user->unit_kerja_id ? (int) $user->unit_kerja_id : null;
        }

        if ($selectedPegawaiId) {
            return Pegawai::query()->whereKey($selectedPegawaiId)->value('unit_kerja_id');
        }

        if ($request->filled('unit_kerja_id')) {
            return (int) $request->input('unit_kerja_id');
        }

        return null;
    }

    private function buildUnitKerjaOptions($user, ?int $selectedUnitKerjaId)
    {
        if ($user->role === 'pegawai') {
            return collect();
        }

        $query = UnitKerja::query()->orderBy('nama_unit');

        if ($user->role === 'admin' && $user->unit_kerja_id) {
            $query->whereKey($user->unit_kerja_id);
        }

        $options = $query->get();

        if ($selectedUnitKerjaId && $options->doesntContain('id', $selectedUnitKerjaId)) {
            return $options;
        }

        return $options;
    }

    private function buildPegawaiOptions(DiklatScopeService $scopeService, $user, ?int $selectedUnitKerjaId)
    {
        if ($user->role === 'pegawai') {
            return Pegawai::query()
                ->with('unit_kerja')
                ->where('user_id', $user->id)
                ->orderBy('nama')
                ->get();
        }

        $query = $scopeService->scopePegawaiQuery(Pegawai::query()->with('unit_kerja'), $user)
            ->orderBy('nama');

        if ($selectedUnitKerjaId) {
            $query->where('unit_kerja_id', $selectedUnitKerjaId);
        }

        return $query->get();
    }

    private function buildUnitAggregateData(Request $request, DiklatScopeService $scopeService, DiklatGapAnalyticsService $analyticsService): array
    {
        $user = $request->user();

        $selectedTahunRencana = $this->normalizeYear($request->input('tahun_rencana'));
        $selectedTahunRealisasi = $this->normalizeYear($request->input('tahun_realisasi')) ?? $selectedTahunRencana;
        $selectedUnitKerjaId = $this->resolveUnitKerjaId($request, $user, null);

        $unitKerjaOptions = $this->buildUnitKerjaOptions($user, $selectedUnitKerjaId);
        $selectedUnitKerja = $selectedUnitKerjaId
            ? $unitKerjaOptions->firstWhere('id', $selectedUnitKerjaId)
            : null;

        $reportReady = $selectedTahunRencana !== null;
        $summary = $this->zeroSummary();
        $exportRows = collect();

        if ($reportReady) {
            $pegawaiQuery = $scopeService->scopePegawaiQuery(Pegawai::query()->with('unit_kerja'), $user);

            if ($selectedUnitKerjaId) {
                $pegawaiQuery->where('unit_kerja_id', $selectedUnitKerjaId);
            }

            $pegawaiIds = $pegawaiQuery->pluck('id')->all();

            $rencanaDiklat = $scopeService->scopeRencanaDiklatQuery(
                RencanaDiklat::with(['pegawai.unit_kerja', 'diklat.pegawai.unit_kerja']),
                $user
            )
                ->where('tahun_rencana', $selectedTahunRencana)
                ->whereIn('pegawai_id', $pegawaiIds)
                ->orderBy('pegawai_id')
                ->orderBy('nama_diklat_rencana')
                ->get();

            $diklat = $scopeService->scopeDiklatQuery(
                Diklat::with(['pegawai.unit_kerja', 'rencanaDiklat.pegawai.unit_kerja']),
                $user
            )
                ->where('tahun', $selectedTahunRealisasi)
                ->whereIn('pegawai_id', $pegawaiIds)
                ->orderBy('pegawai_id')
                ->orderBy('nama_diklat')
                ->get();

            $summary = $analyticsService->summary($rencanaDiklat, $diklat);
            $exportRows = $this->buildUnitAggregateRows($rencanaDiklat, $diklat);
        }

        return [
            'reportReady' => $reportReady,
            'selectedTahunRencana' => $selectedTahunRencana,
            'selectedTahunRealisasi' => $selectedTahunRealisasi,
            'selectedUnitKerjaId' => $selectedUnitKerjaId,
            'selectedUnitKerja' => $selectedUnitKerja,
            'unitKerjaOptions' => $unitKerjaOptions,
            'summary' => $summary,
            'exportRows' => $exportRows,
            'filterQuery' => array_filter([
                'tahun_rencana' => $selectedTahunRencana,
                'tahun_realisasi' => $selectedTahunRealisasi,
                'unit_kerja_id' => $selectedUnitKerjaId,
            ], static fn ($value): bool => $value !== null && $value !== ''),
        ];
    }

    private function buildUnitAggregateRows(Collection $rencanaDiklat, Collection $diklat): Collection
    {
        $activeRows = $this->activePlanRows($rencanaDiklat)->map(function (RencanaDiklat $rencana): array {
            $diklat = $rencana->diklat;
            $targetJam = (int) $rencana->target_jam;
            $realisasiJam = (int) ($diklat?->jumlah_jam ?? 0);
            $bucketStatus = $rencana->status === 'realized'
                ? ((string) ($diklat?->tahun ?? '') !== (string) $rencana->tahun_rencana ? 'cross_year_realized' : 'realized')
                : 'planned';

            return [
                'pegawai' => $rencana->pegawai?->nama ?? '-',
                'unit' => $rencana->pegawai?->unit_kerja?->nama_unit ?? '-',
                'tahun_rencana' => $rencana->tahun_rencana,
                'tahun_realisasi' => $diklat?->tahun ?? '-',
                'nama_rencana' => $rencana->nama_diklat_rencana,
                'nama_realisasi' => $diklat?->nama_diklat ?? '-',
                'bucket_status' => $bucketStatus,
                'bucket_label' => $this->bucketLabel($bucketStatus),
                'target_jam' => $targetJam,
                'realisasi_jam' => $realisasiJam,
                'gap_jam' => max(0, $targetJam - $realisasiJam),
            ];
        });

        $outOfPlanRows = $diklat
            ->filter(function (Diklat $diklatItem): bool {
                return $diklatItem->rencana_diklat_id === null;
            })
            ->values()
            ->map(function (Diklat $diklatItem): array {
                return [
                    'pegawai' => $diklatItem->pegawai?->nama ?? '-',
                    'unit' => $diklatItem->pegawai?->unit_kerja?->nama_unit ?? '-',
                    'tahun_rencana' => '-',
                    'tahun_realisasi' => $diklatItem->tahun,
                    'nama_rencana' => '-',
                    'nama_realisasi' => $diklatItem->nama_diklat,
                    'bucket_status' => 'out_of_plan',
                    'bucket_label' => 'Out of Plan',
                    'target_jam' => 0,
                    'realisasi_jam' => (int) $diklatItem->jumlah_jam,
                    'gap_jam' => 0,
                ];
            });

        return $activeRows->concat($outOfPlanRows)->values();
    }

    private function bucketLabel(string $bucketStatus): string
    {
        return match ($bucketStatus) {
            'planned' => 'Planned',
            'realized' => 'Realized Linked',
            'cross_year_realized' => 'Cross Year Realized',
            'out_of_plan' => 'Out of Plan',
            default => ucwords(str_replace('_', ' ', $bucketStatus)),
        };
    }

    private function mapPlannedRows($rencanaDiklat)
    {
        return $this->activePlanRows($rencanaDiklat)->map(function (RencanaDiklat $rencana): array {
            return [
                'pegawai' => $rencana->pegawai?->nama ?? '-',
                'unit_kerja' => $rencana->pegawai?->unit_kerja?->nama_unit ?? '-',
                'tahun_rencana' => $rencana->tahun_rencana,
                'nama_rencana' => $rencana->nama_diklat_rencana,
                'status' => $rencana->status,
                'target_jam' => (int) $rencana->target_jam,
                'realisasi' => $rencana->diklat
                    ? $rencana->diklat->nama_diklat . ' (' . $rencana->diklat->tahun . ')'
                    : '-',
                'linked' => $rencana->diklat !== null,
            ];
        });
    }

    private function mapRealizedRows($rencanaDiklat)
    {
        return $this->activePlanRows($rencanaDiklat)
            ->filter(function (RencanaDiklat $rencana): bool {
                return $rencana->status === 'realized' && $rencana->diklat !== null;
            })
            ->values()
            ->map(function (RencanaDiklat $rencana): array {
                $diklat = $rencana->diklat;

                return [
                    'pegawai' => $rencana->pegawai?->nama ?? '-',
                    'unit_kerja' => $rencana->pegawai?->unit_kerja?->nama_unit ?? '-',
                    'tahun_rencana' => $rencana->tahun_rencana,
                    'tahun_realisasi' => $diklat?->tahun ?? '-',
                    'nama_rencana' => $rencana->nama_diklat_rencana,
                    'nama_realisasi' => $diklat?->nama_diklat ?? '-',
                    'target_jam' => (int) $rencana->target_jam,
                    'jumlah_jam' => (int) ($diklat?->jumlah_jam ?? 0),
                    'gap_jam' => max(0, (int) $rencana->target_jam - (int) ($diklat?->jumlah_jam ?? 0)),
                    'cross_year' => $diklat && (string) $diklat->tahun !== (string) $rencana->tahun_rencana,
                ];
            });
    }

    private function mapNotRealizedRows($rencanaDiklat)
    {
        return $this->activePlanRows($rencanaDiklat)
            ->filter(function (RencanaDiklat $rencana): bool {
                return $rencana->status !== 'realized';
            })
            ->values()
            ->map(function (RencanaDiklat $rencana): array {
                return [
                    'pegawai' => $rencana->pegawai?->nama ?? '-',
                    'unit_kerja' => $rencana->pegawai?->unit_kerja?->nama_unit ?? '-',
                    'tahun_rencana' => $rencana->tahun_rencana,
                    'nama_rencana' => $rencana->nama_diklat_rencana,
                    'status' => $rencana->status,
                    'target_jam' => (int) $rencana->target_jam,
                ];
            });
    }

    private function mapOutOfPlanRows($diklat)
    {
        return $diklat
            ->filter(function (Diklat $diklatItem): bool {
                return $diklatItem->rencana_diklat_id === null;
            })
            ->values()
            ->map(function (Diklat $diklatItem): array {
                return [
                    'pegawai' => $diklatItem->pegawai?->nama ?? '-',
                    'unit_kerja' => $diklatItem->pegawai?->unit_kerja?->nama_unit ?? '-',
                    'tahun_realisasi' => $diklatItem->tahun,
                    'nama_realisasi' => $diklatItem->nama_diklat,
                    'jumlah_jam' => (int) $diklatItem->jumlah_jam,
                    'penyelenggara' => $diklatItem->penyelenggara,
                ];
            });
    }

    private function mapCrossYearRealizedRows($rencanaDiklat)
    {
        return $this->activePlanRows($rencanaDiklat)
            ->filter(function (RencanaDiklat $rencana): bool {
                return $rencana->status === 'realized'
                    && $rencana->diklat !== null
                    && (string) $rencana->diklat->tahun !== (string) $rencana->tahun_rencana;
            })
            ->values()
            ->map(function (RencanaDiklat $rencana): array {
                $diklat = $rencana->diklat;

                return [
                    'pegawai' => $rencana->pegawai?->nama ?? '-',
                    'unit_kerja' => $rencana->pegawai?->unit_kerja?->nama_unit ?? '-',
                    'tahun_rencana' => $rencana->tahun_rencana,
                    'tahun_realisasi' => $diklat?->tahun ?? '-',
                    'nama_rencana' => $rencana->nama_diklat_rencana,
                    'nama_realisasi' => $diklat?->nama_diklat ?? '-',
                    'target_jam' => (int) $rencana->target_jam,
                    'jumlah_jam' => (int) ($diklat?->jumlah_jam ?? 0),
                    'gap_jam' => max(0, (int) $rencana->target_jam - (int) ($diklat?->jumlah_jam ?? 0)),
                ];
            });
    }

    private function activePlanRows($rencanaDiklat)
    {
        return $rencanaDiklat
            ->filter(function (RencanaDiklat $rencana): bool {
                return in_array($rencana->status, RencanaDiklat::ACTIVE_STATUSES, true);
            })
            ->values();
    }

    private function zeroSummary(): array
    {
        return [
            'planned_count' => 0,
            'realized_linked_count' => 0,
            'not_realized_count' => 0,
            'out_of_plan_count' => 0,
            'cross_year_realized_count' => 0,
            'planned_hours' => 0,
            'realized_linked_hours' => 0,
            'hour_gap' => 0,
        ];
    }
}
