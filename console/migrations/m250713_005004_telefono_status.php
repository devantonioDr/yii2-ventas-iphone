<?php

use yii\db\Migration;

/**
 * Class m250713_005004_telefono_status
 */
class m250713_005004_telefono_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%telefono}}', 'status', $this->string()
            ->notNull()
            ->defaultValue('en_tienda')
            ->after('modelo'));

        $this->createIndex(
            'idx-telefono-status',
            '{{%telefono}}',
            'status'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'idx-telefono-status',
            '{{%telefono}}'
        );
        $this->dropColumn('{{%telefono}}', 'status');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250713_005004_telefono_status cannot be reverted.\n";

        return false;
    }
    */
}
