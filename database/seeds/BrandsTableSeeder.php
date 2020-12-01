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
                'icon' => 'https://cryptotrendstrage.s3-ap-northeast-1.amazonaws.com/images/brand_svg/btc.svg',
                'handling' => true,
            ],
            [
                'name' => 'ETH',
                'realname' => 'イーサリアム',
                'icon' => 'https://cryptotrendstrage.s3-ap-northeast-1.amazonaws.com/images/brand_svg/eth.svg',
                'handling' => true,
            ],
            [
                'name' => 'ETC',
                'realname' => 'イーサリアムクラシック',
                'icon' => 'https://cryptotrendstrage.s3-ap-northeast-1.amazonaws.com/images/brand_svg/etc.svg',
                'handling' => true,
            ],
            [
                'name' => 'LSK',
                'realname' => 'リスク',
                'icon' => 'https://cryptotrendstrage.s3-ap-northeast-1.amazonaws.com/images/brand_svg/lsk.svg',
                'handling' => true,
            ],
            [
                'name' => 'FCT',
                'realname' => 'ファクトム',
                'icon' => 'https://cryptotrendstrage.s3-ap-northeast-1.amazonaws.com/images/brand_svg/fct.svg',
                'handling' => true,
            ],
            [
                'name' => 'XRP',
                'realname' => 'リップル',
                'icon' => 'https://cryptotrendstrage.s3-ap-northeast-1.amazonaws.com/images/brand_svg/xrp.svg',
                'handling' => true,
            ],
            [
                'name' => 'XEM',
                'realname' => 'ネム',
                'icon' => 'https://cryptotrendstrage.s3-ap-northeast-1.amazonaws.com/images/brand_svg/xem.svg',
                'handling' => true,
            ],
            [
                'name' => 'LTC',
                'realname' => 'ライトコイン',
                'icon' => 'https://cryptotrendstrage.s3-ap-northeast-1.amazonaws.com/images/brand_svg/ltc.svg',
                'handling' => true,
            ],
            [
                'name' => 'BCH',
                'realname' => 'ビットコインキャッシュ',
                'icon' => 'https://cryptotrendstrage.s3-ap-northeast-1.amazonaws.com/images/brand_svg/bch.svg',
                'handling' => true,
            ],
            [
                'name' => 'MONA',
                'realname' => 'モナコイン',
                'icon' => 'https://cryptotrendstrage.s3-ap-northeast-1.amazonaws.com/images/brand_svg/mona.svg',
                'handling' => true,
            ],
            [
                'name' => 'XLM',
                'realname' => 'ステラルーメン',
                'icon' => 'https://cryptotrendstrage.s3-ap-northeast-1.amazonaws.com/images/brand_svg/xlm.svg',
                'handling' => true,
            ],
            [
                'name' => 'QTUM',
                'realname' => 'クアンタム',
                'icon' => 'https://cryptotrendstrage.s3-ap-northeast-1.amazonaws.com/images/brand_svg/qtum.svg',
                'handling' => true,
            ],
            [
                'name' => 'BAT',
                'realname' => 'ベーシックアテンショントークン',
                'icon' => 'https://cryptotrendstrage.s3-ap-northeast-1.amazonaws.com/images/brand_svg/bat.svg',
                'handling' => true,
            ],
            [
                'name' => 'IOST',
                'realname' => 'アイオーエスティー',
                'icon' => 'https://cryptotrendstrage.s3-ap-northeast-1.amazonaws.com/images/brand_svg/iost.svg',
                'handling' => true,
            ],
            [
                'name' => 'DASH',
                'realname' => 'ダッシュ',
                'icon' => 'https://cryptotrendstrage.s3-ap-northeast-1.amazonaws.com/images/brand_svg/dash.svg',
                'handling' => false,
            ],
            [
                'name' => 'ZEC',
                'realname' => 'ジーキャッシュ',
                'icon' => 'https://cryptotrendstrage.s3-ap-northeast-1.amazonaws.com/images/brand_svg/zec.svg',
                'handling' => false,
            ],
            [
                'name' => 'XMR',
                'realname' => 'モネロ',
                'icon' => 'https://cryptotrendstrage.s3-ap-northeast-1.amazonaws.com/images/brand_svg/xmr.svg',
                'handling' => false,
            ],
            [
                'name' => 'REP',
                'realname' => 'オーガー',
                'icon' => 'https://cryptotrendstrage.s3-ap-northeast-1.amazonaws.com/images/brand_svg/rep.svg',
                'handling' => false,
            ],
        ]);
    }
}
