<?php

use yii\db\Migration;

/**
 * Class m240101_000001_create_telefono_table
 */
class m240101_000001_create_telefono_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        return true;
        $this->createTable('{{%telefono}}', [
            'id' => $this->primaryKey(),
            'imei' => $this->string()->notNull()->unique()->comment('IMEI del teléfono'),
            'marca' => $this->string()->notNull()->comment('Marca del teléfono'),
            'modelo' => $this->string()->notNull()->comment('Modelo del teléfono'),
            'precio_adquisicion' => $this->decimal(10, 2)->notNull()->comment('Precio de adquisición'),
            'precio_venta_recomendado' => $this->decimal(10, 2)->notNull()->comment('Precio de venta recomendado'),
            'fecha_ingreso' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment('Fecha de ingreso'),
        ]);

        // Crear índices para mejorar el rendimiento
        $this->createIndex('idx_telefono_imei', '{{%telefono}}', 'imei');
        $this->createIndex('idx_telefono_marca', '{{%telefono}}', 'marca');
        $this->createIndex('idx_telefono_fecha_ingreso', '{{%telefono}}', 'fecha_ingreso');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%telefono}}');
    }
} 