<?php

namespace App\Console;

use App\Console\Commands\AutoFollow;
use App\Console\Commands\BatchTest;

use App\Console\Commands\SearchAccountsCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
      AutoFollow::class,
      SearchAccountsCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
      // ここに記述することで指定のスケジュールでタスクが実行される
      // $schedule->command('batchtest')->everyMinute()->hourly();
  
      // ======================
      // 仮想通貨アカウント取得
      // ======================
      // 1日に1回取得する。
      // Twitterユーザーは深夜帯に活動するイメージ → 明け方の取得が一番新規ユーザーの取得が期待できるか
      $schedule->command('command:searchaccounts')
          ->dailyAt('5:20')
          ->withoutOverlapping();
      
      
      // =====================
      // 自動フォロー
      // =====================
      // APIのフォロー制限にかからないようにフォローインターバルを調整(15分に4人までとすることで調整済み)
      // 15分ごとに起動する。
      $schedule->command('command:autofollow')
          ->everyFifteenMinutes()
          ->withoutOverlapping();
      
      
      // ======================
      // 各通貨ごとのツイート数集計
      // ======================
      // // TODO 実行時刻は暫定のもの。
      // // TODO また1回の取得件数で引っかからないようにするために細かいスパンで集計することも検討
      // // 1時間以内のツイート数
      // $schedule->command('command:1Hour')
      //     ->hourly()
      //     ->withoutOverlapping();
      //
      // // 24時間以内のツイート数
      // $schedule->command('command:24Hour')
      //     ->dailyAt('2:30')
      //     ->withoutOverlapping();
      //
      // // 7日以内のツイート数
      // $schedule->command('command:7days')
      //     ->dailyAt('1:30')
      //     ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
