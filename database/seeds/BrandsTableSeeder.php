<?php

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
                'realname' => 'ビットコイン',
                'icon' => 'btc.svg',
                'handling' => true,
            ],
            [
                'name' => 'ETH',
                'realname' => 'イーサリアム',
                'icon' => 'eth.svg',
                'handling' => true,
            ],
            [
                'name' => 'ETC',
                'realname' => 'イーサリアムクラシック',
                'icon' => 'etc.svg',
                'handling' => true,
            ],
            [
                'name' => 'LSK',
                'realname' => 'リスク',
                'icon' => 'lsk.svg',
                'handling' => true,
            ],
            [
                'name' => 'FCT',
                'realname' => 'ファクトム',
                'icon' => 'fct.svg',
                'handling' => true,
            ],
            [
                'name' => 'XRP',
                'realname' => 'リップル',
                'icon' => 'xrp.svg',
                'handling' => true,
            ],
            [
                'name' => 'XEM',
                'realname' => 'ネム',
                'icon' => 'xem.svg',
                'handling' => true,
            ],
            [
                'name' => 'LTC',
                'realname' => 'ライトコイン',
                'icon' => 'ltc.svg',
                'handling' => true,
            ],
            [
                'name' => 'BCH',
                'realname' => 'ビットコインキャッシュ',
                'icon' => 'bch.svg',
                'handling' => true,
            ],
            [
                'name' => 'MONA',
                'realname' => 'モナコイン',
                'icon' => 'mona.svg',
                'handling' => true,
            ],
            [
                'name' => 'XLM',
                'realname' => 'ステラルーメン',
                'icon' => 'xlm.svg',
                'handling' => true,
            ],
            [
                'name' => 'QTUM',
                'realname' => 'クアンタム',
                'icon' => 'qtum.svg',
                'handling' => true,
            ],
            [
                'name' => 'BAT',
                'realname' => 'ベーシックアテンショントークン',
                'icon' => 'bat.svg',
                'handling' => true,
            ],
            [
                'name' => 'IOST',
                'realname' => 'IOST',
                'icon' => 'iost.svg',
                'handling' => true,
            ],
            [
                'name' => 'DASH',
                'realname' => 'ダッシュ',
                'icon' => 'dash.svg',
                'handling' => false,
            ],
            [
                'name' => 'ZEC',
                'realname' => 'ジーキャッシュ',
                'icon' => 'zec.svg',
                'handling' => false,
            ],
            [
                'name' => 'XMR',
                'realname' => 'モネロ',
                'icon' => 'xmr.svg',
                'handling' => false,
            ],
            [
                'name' => 'REP',
                'realname' => 'オーガー',
                'icon' => 'rep.svg',
                'handling' => false,
            ],
        ]);
    }
}
