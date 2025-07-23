<?php

namespace common\models\telefono;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "telefono_socio_pago_photo".
 *
 * @property int $id
 * @property int $pago_id
 * @property string $path
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property TelefonoSocioPago $pago
 */
class TelefonoSocioPagoPhoto extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'telefono_socio_pago_photo';
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
            [['pago_id', 'path'], 'required'],
            [['pago_id', 'created_at', 'updated_at'], 'integer'],
            [['path'], 'string', 'max' => 255],
            [['pago_id'], 'exist', 'skipOnError' => true, 'targetClass' => TelefonoSocioPago::class, 'targetAttribute' => ['pago_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pago_id' => 'Pago ID',
            'path' => 'Path',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Pago]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPago()
    {
        return $this->hasOne(TelefonoSocioPago::class, ['id' => 'pago_id']);
    }
} 