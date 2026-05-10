<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class RencanaDiklat extends Model
{
    /** @use HasFactory<\Database\Factories\RencanaDiklatFactory> */
    protected $table = 'tb_rencana_diklat';
    protected $primaryKey = 'id';

    public const ACTIVE_STATUSES = ['planned', 'realized'];

    protected $fillable = [
        'pegawai_id',
        'tahun_rencana',
        'nama_diklat_rencana',
        'target_kompetensi',
        'kategori_diklat',
        'prioritas',
        'target_jam',
        'target_penyelenggara',
        'alasan_kebutuhan',
        'catatan',
        'status',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $rencanaDiklat): void {
            if (! in_array($rencanaDiklat->status, self::ACTIVE_STATUSES, true)) {
                return;
            }

            $exists = static::query()
                ->where('pegawai_id', $rencanaDiklat->pegawai_id)
                ->where('tahun_rencana', $rencanaDiklat->tahun_rencana)
                ->where('nama_diklat_rencana', $rencanaDiklat->nama_diklat_rencana)
                ->whereIn('status', self::ACTIVE_STATUSES)
                ->when($rencanaDiklat->exists, function ($query) use ($rencanaDiklat): void {
                    $query->where('id', '!=', $rencanaDiklat->id);
                })
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'nama_diklat_rencana' => 'Rencana diklat aktif untuk pegawai, tahun, dan nama yang sama sudah ada.',
                ]);
            }
        });
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function diklat()
    {
        return $this->hasOne(Diklat::class, 'rencana_diklat_id');
    }

    use HasFactory;
}
