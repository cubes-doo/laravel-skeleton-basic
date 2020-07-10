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

$factory->define(App\Models\Example::class, function (Faker $faker) {
    return [
        'title' => $faker->text(100),
        'description' => $faker->text(200),
        'updated_at' => now(),
        'created_at' => now(),
        'created_by' => 1
    ];
});
