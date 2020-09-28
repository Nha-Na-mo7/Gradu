<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class RegisterApiTest extends TestCase
{
    // テスト時にテスト用DBをリセットする(マイグレーションを実行し直す)
    use RefreshDatabase;
    
    /**
     * @test
     */
    public function _新規ユーザー作成APItest()
    {
      $data = [
          'email' => 'example@gmail.com',
          'password' => 'Crypto9999',
          'password_confirmation' => 'Crypto9999'
      ];
      
      $response = $this->json('POST', route('register'), $data);
      
      $user = User::first();
      $this->assertEquals($data['email'], $user->email);
      
      $response
          ->assertStatus(201)
          ->assertJson(['email' => $user->email]);
    }
}
