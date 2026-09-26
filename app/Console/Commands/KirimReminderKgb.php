<?php

namespace App\Console\Commands;

use App\Models\KGB;
use App\Models\Pegawai;
use App\Models\User;
use App\Notifications\KgbJatuhTempoNotification;
use Illuminate\Console\Command;

/**
 * Reminder Kenaikan Gaji Berkala (KGB) yang jatuh tempo dalam 60 hari ke depan.
 *
 * Notifikasi dikirim ke:
 * - Admin unit kerja terkait (untuk memproses KGB pegawainya)
 * - Superadmin (pantauan global)
 */
class KirimReminderKgb extends Command
{
    /**
     * Jumlah hari ke depan yang dipantau.
     */
    public const HARI_MONITOR = 60;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kgb:reminder {--hari= : Jumlah hari ke depan yang dipantau (default 60)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi KGB yang jatuh tempo (tmt_kgb) dalam N hari ke depan ke admin & superadmin';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $hari = (int) ($this->option('hari') ?: self::HARI_MONITOR);
        $batas = now()->addDays($hari)->toDateString();

        $kgbList = Kgb::with('pegawai')
            ->whereNotNull('tmt_kgb')
            ->whereDate('tmt_kgb', '>=', now()->toDateString())
            ->whereDate('tmt_kgb', '<=', $batas)
            ->get();

        if ($kgbList->isEmpty()) {
            $this->info("Tidak ada KGB yang jatuh tempo dalam {$hari} hari ke depan.");

            return self::SUCCESS;
        }

        // Penerima notifikasi: superadmin + admin tiap unit kerja yang terdampak
        $superadmin = User::where('role', 'superadmin')->get();
        $adminUnit = User::whereIn('role', ['admin'])
            ->whereIn('unit_kerja_id', $kgbList->pluck('pegawai.unit_kerja_id')->filter()->unique())
            ->get();

        $penerima = $superadmin->merge($adminUnit)->unique('id');

        foreach ($kgbList as $kgb) {
            foreach ($penerima as $user) {
                // Admin hanya menerima notifikasi KGB pegawai di unitnya
                if ($user->role === 'admin'
                    && $user->unit_kerja_id !== $kgb->pegawai?->unit_kerja_id) {
                    continue;
                }

                $user->notify(new KgbJatuhTempoNotification($kgb, $hari));
            }
        }

        $this->info("{$kgbList->count()} KGB jatuh tempo dalam {$hari} hari — notifikasi terkirim ke {$penerima->count()} pengguna.");

        return self::SUCCESS;
    }
}
