<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%telefono_socio_pago_photo}}`.
 */
class m250723_195230_create_telefono_socio_pago_photo_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%telefono_socio_pago_photo}}';
        if ($this->db->getTableSchema($tableName, true) === null) {
            $this->createTable($tableName, [
                'id' => $this->primaryKey(),
                'pago_id' => $this->integer()->notNull(),
                'path' => $this->string(255)->notNull(),
                'created_at' => $this->integer(),
                'updated_at' => $this->integer(),
            ]);

            $this->createIndex(
                '{{%idx-socio_pago_photo-pago_id}}',
                $tableName,
                'pago_id'
            );

            $this->addForeignKey(
                '{{%fk-socio_pago_photo-pago_id}}',
                $tableName,
                'pago_id',
                '{{%telefono_socio_pagos}}',
                'id',
                'CASCADE'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%telefono_socio_pago_photo}}');
    }
}
