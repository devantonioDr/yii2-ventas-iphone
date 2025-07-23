<?php

use yii\db\Migration;

/**
 * Class m250719_120000_add_codigo_factura_to_telefono_socio_pagos_table
 */
class m250719_120000_add_codigo_factura_to_telefono_socio_pagos_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%telefono_socio_pagos}}', 'codigo_factura', $this->string(255)->notNull()->unique()->after('id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%telefono_socio_pagos}}', 'codigo_factura');
    }
} 