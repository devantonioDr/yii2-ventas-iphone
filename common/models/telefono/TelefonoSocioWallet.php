<?php

namespace common\models\telefono;

use Yii;
use yii\db\ActiveRecord;

class TelefonoSocioWallet extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%telefono_socio_wallet}}';
    }

    public function rules()
    {
        return [
            [['telefono_socio_id', 'balance'], 'required'],
            [['telefono_socio_id'], 'integer'],
            [['balance'], 'number'],
            [['updated_at'], 'safe'],
            [['telefono_socio_id'], 'exist', 'skipOnError' => true, 'targetClass' => TelefonoSocio::class, 'targetAttribute' => ['telefono_socio_id' => 'id']],
        ];
    }

    public function getTelefonoSocio()
    {
        return $this->hasOne(TelefonoSocio::class, ['id' => 'telefono_socio_id']);
    }

    public function getTransactions()
    {
        return $this->hasMany(TelefonoSocioWalletTransaction::class, ['wallet_id' => 'id']);
    }
} 