<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Order;
use Faker\Generator as Faker;

$factory->define(Order::class, function (Faker $faker) {
    return [
        'start_latitude' => $faker->latitude,
        'start_longitude' => $faker->longitude,
        'end_latitude' => $faker->latitude,
        'end_longitude' => $faker->longitude,
        'distance_in_meters' => rand(100, 1000),
        'status' => 0,
    ];
});
