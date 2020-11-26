<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }
    /**
     * 指定した通貨のカラムを取得。指定がない場合、全てのカラムを取得。
     */
    public function get_brands($brand_id = null)
    {
      Log::debug('BrandController.get_brands : 通貨カラムの取得');
      // 引数に指定がなかった場合、全ての通貨を取得し返却
      if($brand_id == null) {
        return Brand::all();
      }
  
      // 引数と一致する通貨のカラムを取得
      Log::debug('通貨情報を取得します。取得する通貨ID: '.$brand_id);
      return Brand::where('id', $brand_id)->first();
    }
}
