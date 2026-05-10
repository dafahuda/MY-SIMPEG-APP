<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class DiklatScopeService
{
    public function scopePegawaiQuery(Builder $query, User $user): Builder
    {
        return match ($user->role) {
            'superadmin' => $query,
            'admin' => $query->where('unit_kerja_id', $user->unit_kerja_id),
            'pegawai' => $query->where('user_id', $user->id),
            default => $query->whereRaw('1 = 0'),
        };
    }

    public function scopeRencanaDiklatQuery(Builder $query, User $user): Builder
    {
        return $this->scopeRelatedToPegawai($query, $user);
    }

    public function scopeDiklatQuery(Builder $query, User $user): Builder
    {
        return $this->scopeRelatedToPegawai($query, $user);
    }

    private function scopeRelatedToPegawai(Builder $query, User $user): Builder
    {
        return match ($user->role) {
            'superadmin' => $query,
            'admin' => $query->whereHas('pegawai', function ($pegawaiQuery) use ($user): void {
                $pegawaiQuery->where('unit_kerja_id', $user->unit_kerja_id);
            }),
            'pegawai' => $query->whereHas('pegawai', function ($pegawaiQuery) use ($user): void {
                $pegawaiQuery->where('user_id', $user->id);
            }),
            default => $query->whereRaw('1 = 0'),
        };
    }
}
