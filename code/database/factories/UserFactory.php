<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
| Faking all attributes for a model, this way removing redundancies, and 
| adding ease of use accross all seeders and tests.
|
*/

$factory->define(App\Models\User::class, function (Faker $faker) {
    return [
        'first_name'     => $faker->name,
        'last_name'      => $faker->lastName,
        'email'          => $faker->unique()->safeEmail,
        'password'       => bcrypt('psst!@#'),
        'remember_token' => str_random(10),
    ];
});
