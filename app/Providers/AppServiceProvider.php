<?php

namespace App\Providers;

use App\Observers\AktivitasLogObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        // Audit trail: catat aktivitas buat/ubah/hapus model kepegawaian penting
        AktivitasLogObserver::observe([
            \App\Models\Pegawai::class,
            \App\Models\Cuti::class,
            \App\Models\KGB::class,
            \App\Models\Diklat::class,
            \App\Models\RencanaDiklat::class,
            \App\Models\Mutasi::class,
            \App\Models\Tpp::class,
        ]);
    }
}
