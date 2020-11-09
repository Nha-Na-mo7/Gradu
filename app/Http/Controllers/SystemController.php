<?php

namespace App\Http\Controllers;

use App\Models\UpdatedAtTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SystemController extends Controller
{
  // =======================================
  // 最終更新日時テーブルから指定idの情報を取得する
  // =======================================
  public function get_updated_at() {
  
    $id = filter_input(INPUT_GET, 'id') ? filter_input(INPUT_GET, 'id') : 0;
  
    return UpdatedAtTable::find($id);
  }
}
