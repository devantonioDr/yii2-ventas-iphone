<?php
$config = require __DIR__ . '/main-local.php';

// access the database properties
// $dbConfig = $config['components']['db'];

// $connection = new \yii\db\Connection([
//     'dsn' => $dbConfig['dsn'],
//     'username' =>  $dbConfig['username'],
//     'password' => $dbConfig['password'],
//     'charset' => $dbConfig['charset'],
// ]);
// $connection->open();
// $row = $connection->createCommand('SELECT * FROM config where id = 1')->queryOne();
// // Cerrar la conexión a la base de datos
// $connection->close();

// $multiploMeta = $row['multiplo_meta'];
// $metaGeneral = $row['meta_general'];

return [
    'user.passwordResetTokenExpire' => 3600,
    'user.passwordMinLength' => 8,
    'appName' => "Combo manager",
];
