<?php

namespace App\Console\Commands;

use App\Http\Controllers\TwitterAccountListController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SearchAccountsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:searchaccounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '定期的に仮想通貨関連アカウントを取得するファイルです';

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
      Log::debug('Console/Commands SearchAccounts 仮想通貨アカウント取得');
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
      // TwitterAccountListControllerのインスタンスを取得し、アカウント取得メソッドを起動する
      $twitterAccountList = new TwitterAccountListController();
  
      // 仮想通貨アカウント取得
      $twitterAccountList->search_accounts();
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
      Log::debug('定刻の仮想通貨アカウント取得を終了します。');
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
    }
}
