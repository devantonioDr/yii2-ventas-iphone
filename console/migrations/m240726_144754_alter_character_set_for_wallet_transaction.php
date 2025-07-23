<?php

use yii\db\Migration;

/**
 * Class m240726_144754_alter_character_set_for_wallet_transaction
 */
class m240726_144754_alter_character_set_for_wallet_transaction extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE {{%telefono_socio_wallet_transaction}} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->alterColumn('{{%telefono_socio_wallet_transaction}}', 'comment', $this->text()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('ALTER TABLE {{%telefono_socio_wallet_transaction}} CHARACTER SET utf8 COLLATE utf8_unicode_ci');
        $this->alterColumn('{{%telefono_socio_wallet_transaction}}', 'comment', $this->text()->append('CHARACTER SET utf8 COLLATE utf8_unicode_ci'));
    }
} 