<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%telefono_socio_wallet_transaction}}`.
 */
class m250720_100001_create_telefono_socio_wallet_transaction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%telefono_socio_wallet_transaction}}', [
            'id' => $this->primaryKey(),
            'wallet_id' => $this->integer()->notNull(),
            'type' => "ENUM('credit', 'debit') NOT NULL",
            'amount' => $this->decimal(10, 2)->notNull(),
            'comment' => $this->text(),
            'photo' => $this->string(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-wallet_transaction-wallet_id',
            '{{%telefono_socio_wallet_transaction}}',
            'wallet_id',
            '{{%telefono_socio_wallet}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-wallet_transaction-wallet_id',
            '{{%telefono_socio_wallet_transaction}}'
        );

        $this->dropTable('{{%telefono_socio_wallet_transaction}}');
    }
} 