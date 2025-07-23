<?php

use yii\db\Migration;

/**
 * Class m250719_110000_add_ganancia_neta_to_telefono_socio_pagos_table
 */
class m250719_110000_add_ganancia_neta_to_telefono_socio_pagos_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%telefono_socio_pagos}}', 'ganancia_neta', $this->decimal(10, 2)->notNull()->after('ganancia_empresa'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%telefono_socio_pagos}}', 'ganancia_neta');
    }
} 