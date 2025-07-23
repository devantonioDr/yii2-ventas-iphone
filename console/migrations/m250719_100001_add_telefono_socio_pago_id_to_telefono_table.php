<?php

use yii\db\Migration;

/**
 * Class m250719_100001_add_telefono_socio_pago_id_to_telefono_table
 */
class m250719_100001_add_telefono_socio_pago_id_to_telefono_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%telefono}}', 'telefono_socio_pago_id', $this->integer()->null());

        // creates index for column `telefono_socio_pago_id`
        $this->createIndex(
            '{{%idx-telefono-telefono_socio_pago_id}}',
            '{{%telefono}}',
            'telefono_socio_pago_id'
        );

        // add foreign key for table `{{%telefono_socio_pagos}}`
        $this->addForeignKey(
            '{{%fk-telefono-telefono_socio_pago_id}}',
            '{{%telefono}}',
            'telefono_socio_pago_id',
            '{{%telefono_socio_pagos}}',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%telefono_socio_pagos}}`
        $this->dropForeignKey(
            '{{%fk-telefono-telefono_socio_pago_id}}',
            '{{%telefono}}'
        );

        // drops index for column `telefono_socio_pago_id`
        $this->dropIndex(
            '{{%idx-telefono-telefono_socio_pago_id}}',
            '{{%telefono}}'
        );

        $this->dropColumn('{{%telefono}}', 'telefono_socio_pago_id');
    }
} 