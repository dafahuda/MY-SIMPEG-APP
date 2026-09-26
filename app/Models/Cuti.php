<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    /** @use HasFactory<\Database\Factories\CutiFactory> */
    protected $table = 'tb_cuti';
    protected $primaryKey = 'id';
    protected $fillable = [
        'pegawai_id',
        'jenis_cuti',
        'no_surat_cuti',
        'tgl_surat_cuti',
        'pelaksanaan_cuti_mulai',
        'pelaksanaan_cuti_selesai',
        'durasi_cuti',
        'ketentuan_a',
        'ketentuan_b',
        'ketentuan_c',
        'file_surat_cuti',
        'tebusan',
        'status',
        'alasan_penolakan',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'pelaksanaan_cuti_mulai' => 'date',
        'pelaksanaan_cuti_selesai' => 'date',
        'tgl_surat_cuti' => 'date',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /** Scope: hanya cuti menunggu persetujuan */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /** Scope: hanya cuti yang sudah disetujui */
    public function scopeDisetujui($query)
    {
        return $query->where('status', 'disetujui');
    }

    use HasFactory;
}
