<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Brand;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Brand::class, function (Faker $faker) {
    return [
        [
            'name' => Str::random(3),
            'icon' => Str::random(3).'.png',
            'handling' => true,
        ],
        [
            'name' => Str::random(4),
            'icon' => '',
            'handling' => false,
        ],
    ];
});
