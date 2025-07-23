<?php

namespace common\models\telefono;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "telefono_socio_wallet_transaction_photo".
 *
 * @property int $id
 * @property int $transaction_id
 * @property string $path
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property TelefonoSocioWalletTransaction $transaction
 */
class TelefonoSocioWalletTransactionPhoto extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'telefono_socio_wallet_transaction_photo';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['transaction_id', 'path'], 'required'],
            [['transaction_id', 'created_at', 'updated_at'], 'integer'],
            [['path'], 'string', 'max' => 255],
            [['transaction_id'], 'exist', 'skipOnError' => true, 'targetClass' => TelefonoSocioWalletTransaction::class, 'targetAttribute' => ['transaction_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'transaction_id' => 'Transaction ID',
            'path' => 'Path',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Transaction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTransaction()
    {
        return $this->hasOne(TelefonoSocioWalletTransaction::class, ['id' => 'transaction_id']);
    }
} 