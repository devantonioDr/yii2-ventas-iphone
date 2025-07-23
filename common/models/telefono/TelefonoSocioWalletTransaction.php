<?php

namespace common\models\telefono;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "telefono_socio_wallet_transaction".
 *
 * @property int $id
 * @property int $wallet_id
 * @property string $type
 * @property float $amount
 * @property string|null $comment
 * @property float|null $current_balance
 * @property int $created_at
 * @property int $updated_at
 *
 * @property TelefonoSocioWallet $wallet
 * @property TelefonoSocioWalletTransactionPhoto[] $photos
 */
class TelefonoSocioWalletTransaction extends ActiveRecord
{
    const TYPE_CREDIT = 'credit';
    const TYPE_DEBIT = 'debit';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'telefono_socio_wallet_transaction';
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['wallet_id', 'type', 'amount'], 'required'],
            [['wallet_id', 'created_at', 'updated_at'], 'integer'],
            [['amount', 'current_balance'], 'number'],
            [['type'], 'string', 'max' => 50],
            [['comment'], 'string', 'max' => 255],
            [['type'], 'in', 'range' => [self::TYPE_CREDIT, self::TYPE_DEBIT]],
            [['wallet_id'], 'exist', 'skipOnError' => true, 'targetClass' => TelefonoSocioWallet::class, 'targetAttribute' => ['wallet_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wallet_id' => 'ID de Wallet',
            'type' => 'Tipo',
            'amount' => 'Monto',
            'comment' => 'Comentario',
            'current_balance' => 'Balance Actual',
            'created_at' => 'Fecha de Creación',
            'updated_at' => 'Fecha de Actualización',
        ];
    }

    /**
     * Gets query for [[Wallet]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWallet()
    {
        return $this->hasOne(TelefonoSocioWallet::class, ['id' => 'wallet_id']);
    }

    /**
     * Gets query for [[TelefonoSocioWalletTransactionPhotos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPhotos()
    {
        return $this->hasMany(TelefonoSocioWalletTransactionPhoto::class, ['transaction_id' => 'id']);
    }
} 