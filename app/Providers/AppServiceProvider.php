<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        define('MENGGUNAKAN_DANA',"1");
        define('MEMBAYAR_WAKAF',"2");
        define('LUNAS_WAKAF',"3");
        define('MENUNGGU_PENGAJUAN',"4");
        define('PENGAJUAN_DITERIMA',"5");
        define('PENGAJUAN_DITOLAK',"6");
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
