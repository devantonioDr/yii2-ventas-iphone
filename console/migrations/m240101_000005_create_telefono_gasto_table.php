<?php

use yii\db\Migration;

/**
 * Class m240101_000005_create_telefono_gasto_table
 */
class m240101_000005_create_telefono_gasto_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%telefono_gasto}}', [
            'id' => $this->primaryKey(),
            'telefono_id' => $this->integer()->notNull(),
            'descripcion' => $this->string(255)->notNull(),
            'monto_gasto' => $this->decimal(10, 2)->notNull(),
            'fecha_gasto' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex(
            'idx-telefono_gasto-telefono_id',
            '{{%telefono_gasto}}',
            'telefono_id'
        );

        $this->addForeignKey(
            'fk-telefono_gasto-telefono_id',
            '{{%telefono_gasto}}',
            'telefono_id',
            '{{%telefono}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-telefono_gasto-telefono_id', '{{%telefono_gasto}}');
        $this->dropIndex('idx-telefono_gasto-telefono_id', '{{%telefono_gasto}}');
        $this->dropTable('{{%telefono_gasto}}');
    }
} 