<?php

use yii\db\Migration;

/**
 * Class m250714_202350_add_suplidor_to_telefono_table
 */
class m250714_202350_add_suplidor_to_telefono_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%telefono}}', 'suplidor', $this->string(255)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%telefono}}', 'suplidor');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250714_202350_add_suplidor_to_telefono_table cannot be reverted.\n";

        return false;
    }
    */
}
