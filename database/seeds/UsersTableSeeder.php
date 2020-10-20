<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
  public function run()
  {
      // 開発用ユーザーを定義
      User::create([
          'name' => 'develop_user',
          'email' => 'my_email@gmail.com',
          'password' => Hash::make('my_secure_password'), // この場合、「my_secure_password」でログインできる
          'remember_token' => str_random(10),
      ]);
    
      // モデルファクトリーで定義したテストユーザーを 20 作成
      factory(User::class, 20)->create();
  }

}
