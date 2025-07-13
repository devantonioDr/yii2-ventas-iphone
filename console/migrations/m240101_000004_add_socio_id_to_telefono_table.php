<?php

use yii\db\Migration;

/**
 * Class m240101_000004_add_socio_id_to_telefono_table
 */
class m240101_000004_add_socio_id_to_telefono_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        return true;
        $this->addColumn('{{%telefono}}', 'socio_id', $this->integer()->null());
        
        // Crear índice para la relación
        $this->createIndex('idx_telefono_socio_id', '{{%telefono}}', 'socio_id');
        
        // Crear la foreign key
        $this->addForeignKey(
            'fk_telefono_socio_id',
            '{{%telefono}}',
            'socio_id',
            '{{%telefono_socio}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_telefono_socio_id', '{{%telefono}}');
        $this->dropIndex('idx_telefono_socio_id', '{{%telefono}}');
        $this->dropColumn('{{%telefono}}', 'socio_id');
    }
} 