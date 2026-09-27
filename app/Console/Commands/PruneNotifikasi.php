<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneNotifikasi extends Command
{
    protected $signature = 'notifikasi:prune
                            {--days=90 : Notifikasi lebih tua dari jumlah hari ini dihapus}';

    protected $description = 'Hapus notifikasi lama (default 90 hari) agar tabel notifications tidak menumpuk';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $batas = now()->subDays($days);

        $dihapus = DB::table('notifications')
            ->where('created_at', '<', $batas)
            ->delete();

        $this->info("Notifikasi lebih tua dari {$days} hari dihapus: {$dihapus} baris.");

        return self::SUCCESS;
    }
}
