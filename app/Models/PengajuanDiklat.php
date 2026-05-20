<?php

namespace App\Models;

use App\Support\DiklatGlossary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class PengajuanDiklat extends Model
{
    /** @use HasFactory<\Illuminate\Database\Eloquent\Factories\Factory> */
    protected $table = 'tb_pengajuan_diklat';
    protected $primaryKey = 'id';

    public const STATUS_PENDING = 'pending';
    public const STATUS_REVISION_REQUESTED = 'revision_requested';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_CANCELLED = 'cancelled';

    public const ACTIVE_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_REVISION_REQUESTED,
        self::STATUS_APPROVED,
    ];

    protected $fillable = [
        'rencana_diklat_id',
        'pegawai_id',
        'diklat_id',
        'status',
        'file_bukti',
        'nomor_sertifikat',
        'tanggal_sertifikat',
        'jumlah_jam_realisasi',
        'catatan_pegawai',
        'catatan_verifikator',
        'verified_by',
        'verified_at',
        'submitted_at',
        'revision_count',
    ];

    protected $casts = [
        'tanggal_sertifikat' => 'date',
        'jumlah_jam_realisasi' => 'integer',
        'verified_at' => 'datetime',
        'submitted_at' => 'datetime',
        'revision_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $pengajuanDiklat): void {
            $rencanaDiklat = RencanaDiklat::query()->find($pengajuanDiklat->rencana_diklat_id);

            if (! $rencanaDiklat) {
                return;
            }

            if ((int) $pengajuanDiklat->pegawai_id !== (int) $rencanaDiklat->pegawai_id) {
                throw ValidationException::withMessages([
                    'rencana_diklat_id' => 'Pengajuan diklat hanya bisa terhubung ke rencana diklat milik pegawai yang sama.',
                ]);
            }

            if (! in_array($pengajuanDiklat->status, self::ACTIVE_STATUSES, true)) {
                return;
            }

            $exists = static::query()
                ->where('rencana_diklat_id', $pengajuanDiklat->rencana_diklat_id)
                ->where('pegawai_id', $pengajuanDiklat->pegawai_id)
                ->whereIn('status', self::ACTIVE_STATUSES)
                ->when($pengajuanDiklat->exists, function ($query) use ($pengajuanDiklat): void {
                    $query->where('id', '!=', $pengajuanDiklat->id);
                })
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'rencana_diklat_id' => 'Pengajuan diklat aktif untuk rencana dan pegawai yang sama sudah ada.',
                ]);
            }
        });
    }

    public function rencanaDiklat()
    {
        return $this->belongsTo(RencanaDiklat::class, 'rencana_diklat_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function diklat()
    {
        return $this->belongsTo(Diklat::class, 'diklat_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function needsRevision(): bool
    {
        return $this->status === self::STATUS_REVISION_REQUESTED;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function statusLabel(): string
    {
        return DiklatGlossary::submissionStatusLabel($this->status);
    }

    use HasFactory;
}
