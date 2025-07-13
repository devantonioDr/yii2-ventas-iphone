<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\User;

class RegisterUserController extends Controller
{
    public function actionIndex()
    {
        $username = $this->prompt('Enter username:');
        $password = $this->prompt('Enter password:');
        $email = $this->prompt('Enter email:');

        $user = new User();
        $user->username = strtolower($username);
        $user->email = strtolower($email);
        $user->setPassword($password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();

        if ($user->save()) {
            echo "User registered successfully." . PHP_EOL;
        } else {
            echo "Failed to register user." . PHP_EOL;
            foreach ($user->getErrors() as $attribute => $errors) {
                echo $attribute . ': ' . implode(', ', $errors) . PHP_EOL;
            }
        }
    }


    public function actionTest()
    {
        $username = $this->prompt('Enter username:');
        $password = $this->prompt('Enter password:');

       
    }
}
