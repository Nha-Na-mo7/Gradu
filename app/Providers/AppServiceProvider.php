<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 本番環境以外だった場合、SQLログを出力させる。
        // storage/logs にSQLのログファイルが表示されるようになる。
        if (config('app.env') !== 'production') {
          \DB::listen(function ($query) {
            \Log::info("Query Time:{$query->time}s] $query->sql");
          });
        }
    }
}
