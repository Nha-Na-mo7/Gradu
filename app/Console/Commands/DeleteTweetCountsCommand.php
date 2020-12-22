<?php

namespace App\Console\Commands;

use App\Http\Controllers\TrendTweetController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteTweetCountsCommand extends Command
{
    // N日を経過したツイート数データを全て削除する
    const DELETE_DAYS = 14;
  
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:delete_tweet_counts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DB容量削減のため、一定期間を過ぎたツイート数データを削除するコマンドです。';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
      Log::debug('Console/Commands DeleteTweetCounts 古くなったデータの削除');
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
      // TrendTweetControllerのインスタンスを取得
      $trendTweetController = new TrendTweetController();
  
      // 1日1回、14日以上前の古くなったレコードを削除する
      $trendTweetController->delete_tweet_count(self::DELETE_DAYS);
  
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
      Log::debug('定刻のツイート数レコードの削除を終了します。');
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
    }
}
