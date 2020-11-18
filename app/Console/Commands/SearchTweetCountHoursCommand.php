<?php

namespace App\Console\Commands;

use App\Http\Controllers\TwitterController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SearchTweetCountHoursCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:count_hour';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '1時間以内で指定の通貨のツイート数をカウントするコマンドです';

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
      Log::debug('Console/Commands SearchTweetCountHours ツイート数取得(1時間)');
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
      // TwitterControllerのインスタンスを取得し、ツイート数取得メソッドを起動する
      $twitterController = new TwitterController();
  
      // ツイート数カウント開始(引数は$Search_type)
      $search_type = 0;
      $twitterController->count_tweets($search_type);
  
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
      Log::debug('定刻のツイート数取得(1時間)を終了します。');
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
    }
}
