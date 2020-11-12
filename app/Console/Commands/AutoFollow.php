<?php

namespace App\Console\Commands;

use App\Http\Controllers\TwitterController;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoFollow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autofollow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '
    アカウント一覧ページ/自動フォロー用のバッチファイルです。
    処理の流れ
    ・twitter_accountsテーブルに保存されているユーザーからaccount_idを取り出し配列に格納
    ・usersテーブルから、auto_follow_flgがtrueのユーザーを取り出し配列に格納
    ・account_id由来の配列からアカウントID1つを取り出し、そのユーザーをfor文で回し全員にフォローさせる
    ・既にフォローしているアカウントであればフォローしない
    ・users配列内のユーザーの処理が完了したら、次のaccount_idを取り出しフォローさせる
    ・これを繰り返す。
    
    ・問題点 > 起算点をいつにするか？
    
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
      Log::debug('Console/Commands AutoFollow 定刻なので自動フォロー開始');
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
      // TwitterControllerのインスタンスを取得し、自動フォローメソッドを起動する
      $twitterController = new TwitterController();
      
      // 自動フォロー
      $twitterController->auto_follow();
  
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
      Log::debug('定刻の自動フォロー処理を終了します。');
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
      
    }
}
