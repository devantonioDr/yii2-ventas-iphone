<?php

use yii\db\Migration;

/**
 * Class m250718_120000_add_codigo_factura_to_telefono_compra_table
 */
class m250718_120000_add_codigo_factura_to_telefono_compra_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%telefono_compra}}', 'codigo_factura', $this->string(50)->unique()->after('id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%telefono_compra}}', 'codigo_factura');
    }
} 