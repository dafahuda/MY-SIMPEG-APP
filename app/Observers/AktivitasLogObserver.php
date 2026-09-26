<?php

namespace App\Observers;

use App\Models\AktivitasLog;
use Illuminate\Database\Eloquent\Model;

/**
 * Observer generik: mencatat aktivitas buat/ubah/hapus model ke tb_aktivitas_log.
 *
 * Daftarkan model yang ingin dicatat di AppServiceProvider::boot() via
 *AktivitasLogObserver::observe([ModelA::class, ModelB::class]).
 */
class AktivitasLogObserver
{
    /**
     * Nama kolom label per model (untuk deskripsi log yang manusiawi).
     */
    private const LABEL_MODEL = [
        \App\Models\Pegawai::class => 'nama',
        \App\Models\Cuti::class => 'jenis_cuti',
        \App\Models\KGB::class => 'no_kgb',
        \App\Models\Diklat::class => 'nama_diklat',
        \App\Models\RencanaDiklat::class => 'nama_diklat_rencana',
        \App\Models\Mutasi::class => 'no_sk_mutasi',
        \App\Models\Tpp::class => 'periode',
    ];

    public static function observe(array $models): void
    {
        foreach ($models as $model) {
            $model::observe(static::class);
        }
    }

    public function created(Model $model): void
    {
        $this->catat('buat', $model);
    }

    public function updated(Model $model): void
    {
        $this->catat('ubah', $model);
    }

    public function deleted(Model $model): void
    {
        $this->catat('hapus', $model);
    }

    private function catat(string $aksi, Model $model): void
    {
        try {
            $label = self::LABEL_MODEL[$model::class] ?? 'id';

            AktivitasLog::catat(
                auth()->user(),
                $aksi,
                class_basename($model),
                sprintf(
                    '%s data %s: %s',
                    ['buat' => 'Menambahkan', 'ubah' => 'Mengubah', 'hapus' => 'Menghapus'][$aksi],
                    class_basename($model),
                    $model->{$label} ?? $model->getKey(),
                ),
                $aksi === 'ubah' || $aksi === 'hapus'
                    ? collect($model->getOriginal())->only(array_keys($model->getChanges()))->all()
                    : null,
                $aksi !== 'hapus' ? $model->getChanges() ?: null : null,
            );
        } catch (\Throwable) {
            // Log aktivitas tidak boleh menggagalkan operasi utama
        }
    }
}
