<?php

use yii\db\Migration;

/**
 * Class m250720_161709_add_current_balance_to_telefono_socio_wallet_transaction_table
 */
class m250720_161709_add_current_balance_to_telefono_socio_wallet_transaction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
            $this->addColumn('{{%telefono_socio_wallet_transaction}}', 'current_balance', $this->decimal(10, 2)->notNull()->after('comment'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%telefono_socio_wallet_transaction}}', 'current_balance');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250720_161709_add_current_balance_to_telefono_socio_wallet_transaction_table cannot be reverted.\n";

        return false;
    }
    */
}
