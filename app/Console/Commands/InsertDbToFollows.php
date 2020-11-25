<?php

namespace App\Console\Commands;

use App\Http\Controllers\TwitterAccountListController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class InsertDbToFollows extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:insert_db_to_follows';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '
    定期的にTwitterアカウントを持つユーザーのフォローリストを取得し、followsテーブルに登録するコマンドです。
    登録対象は、accountsテーブル(Twitterアカウント検索で取得したもの)に登録されているIDに限られます。
    ';

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
      Log::debug('Console/Commands batch_follow_db_insert ');
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
      // TwitterAccountListControllerのインスタンスを取得し、フォローリストをDB登録するメソッドを起動する
      $twitterAccountList = new TwitterAccountListController();
  
      // 仮想通貨アカウント取得
      $twitterAccountList->batch_follow_db_insert();
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
      Log::debug('定刻のフォローリスト取得 → DB登録処理を終了します。');
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
    }
}
