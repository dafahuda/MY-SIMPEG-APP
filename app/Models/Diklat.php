<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Diklat extends Model
{
    /** @use HasFactory<\Database\Factories\DiklatFactory> */
    protected $table = 'tb_diklat';
    protected $primaryKey = 'id';
    protected $fillable = [
        'pegawai_id',
        'rencana_diklat_id',
        'original_filename',
        'nama_diklat',
        'jumlah_jam',
        'penyelenggara',
        'tempat',
        'angkatan',
        'tahun',
        'no_sttpp',
        'tgl_sttpp',
        'file_sertifikat_diklat'
    ];


    protected static function booted(): void
    {
        static::saving(function (self $diklat): void {
            if (blank($diklat->rencana_diklat_id)) {
                return;
            }

            $rencana = RencanaDiklat::query()->find($diklat->rencana_diklat_id);

            if (! $rencana || $rencana->status === 'cancelled') {
                throw ValidationException::withMessages([
                    'rencana_diklat_id' => 'Rencana diklat tidak valid atau sudah dibatalkan.',
                ]);
            }

            if ((int) $rencana->pegawai_id !== (int) $diklat->pegawai_id) {
                throw ValidationException::withMessages([
                    'rencana_diklat_id' => 'Rencana diklat harus milik pegawai yang sama.',
                ]);
            }

            $tahunRencana = (int) $rencana->tahun_rencana;
            $tahunRealisasi = (int) $diklat->tahun;

            if (! in_array($tahunRealisasi, [$tahunRencana, $tahunRencana + 1], true)) {
                throw ValidationException::withMessages([
                    'tahun' => 'Tahun realisasi harus sama dengan tahun rencana atau satu tahun setelahnya.',
                ]);
            }

            $exists = static::query()
                ->where('rencana_diklat_id', $diklat->rencana_diklat_id)
                ->when($diklat->exists, function ($query) use ($diklat): void {
                    $query->where('id', '!=', $diklat->id);
                })
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'rencana_diklat_id' => 'Rencana diklat ini sudah memiliki realisasi.',
                ]);
            }
        });
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function rencanaDiklat()
    {
        return $this->belongsTo(RencanaDiklat::class, 'rencana_diklat_id');
    }

    use HasFactory;
}
