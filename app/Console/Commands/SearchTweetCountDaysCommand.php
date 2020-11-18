<?php

namespace App\Console\Commands;

use App\Http\Controllers\TwitterController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SearchTweetCountDaysCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:count_day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '1日以内で指定の通貨のツイート数をカウントするコマンドです';

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
      Log::debug('Console/Commands SearchTweetCountDays ツイート数取得(1日)');
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
      // TwitterControllerのインスタンスを取得し、ツイート数取得メソッドを起動する
      $twitterController = new TwitterController();
  
      // 日ごとのツイート数カウント開始
      $twitterController->start_tweet_count_days();
      
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
      Log::debug('定刻のツイート数取得(1日)を終了します。');
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
    }
}
