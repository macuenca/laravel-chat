<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\ChatMessage::class, function (Faker\Generator $faker, $params) {
    return [
        'conversation_id' => $params['conversation_id'],
        'sender_id' => $params['sender_id'],
        'receiver_id' => $params['receiver_id'],
        'message' => $params['message'],
        'sender_name' => $params['sender_name'],
        'receiver_name' => $params['receiver_name'],
    ];
});