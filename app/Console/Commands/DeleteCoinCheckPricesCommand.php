<?php

namespace App\Console\Commands;

use App\Http\Controllers\CoinCheckController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteCoinCheckPricesCommand extends Command
{
    // N日を経過したツイート数データを全て削除する
    const DELETE_DAYS = 14;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:delete_coincheck_prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DB容量が無限に増え続けるのを避けるため、一定期間ごとにBTCの取引価格レコードを削除します。';

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
        Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
        Log::debug('Console/Commands DeleteCoinCheckPrices 古い取引価格データ削除');
        Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
        // CoinCheckControllerのインスタンスを取得
        $coincheckController = new CoinCheckController();
    
        // 1日1回、14日以上前の古くなったレコードを削除する
        $coincheckController->delete_price_by_db(self::DELETE_DAYS);
    
        Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
        Log::debug('定刻の取引価格レコードの削除を終了します。');
        Log::debug('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
    }
}
