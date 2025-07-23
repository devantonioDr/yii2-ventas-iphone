<?php

namespace common\models\telefono;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "telefono_socio_pagos".
 *
 * @property int $id
 * @property string $codigo_factura
 * @property int $socio_id
 * @property string $fecha_pago
 * @property int $cantidad_telefonos
 * @property float $ganancia_socio
 * @property float $ganancia_empresa
 * @property float $ganancia_neta
 * @property float $gastos
 * @property float $invertido
 *
 * @property TelefonoSocio $socio
 * @property Telefono[] $telefonos
 */
class TelefonoSocioPago extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%telefono_socio_pagos}}';
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['fecha_pago'],
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
            [['socio_id', 'fecha_pago', 'cantidad_telefonos', 'ganancia_socio', 'ganancia_empresa', 'ganancia_neta', 'gastos', 'invertido', 'codigo_factura'], 'required'],
            [['socio_id', 'cantidad_telefonos'], 'integer'],
            [['fecha_pago'], 'safe'],
            [['ganancia_socio', 'ganancia_empresa', 'ganancia_neta', 'gastos', 'invertido'], 'number'],
            [['codigo_factura'], 'string', 'max' => 255],
            [['codigo_factura'], 'unique'],
            [['socio_id'], 'exist', 'skipOnError' => true, 'targetClass' => TelefonoSocio::class, 'targetAttribute' => ['socio_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo_factura' => 'Código Factura',
            'socio_id' => 'Socio ID',
            'fecha_pago' => 'Fecha Pago',
            'cantidad_telefonos' => 'Cantidad Telefonos',
            'ganancia_socio' => 'Ganancia Socio',
            'ganancia_empresa' => 'Ganancia Empresa',
            'ganancia_neta' => 'Ganancia Neta',
            'gastos' => 'Gastos',
            'invertido' => 'Invertido',
        ];
    }

    /**
     * Gets query for [[Socio]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSocio()
    {
        return $this->hasOne(TelefonoSocio::class, ['id' => 'socio_id']);
    }

    /**
     * Gets query for [[Telefonos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTelefonos()
    {
        return $this->hasMany(Telefono::class, ['telefono_socio_pago_id' => 'id']);
    }

    /**
     * Gets query for [[TelefonoSocioPagoPhotos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPhotos()
    {
        return $this->hasMany(TelefonoSocioPagoPhoto::class, ['pago_id' => 'id']);
    }
} 