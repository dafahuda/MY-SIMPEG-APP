<?php

namespace App\Support;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Central authorization and data-scoping facade for SIMPEG.
 *
 * Role tiers (unchanged semantics):
 * - pegawai    -> sees/acts only on their own Pegawai-linked records
 * - admin      -> sees/acts only within their own unit_kerja_id
 * - superadmin -> unrestricted
 *
 * Controllers call these helpers instead of hand-rolling role branches,
 * so every scoping decision lives in one reviewable place.
 */
final class Access
{
    public static function user(): User
    {
        return Auth::user();
    }

    public static function role(): string
    {
        return Auth::user()->role;
    }

    public static function isAdmin(): bool
    {
        return static::role() === 'admin';
    }

    public static function isPegawai(): bool
    {
        return static::role() === 'pegawai';
    }

    public static function isSuperadmin(): bool
    {
        return static::role() === 'superadmin';
    }

    public static function unitKerjaId(): ?int
    {
        return Auth::user()->unit_kerja_id;
    }

    /** The Pegawai row linked to the current user, or null. */
    public static function myPegawai(): ?Pegawai
    {
        return Pegawai::where('user_id', Auth::id())->first();
    }

    /** Restrict an admin-scoped query to pegawai inside the admin's unit. */
    public static function scopeToUnit(Builder $query): Builder
    {
        return $query->whereHas('pegawai', function (Builder $q) {
            $q->where('unit_kerja_id', static::unitKerjaId());
        });
    }

    /** Restrict a related-record query to the current user's own pegawai rows. */
    public static function scopeToSelf(Builder $query): Builder
    {
        $pegawai = static::myPegawai();

        return $pegawai
            ? $query->where('pegawai_id', $pegawai->id)
            : $query->whereRaw('1=0');
    }

    /**
     * Role-aware Pegawai option list for form selects:
     * admin -> own unit, pegawai -> own row, superadmin -> everyone (ordered by name).
     */
    public static function pegawaiOptions(): Collection
    {
        if (static::isAdmin()) {
            return Pegawai::where('unit_kerja_id', static::unitKerjaId())->orderBy('nama')->get();
        }

        if (static::isPegawai()) {
            return Pegawai::where('user_id', Auth::id())->orderBy('nama')->get();
        }

        return Pegawai::orderBy('nama')->get();
    }

    /**
     * Ownership guard for pegawai-role mutations: only enforced for the
     * pegawai role; admin and superadmin pass through unchanged.
     */
    public static function ensureOwnRecord(object $record): void
    {
        if (! static::isPegawai()) {
            return;
        }

        $pegawai = static::myPegawai();

        abort_unless(
            $pegawai !== null && (int) $record->pegawai_id === (int) $pegawai->id,
            403,
            'Akses ditolak'
        );
    }

    /**
     * Unit guard for admin mutations: admins may only act on pegawai inside
     * their own unit; pegawai (own record, already guarded) and superadmin pass.
     */
    public static function ensureInUnit(?Pegawai $pegawai): void
    {
        if (! static::isAdmin()) {
            return;
        }

        abort_unless(
            $pegawai !== null && $pegawai->unit_kerja_id === static::unitKerjaId(),
            403,
            'Akses ditolak'
        );
    }

    /** Abort 403 unless the current role is one of the given roles. */
    public static function abortUnlessRole(string ...$roles): void
    {
        abort_unless(in_array(static::role(), $roles, true), 403, 'Akses ditolak');
    }

    public static function abortUnlessAdminOrSuperadmin(): void
    {
        static::abortUnlessRole('admin', 'superadmin');
    }
}
