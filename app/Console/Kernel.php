<?php

namespace App\Console;

use App\Console\Commands\AutoFollow;
use App\Console\Commands\BatchTest;

use App\Console\Commands\SearchAccountsCommand;
use App\Console\Commands\SearchTweetCountDaysCommand;
use App\Console\Commands\SearchTweetCountHoursCommand;
use App\Console\Commands\SearchTweetCountWeeksCommand;
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
      SearchAccountsCommand::class,
      SearchTweetCountDaysCommand::class,
      SearchTweetCountHoursCommand::class,
      SearchTweetCountWeeksCommand::class
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
      // APIのフォロー制限にかからないようにフォローインターバルを調整(30分にフォローは5人まで)
      // 30分ごとに起動する。
      $schedule->command('command:autofollow')
          ->everyThirtyMinutes()
          ->withoutOverlapping();
      
      
      // ======================
      // 各通貨ごとのツイート数集計
      // ======================
      // 1時間以内のツイート数(15分ごとに実施、多くても2回で集計完了する見込み)
      $schedule->command('command:count_hour')
          ->everyFifteenMinutes()
          ->withoutOverlapping();
      
      // 1日以内のツイート数(最初に7日分まとめてカウントするので、こちらも1時間ごとに実行する)
      // API制限対策で、1時間ごとの集計が終了しているであろう毎時45分に集計開始
      $schedule->command('command:count_day')
          ->hourlyAt(45)
          ->withoutOverlapping();
      
      // 1週間以内のツイート数(7日分を合算するメソッド)
      // 稼働初日は7日分を集計し終わるであろう22時に、以降は1日の集計が終わるであろうAM3時に集計する
      $schedule->command('command:count_week')
          ->twiceDaily(3,22)
          ->withoutOverlapping();
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
