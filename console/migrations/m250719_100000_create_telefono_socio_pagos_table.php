<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%telefono_socio_pagos}}`.
 */
class m250719_100000_create_telefono_socio_pagos_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%telefono_socio_pagos}}', [
            'id' => $this->primaryKey(),
            'socio_id' => $this->integer()->notNull(),
            'fecha_pago' => $this->dateTime()->notNull(),
            'cantidad_telefonos' => $this->integer()->notNull(),
            'ganancia_socio' => $this->decimal(10, 2)->notNull(),
            'ganancia_empresa' => $this->decimal(10, 2)->notNull(),
            'gastos' => $this->decimal(10, 2)->notNull(),
            'invertido' => $this->decimal(10, 2)->notNull(),
        ]);

        // creates index for column `socio_id`
        $this->createIndex(
            '{{%idx-telefono_socio_pagos-socio_id}}',
            '{{%telefono_socio_pagos}}',
            'socio_id'
        );

        // add foreign key for table `{{%telefono_socio}}`
        $this->addForeignKey(
            '{{%fk-telefono_socio_pagos-socio_id}}',
            '{{%telefono_socio_pagos}}',
            'socio_id',
            '{{%telefono_socio}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%telefono_socio}}`
        $this->dropForeignKey(
            '{{%fk-telefono_socio_pagos-socio_id}}',
            '{{%telefono_socio_pagos}}'
        );

        // drops index for column `socio_id`
        $this->dropIndex(
            '{{%idx-telefono_socio_pagos-socio_id}}',
            '{{%telefono_socio_pagos}}'
        );

        $this->dropTable('{{%telefono_socio_pagos}}');
    }
} 