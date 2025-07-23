<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%telefono}}`.
 */
class m250716_190000_remove_suplidor_from_telefono_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%telefono}}', 'suplidor');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%telefono}}', 'suplidor', $this->string(255));
    }
} 