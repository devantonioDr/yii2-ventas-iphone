<?php

use yii\db\Migration;

/**
 * Class m240101_000002_create_telefono_marca_modelo_table
 */
class m240101_000002_create_telefono_marca_modelo_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        return true;
        $this->createTable('{{%telefono_marca_modelo}}', [
            'id' => $this->primaryKey(),
            'marca' => $this->string()->notNull()->comment('Marca del teléfono'),
            'modelo' => $this->string()->notNull()->comment('Modelo del teléfono'),
        ]);

        // Crear índices para mejorar el rendimiento
        $this->createIndex('idx_telefono_marca_modelo_marca', '{{%telefono_marca_modelo}}', 'marca');
        $this->createIndex('idx_telefono_marca_modelo_modelo', '{{%telefono_marca_modelo}}', 'modelo');
        $this->createIndex('idx_telefono_marca_modelo_unique', '{{%telefono_marca_modelo}}', ['marca', 'modelo'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%telefono_marca_modelo}}');
    }
} 