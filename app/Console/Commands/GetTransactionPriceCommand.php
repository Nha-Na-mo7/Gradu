<?php

namespace App\Console\Commands;

use App\Http\Controllers\CoinCheckController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetTransactionPriceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:transaction_price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '24時間以内の最高or最低取引価格を取得するコマンドです。';

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
      Log::debug('Console/Commands GetTransactionPrice 24時間の取引価格取得');
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
      // CoinCheckControllerのインスタンスを取得し、取引価格取得メソッドを起動する
      $coincheck = new CoinCheckController();
  
      // 日ごとのツイート数カウント開始
      $coincheck->daily_price_check();
  
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
      Log::debug('定刻の最低・最高取引価格の取得を終了します。');
      Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
    }
}
