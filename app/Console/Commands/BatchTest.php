<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BatchTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'batchtest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description =
        'テスト用のバッチファイルです。本番では使用しません。';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // ここに処理を記述します
        echo '光の浄化を受けよ！';
    }
}
