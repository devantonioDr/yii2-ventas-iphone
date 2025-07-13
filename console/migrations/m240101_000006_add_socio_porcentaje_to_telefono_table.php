<?php

use yii\db\Migration;

/**
 * Class m240101_000006_add_socio_porcentaje_to_telefono_table
 */
class m240101_000006_add_socio_porcentaje_to_telefono_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%telefono}}', 'socio_porcentaje', $this->decimal(5, 2)->defaultValue(20.00)->comment('Porcentaje del socio al momento de la inserción'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%telefono}}', 'socio_porcentaje');
    }
} 