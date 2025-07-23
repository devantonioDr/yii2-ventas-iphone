<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%telefono}}`.
 */
class m250717_190000_add_batch_id_to_telefono_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%telefono}}', 'batch_id', $this->string(36)->null()->after('id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%telefono}}', 'batch_id');
    }
} 