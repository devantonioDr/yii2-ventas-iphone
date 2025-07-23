<?php

use yii\db\Migration;

/**
 * Class m250723_193333_alter_character_set_for_wallet_transaction
 */
class m250723_193333_alter_character_set_for_wallet_transaction extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250723_193333_alter_character_set_for_wallet_transaction cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250723_193333_alter_character_set_for_wallet_transaction cannot be reverted.\n";

        return false;
    }
    */
}
