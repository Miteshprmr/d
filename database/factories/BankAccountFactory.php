<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\BankAccount;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(BankAccount::class, function (Faker $faker) {
    return [
        'account_number' => $faker->unique()->randomNumber,
        'balance' => $faker->randomFloat(2, 1, 999999),
    ];
});
