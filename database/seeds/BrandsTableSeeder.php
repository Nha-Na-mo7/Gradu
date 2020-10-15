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
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'ETH',
                'icon' => 'eth.svg',
                'handling' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'ETC',
                'icon' => 'etc.svg',
                'handling' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'LSK',
                'icon' => 'lsk.svg',
                'handling' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'FCT',
                'icon' => 'fct.svg',
                'handling' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'XRP',
                'icon' => 'xrp.svg',
                'handling' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'XEM',
                'icon' => 'xem.svg',
                'handling' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'LTC',
                'icon' => 'ltc.svg',
                'handling' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'BCH',
                'icon' => 'bch.svg',
                'handling' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'MONA',
                'icon' => 'mona.svg',
                'handling' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'XLM',
                'icon' => 'xlm.svg',
                'handling' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'QTUM',
                'icon' => 'qtum.svg',
                'handling' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'BAT',
                'icon' => 'bat.svg',
                'handling' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'IOST',
                'icon' => 'iost.svg',
                'handling' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'DASH',
                'icon' => '',
                'handling' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'ZEC',
                'icon' => '',
                'handling' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'XMR',
                'icon' => '',
                'handling' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'REP',
                'icon' => '',
                'handling' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
