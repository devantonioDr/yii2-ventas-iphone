<?php

use yii\db\Migration;

/**
 * Class m240101_000003_create_telefono_socio_table
 */
class m240101_000003_create_telefono_socio_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        return true;
        $this->createTable('{{%telefono_socio}}', [
            'id' => $this->primaryKey(),
            'nombre' => $this->string(100)->notNull(),
            'margen_ganancia' => $this->decimal(5, 2)->notNull()->defaultValue(0.00),
        ]);

        // Agregar índice para búsquedas por nombre
        $this->createIndex('idx_telefono_socio_nombre', '{{%telefono_socio}}', 'nombre');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%telefono_socio}}');
    }
} 