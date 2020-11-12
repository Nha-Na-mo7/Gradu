<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique()->comment('アルファベットの略称名');
            $table->string('realname')->unique()->comment('カタカナでの一般的な読み方');
            $table->string('icon')->nullable()->comment('svgアイコンのパス');
            $table->boolean('handling')->comment('CoinCheckで現在取扱中の銘柄であるか');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brands');
    }
}
