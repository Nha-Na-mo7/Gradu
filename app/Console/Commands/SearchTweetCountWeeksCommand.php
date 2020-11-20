<?php

namespace App\Console\Commands;

use App\Http\Controllers\TrendTweetController;
use App\Http\Controllers\TwitterController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SearchTweetCountWeeksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:count_week';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '7日間以内で指定の通貨のツイート数をカウントするコマンドです';

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
      Log::debug('Console/Commands SearchTweetCountWeeks ツイート数取得(1週間)');
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
      // TrendTweetControllerのインスタンスを取得し、ツイート数取得メソッドを起動する
      $trendTweetController = new TrendTweetController();
      
      $trendTweetController->make_tweet_count_week();
  
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
      Log::debug('定刻のツイート数取得(1週間)を終了します。');
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
    }
}
