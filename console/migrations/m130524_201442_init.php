<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'verification_token' => $this->string()->defaultValue(null),
            'access_token' => $this->string(512),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        //pass: 123456
        $this->insert('{{%user}}', array(
            'username' => 'zeros0',
            'auth_key' => 'wFYCL1nzZjh3FH330cRYmXIoPVALWw1o',
            'password_hash' => '$2y$13$Lji9oFTdu972iDJFliOh5esY.5e5j.OBmLgdiea9s7LD8BzEUUWRa',
            'password_reset_token' => NULL,
            'email' => '',
            'status' => 10,
            'created_at' => 1432295192,
            'updated_at' => 1432295192,
            'access_token' => 2391
        ));

    }

    public function down()
    {
        $this->delete('{{%user}}', ['id' => 1]);
        $this->dropTable('{{%user}}');
    }
}
