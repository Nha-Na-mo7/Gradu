<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MypageController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }
    
    // ビューを返却
    public function index()
    {
      return view('mypage');
    }
}
