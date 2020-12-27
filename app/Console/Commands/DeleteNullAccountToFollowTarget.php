<?php

namespace App\Console\Commands;

use App\Http\Controllers\TwitterAccountListController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteNullAccountToFollowTarget extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'アカウントIDがNULLとなっているレコードを削除します';

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
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
      Log::debug('Console/Commands DeleteNullAccountToFollowTarget アカウントIDがNULLのレコードを削除');
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
      // TrendTweetControllerのインスタンスを取得
      $trendTweetController = new TwitterAccountListController();
  
      // follow_targetsテーブル内、account_idがNULLのレコードを全て削除する
      $trendTweetController->delete_nullaccount_follow();
  
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
      Log::debug('定刻のアカウントIDがNULLのレコード削除を終了します。');
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
    }
}
