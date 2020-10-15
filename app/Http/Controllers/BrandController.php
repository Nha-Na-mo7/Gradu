<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    //
    /**
     * 指定した通貨のカラムを取得。指定がない場合、全てのカラムを取得。
     */
    public function get_brands($brand_name = null)
    {
      Log::debug('BrandController : get_brands : 通貨カラムの取得');
      // 引数に指定がなかった場合、全ての日誌一覧を取得する
      if($brand_name == null) {
        Log::debug('全部取得します');
        // 全ての通貨カラムを取得し返却する
        return Brand::all();
      }
      
      Log::debug('通貨カラムの取得 : $brand_nameがある場合');
      // 引数と一致する通貨名のカラムを取得
      return Brand::where('name', $brand_name)->first();
    }
}
