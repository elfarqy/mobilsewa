<?php

namespace console\controllers;

use common\models\User;
use common\models\Vehicle;
use Faker\Factory;
use Yii;
use yii\console\Controller;
use yii\helpers\VarDumper;

class GeneratorController extends Controller
{
    public function actionData()
    {
        $this->stdout("Generate User \n");

        $user = new User([
            'email' => 'admin@example.com',
            'username' => 'admin@example.com',
            'role' => 'admin',
            'auth_key' => Yii::$app->security->generateRandomString(),

        ]);
        $user->setPassword('123456');

        if (!$user->save()){
            VarDumper::dump($user->errors);
            exit();
        }


        $counter = 3;

        for ($i = 0; $i <= $counter; $i++)
        {
            $faker = Factory::create('id');
            $email = $faker->email;
            $this->stdout("generate user {$i} \n");

            $driver = new User([
                'email' => $email,
                'username' => $email,
                'role' => 'driver',
                'auth_key' => Yii::$app->security->generateRandomString()
            ]);
            $driver->setPassword('123456');

            if (!$driver->save()){
                VarDumper::dump($driver->errors);
                exit();
            }

            $manager = $faker->email;

            $managerInstance = new User([
                'email' => $manager,
                'username' => $manager,
                'role' => 'manager',
                'auth_key' => Yii::$app->security->generateRandomString()
            ]);
            $managerInstance->setPassword('123456');

            if (!$managerInstance->save()){
                VarDumper::dump($managerInstance->errors);
                exit();
            }

        }


        $this->stdout("Generate Vehicles \n");
        for ($i = 0; $i <= $counter; $i++)
        {
            $this->stdout("generate vehicle {$i} \n");
            $faker = Factory::create('id');
            $isbn = $faker->randomNumber(4);
            $name = $faker->text(10);

            $vehicle = new Vehicle([
                'name' => "Mobil {$name}",
                'plate_number' => "N {$isbn}",
            ]);
//            $driver->setPassword('123456');

            if (!$vehicle->save()){
                VarDumper::dump($vehicle->errors);
                exit();
            }

        }

        $this->stdout("Done \n");

    }

}