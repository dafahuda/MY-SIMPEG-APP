<?php

namespace App\Support;

final class DiklatGlossary
{
    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'draft' => 'Draf',
            'planned' => 'Direncanakan',
            'realized' => 'Terealisasi',
            'cancelled' => 'Dibatalkan',
            default => self::fallback($status),
        };
    }

    public static function submissionStatusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'Menunggu Verifikasi',
            'revision_requested' => 'Perlu Revisi',
            'approved' => 'Terverifikasi',
            'cancelled' => 'Dibatalkan',
            default => self::fallback($status),
        };
    }

    public static function submissionStatusOptions(): array
    {
        return [
            'pending' => self::submissionStatusLabel('pending'),
            'revision_requested' => self::submissionStatusLabel('revision_requested'),
            'approved' => self::submissionStatusLabel('approved'),
            'cancelled' => self::submissionStatusLabel('cancelled'),
        ];
    }

    public static function linkLabel(string $state): string
    {
        return match ($state) {
            'linked' => 'Terhubung',
            'out_of_plan' => 'Di luar rencana',
            'cross_year' => 'Terhubung (Lintas tahun)',
            default => self::fallback($state),
        };
    }

    public static function reportLabel(string $key): string
    {
        return match ($key) {
            'planned' => 'Rencana aktif',
            'realized' => 'Realisasi terhubung',
            'not_realized' => 'Belum terealisasi',
            'out_of_plan' => 'Di luar rencana',
            'cross_year_realized' => 'Realisasi lintas tahun',
            'planned_hours' => 'Jam rencana',
            'realized_linked_hours' => 'Jam realisasi',
            'hour_gap' => 'Selisih jam',
            default => self::fallback($key),
        };
    }

    public static function uiLabel(string $key): string
    {
        return match ($key) {
            'report' => 'Laporan',
            'get_report' => 'Tampilkan Laporan',
            'print' => 'Cetak',
            'export_excel' => 'Ekspor Excel',
            'save' => 'Simpan',
            'save_draft' => 'Simpan Draf',
            'save_planned' => 'Simpan Rencana',
            'cancel' => 'Batal',
            'edit' => 'Ubah',
            'delete' => 'Hapus',
            'read_only' => 'Hanya baca',
            'self_scope' => 'Cakupan mandiri',
            'current_link' => 'Terhubung saat ini',
            'without_plan' => 'Tanpa rencana',
            'profile' => 'Profil',
            'profile_edit' => 'Ubah Profil',
            'no_photo' => 'Tidak ada foto',
            'linked_current' => 'Terhubung saat ini',
            default => self::fallback($key),
        };
    }

    private static function fallback(string $value): string
    {
        return ucwords(str_replace(['_', '-'], ' ', $value));
    }
}
