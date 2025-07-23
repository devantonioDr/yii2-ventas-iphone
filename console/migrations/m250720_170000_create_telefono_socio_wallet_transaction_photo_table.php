<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%telefono_socio_wallet_transaction_photo}}`.
 */
class m250720_170000_create_telefono_socio_wallet_transaction_photo_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%telefono_socio_wallet_transaction_photo}}', [
            'id' => $this->primaryKey(),
            'transaction_id' => $this->integer()->notNull(),
            'path' => $this->string(255)->notNull(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        // creates index for column `transaction_id`
        $this->createIndex(
            '{{%idx-wallet_transaction_photo-transaction_id}}',
            '{{%telefono_socio_wallet_transaction_photo}}',
            'transaction_id'
        );

        // add foreign key for table `telefono_socio_wallet_transaction`
        $this->addForeignKey(
            '{{%fk-wallet_transaction_photo-transaction_id}}',
            '{{%telefono_socio_wallet_transaction_photo}}',
            'transaction_id',
            '{{%telefono_socio_wallet_transaction}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `telefono_socio_wallet_transaction`
        $this->dropForeignKey(
            '{{%fk-wallet_transaction_photo-transaction_id}}',
            '{{%telefono_socio_wallet_transaction_photo}}'
        );

        // drops index for column `transaction_id`
        $this->dropIndex(
            '{{%idx-wallet_transaction_photo-transaction_id}}',
            '{{%telefono_socio_wallet_transaction_photo}}'
        );

        $this->dropTable('{{%telefono_socio_wallet_transaction_photo}}');
    }
} 