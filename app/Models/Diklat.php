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


    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function rencanaDiklat()
    {
        return $this->belongsTo(RencanaDiklat::class, 'rencana_diklat_id');
    }

    protected static function booted(): void
    {
        static::saving(function (self $diklat): void {
            if ($diklat->rencana_diklat_id === null) {
                return;
            }

            $rencanaDiklat = RencanaDiklat::query()->find($diklat->rencana_diklat_id);

            if (! $rencanaDiklat) {
                return;
            }

            if ($rencanaDiklat->status === 'cancelled') {
                throw ValidationException::withMessages([
                    'rencana_diklat_id' => 'Rencana diklat berstatus cancelled tidak bisa dipakai.',
                ]);
            }

            if ((int) $diklat->pegawai_id !== (int) $rencanaDiklat->pegawai_id) {
                throw ValidationException::withMessages([
                    'rencana_diklat_id' => 'Diklat hanya bisa terhubung ke rencana diklat milik pegawai yang sama.',
                ]);
            }

            $tahunDiklat = (int) $diklat->tahun;
            $tahunRencana = (int) $rencanaDiklat->tahun_rencana;

            if ($tahunDiklat !== $tahunRencana && $tahunDiklat !== ($tahunRencana + 1)) {
                throw ValidationException::withMessages([
                    'tahun' => 'Tahun realisasi hanya boleh sama dengan tahun rencana atau satu tahun setelahnya.',
                ]);
            }

            $existingLink = static::query()
                ->where('rencana_diklat_id', $diklat->rencana_diklat_id)
                ->when($diklat->exists, function ($query) use ($diklat): void {
                    $query->where('id', '!=', $diklat->id);
                })
                ->exists();

            if ($existingLink) {
                throw ValidationException::withMessages([
                    'rencana_diklat_id' => 'Satu rencana diklat hanya boleh memiliki satu realisasi.',
                ]);
            }
        });
    }

    use HasFactory;
}
