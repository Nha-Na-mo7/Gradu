<?php

namespace App\Console\Commands;

use App\Http\Controllers\TwitterAccountListController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoFollow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:autofollow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'アカウント一覧ページ/自動フォロー用のバッチファイルです。';

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
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
      Log::debug('Console/Commands AutoFollow 定刻の自動フォロー開始');
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
      // TwitterAccountListControllerのインスタンスを取得し、自動フォローメソッドを起動する
      $twitterAccountList = new TwitterAccountListController();
      
      // 自動フォロー
      $twitterAccountList->auto_follow();
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
      Log::debug('定刻の自動フォロー処理を終了します。');
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
    }
}
