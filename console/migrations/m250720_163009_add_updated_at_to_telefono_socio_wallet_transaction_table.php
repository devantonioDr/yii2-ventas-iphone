<?php

use yii\db\Migration;

/**
 * Class m250720_163009_add_updated_at_to_telefono_socio_wallet_transaction_table
 */
class m250720_163009_add_updated_at_to_telefono_socio_wallet_transaction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%telefono_socio_wallet_transaction}}', 'updated_at', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%telefono_socio_wallet_transaction}}', 'updated_at');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        $this->dropColumn('{{%telefono_socio_wallet_transaction}}', 'updated_at');
    }
    */
}
