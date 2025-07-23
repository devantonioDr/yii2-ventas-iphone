<?php

namespace common\models\telefono;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "telefono_gasto".
 *
 * @property int $id
 * @property int $telefono_id
 * @property string $descripcion
 * @property float $monto_gasto
 * @property string $fecha_gasto
 * @property Telefono $telefono
 */
class TelefonoGasto extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%telefono_gasto}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['fecha_gasto'],
                ],
                'value' => new \yii\db\Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['telefono_id', 'descripcion', 'monto_gasto'], 'required'],
            [['telefono_id'], 'integer'],
            [['monto_gasto'], 'safe'],
            [['fecha_gasto'], 'safe'],
            [['descripcion'], 'string', 'max' => 255],
            [['telefono_id'], 'exist', 'skipOnError' => true, 'targetClass' => Telefono::class, 'targetAttribute' => ['telefono_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'telefono_id' => 'Teléfono',
            'descripcion' => 'Descripción',
            'monto_gasto' => 'Monto del Gasto',
            'fecha_gasto' => 'Fecha del Gasto',
        ];
    }

    /**
     * Gets query for [[Telefono]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTelefono()
    {
        return $this->hasOne(Telefono::class, ['id' => 'telefono_id']);
    }
} 