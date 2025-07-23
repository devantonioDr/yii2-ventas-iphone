<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%telefono}}`.
 */
class m250716_183000_add_telefono_compra_id_to_telefono_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%telefono}}', 'telefono_compra_id', $this->integer());

        $this->addForeignKey(
            '{{%fk-telefono-telefono_compra_id}}',
            '{{%telefono}}',
            'telefono_compra_id',
            '{{%telefono_compra}}',
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
            '{{%fk-telefono-telefono_compra_id}}',
            '{{%telefono}}'
        );

        $this->dropColumn('{{%telefono}}', 'telefono_compra_id');
    }
} 