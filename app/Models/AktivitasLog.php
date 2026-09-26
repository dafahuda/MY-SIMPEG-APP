<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AktivitasLog extends Model
{
    protected $table = 'tb_aktivitas_log';

    protected $fillable = [
        'user_id',
        'username_snapshot',
        'aksi',
        'modul',
        'deskripsi',
        'ip_address',
        'user_agent',
        'data_lama',
        'data_baru',
    ];

    protected $casts = [
        'data_lama' => 'array',
        'data_baru' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Catat aktivitas ke tabel log (helper statis agar mudah dipanggil dari controller).
     */
    public static function catat(
        ?User $user,
        string $aksi,
        string $modul,
        string $deskripsi,
        ?array $dataLama = null,
        ?array $dataBaru = null,
    ): self {
        return static::create([
            'user_id' => $user?->id,
            'username_snapshot' => $user?->username,
            'aksi' => $aksi,
            'modul' => $modul,
            'deskripsi' => mb_substr($deskripsi, 0, 500),
            'ip_address' => request()?->ip(),
            'user_agent' => mb_substr((string) request()?->userAgent(), 0, 500),
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru,
        ]);
    }
}
