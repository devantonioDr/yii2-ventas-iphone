<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%telefono_socio_wallet}}`.
 */
class m250720_100000_create_telefono_socio_wallet_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%telefono_socio_wallet}}', [
            'id' => $this->primaryKey(),
            'telefono_socio_id' => $this->integer()->notNull(),
            'balance' => $this->decimal(10, 2)->notNull()->defaultValue(0),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-telefono_socio_wallet-telefono_socio_id',
            '{{%telefono_socio_wallet}}',
            'telefono_socio_id',
            '{{%telefono_socio}}',
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
            'fk-telefono_socio_wallet-telefono_socio_id',
            '{{%telefono_socio_wallet}}'
        );

        $this->dropTable('{{%telefono_socio_wallet}}');
    }
} 