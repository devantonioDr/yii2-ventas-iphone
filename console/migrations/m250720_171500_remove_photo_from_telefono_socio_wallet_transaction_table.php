<?php

use yii\db\Migration;

/**
 * Handles removing columns from table `{{%telefono_socio_wallet_transaction}}`.
 */
class m250720_171500_remove_photo_from_telefono_socio_wallet_transaction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%telefono_socio_wallet_transaction}}', 'photo');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%telefono_socio_wallet_transaction}}', 'photo', $this->string());
    }
} 