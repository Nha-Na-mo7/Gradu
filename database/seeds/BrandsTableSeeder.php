<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    
    // 通貨銘柄の初期データ用

    public function run()
    {
        DB::table('brands')->insert([
            [
                'name' => 'BTC',
                'icon' => 'btc.svg',
                'handling' => true,
            ],
            [
                'name' => 'ETH',
                'icon' => 'eth.svg',
                'handling' => true,
            ],
            [
                'name' => 'ETC',
                'icon' => 'etc.svg',
                'handling' => true,
            ],
            [
                'name' => 'LSK',
                'icon' => 'lsk.svg',
                'handling' => true,
            ],
            [
                'name' => 'FCT',
                'icon' => 'fct.svg',
                'handling' => true,
            ],
            [
                'name' => 'XRP',
                'icon' => 'xrp.svg',
                'handling' => true,
            ],
            [
                'name' => 'XEM',
                'icon' => 'xem.svg',
                'handling' => true,
            ],
            [
                'name' => 'LTC',
                'icon' => 'ltc.svg',
                'handling' => true,
            ],
            [
                'name' => 'BCH',
                'icon' => 'bch.svg',
                'handling' => true,
            ],
            [
                'name' => 'MONA',
                'icon' => 'mona.svg',
                'handling' => true,
            ],
            [
                'name' => 'XLM',
                'icon' => 'xlm.svg',
                'handling' => true,
            ],
            [
                'name' => 'QTUM',
                'icon' => 'qtum.svg',
                'handling' => true,
            ],
            [
                'name' => 'BAT',
                'icon' => 'bat.svg',
                'handling' => true,
            ],
            [
                'name' => 'IOST',
                'icon' => 'iost.svg',
                'handling' => true,
            ],
            [
                'name' => 'DASH',
                'icon' => 'dash.svg',
                'handling' => false,
            ],
            [
                'name' => 'ZEC',
                'icon' => 'zec.svg',
                'handling' => false,
            ],
            [
                'name' => 'XMR',
                'icon' => 'xmr.svg',
                'handling' => false,
            ],
            [
                'name' => 'REP',
                'icon' => 'rep.svg',
                'handling' => false,
            ],
        ]);
    }
}
