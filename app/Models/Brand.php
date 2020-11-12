<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
  // タイムスタンプカラムは用意していないので、無理やり挿入しようとしてエラーにならないようにfalseにする
  public $timestamps = false;
  
}
