<?php

namespace App\Services;

use App\Models\Diklat;
use App\Models\RencanaDiklat;
use Illuminate\Database\Eloquent\Collection;

class DiklatGapAnalyticsService
{
    public function summary(Collection $rencanaDiklat, ?Collection $diklat = null): array
    {
        $rencanaDiklat = $rencanaDiklat->loadMissing('diklat');
        $diklat = $diklat?->loadMissing('rencanaDiklat') ?? new Collection();

        $this->syncScopedLinkedDiklat($rencanaDiklat, $diklat);

        return [
            'planned_count' => $this->plannedCount($rencanaDiklat),
            'realized_linked_count' => $this->realizedLinkedCount($rencanaDiklat),
            'not_realized_count' => $this->notRealizedCount($rencanaDiklat),
            'out_of_plan_count' => $this->outOfPlanCount($diklat),
            'cross_year_realized_count' => $this->crossYearRealizedCount($rencanaDiklat),
            'planned_hours' => $this->plannedHours($rencanaDiklat),
            'realized_linked_hours' => $this->realizedLinkedHours($rencanaDiklat),
            'hour_gap' => $this->hourGap($rencanaDiklat),
        ];
    }

    public function plannedCount(Collection $rencanaDiklat): int
    {
        return $this->activePlans($rencanaDiklat)->count();
    }

    public function realizedLinkedCount(Collection $rencanaDiklat): int
    {
        return $this->realizedPlans($rencanaDiklat)->count();
    }

    public function notRealizedCount(Collection $rencanaDiklat): int
    {
        return $this->plannedCount($rencanaDiklat) - $this->realizedLinkedCount($rencanaDiklat);
    }

    public function outOfPlanCount(Collection $diklat): int
    {
        return $diklat->filter(function (Diklat $diklat): bool {
            return $diklat->rencana_diklat_id === null;
        })->count();
    }

    public function crossYearRealizedCount(Collection $rencanaDiklat): int
    {
        return $this->realizedPlans($rencanaDiklat)->filter(function (RencanaDiklat $rencana): bool {
            if (! $rencana->diklat) {
                return false;
            }

            return (int) $rencana->diklat->tahun === ((int) $rencana->tahun_rencana + 1);
        })->count();
    }

    public function plannedHours(Collection $rencanaDiklat): int
    {
        return (int) $this->activePlans($rencanaDiklat)->sum(function (RencanaDiklat $rencana): int {
            return (int) $rencana->target_jam;
        });
    }

    public function realizedLinkedHours(Collection $rencanaDiklat): int
    {
        return (int) $this->realizedPlans($rencanaDiklat)->sum(function (RencanaDiklat $rencana): int {
            return (int) ($rencana->diklat?->jumlah_jam ?? 0);
        });
    }

    public function hourGap(Collection $rencanaDiklat): int
    {
        return $this->plannedHours($rencanaDiklat) - $this->realizedLinkedHours($rencanaDiklat);
    }

    private function activePlans(Collection $rencanaDiklat): Collection
    {
        return $rencanaDiklat->filter(function (RencanaDiklat $rencana): bool {
            return in_array($rencana->status, RencanaDiklat::ACTIVE_STATUSES, true);
        })->values();
    }

    private function realizedPlans(Collection $rencanaDiklat): Collection
    {
        return $this->activePlans($rencanaDiklat)->filter(function (RencanaDiklat $rencana): bool {
            return $rencana->status === 'realized' && $rencana->diklat !== null;
        })->values();
    }

    private function syncScopedLinkedDiklat(Collection $rencanaDiklat, Collection $diklat): void
    {
        $diklatByRencanaId = $diklat
            ->filter(fn (Diklat $diklatItem): bool => $diklatItem->rencana_diklat_id !== null)
            ->keyBy('rencana_diklat_id');

        $rencanaDiklat->each(function (RencanaDiklat $rencana) use ($diklatByRencanaId): void {
            $matchedDiklat = $diklatByRencanaId->get($rencana->id);

            $rencana->setRelation('diklat', $matchedDiklat);
        });
    }
}
