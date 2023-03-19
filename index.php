<?php

require __DIR__ . '/vendor/autoload.php';

use App\User;
use App\UserManager;

try {

//    $faker = Faker\Factory::create();
//    for($i = 0; $i < 20; $i++) {
//        $userData = [
//            'first_name' => $faker->firstName,
//            'last_name' => $faker->lastName,
//            'gender' => 1,
//            'city' => $faker->city,
//            'date_birth' =>  $faker->date('Y-m-d', '2010-01-01')
//        ];
//        $user = new User(data: $userData);
//        $user->save();
//    }
    $manager = new UserManager([['date_birth', '>', '1995-03-05'], ['first_name', '!=', 'Shanny']], new User);
    $models = $manager->loadModels();
//    $manager->deleteModel($models[0]);
//    $models = $manager->loadModels();
    $models = array_map(fn($model) => (array) $model->transform(), $models);
    echo '<pre>'; print_r($models);
} catch (Exception $e) {
    echo $e->getMessage();
}
