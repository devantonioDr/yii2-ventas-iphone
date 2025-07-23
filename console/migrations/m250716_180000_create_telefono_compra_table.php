<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%telefono_compra}}`.
 */
class m250716_180000_create_telefono_compra_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%telefono_compra}}', [
            'id' => $this->primaryKey(),
            'fecha_compra' => $this->dateTime()->notNull(),
            'suplidor' => $this->string(255)->null(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%telefono_compra}}');
    }
} 