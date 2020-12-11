<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpdatedAtTable extends Model
{
    public $timestamps = false;
  
    // 更新日時を管理するテーブルです。
    // アカウント検索や人気通貨ツイート検索などの自動処理におけるテーブルごとの最終更新日時を記録します。
    // このテーブルはユーザー側から触れることはできません。
  
    protected $fillable = [
        'complete_flg',
        'updated_at',
    ];
  
    protected $visible = [
        'updated_at'
    ];
}
