<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenPegawai extends Model
{
    /** @use HasFactory<\Database\Factories\DokumenPegawaiFactory> */
    use HasFactory;

    protected $table = 'tb_dokumen_pegawai';

    protected $fillable = [
        'pegawai_id',
        'jenis_dokumen',
        'nama_dokumen',
        'file_path',
        'file_name',
        'mime_type',
        'size',
        'uploaded_by',
        'keterangan',
    ];

    public const JENIS_DOKUMEN = [
        'sk_cpns' => 'SK CPNS',
        'sk_pns' => 'SK PNS',
        'sk_pangkat' => 'SK Pangkat',
        'ktp' => 'KTP',
        'kk' => 'Kartu Keluarga',
        'ijazah' => 'Ijazah',
        'foto' => 'Foto Pegawai',
        'lainnya' => 'Lainnya',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getJenisLabelAttribute(): string
    {
        return self::JENIS_DOKUMEN[$this->jenis_dokumen] ?? ucfirst($this->jenis_dokumen);
    }

    public function getUkuranLabelAttribute(): string
    {
        if ($this->size >= 1048576) {
            return round($this->size / 1048576, 1) . ' MB';
        }

        return round($this->size / 1024) . ' KB';
    }
}
