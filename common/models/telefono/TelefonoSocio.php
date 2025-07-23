<?php

namespace common\models\telefono;

use Yii;
use yii\db\ActiveRecord;
use common\models\telefono\TelefonoSocioPago;
use common\models\telefono\TelefonoSocioPagoSearch;
use common\models\telefono\TelefonoSocioSearch;
use common\models\telefono\TelefonoSocioWallet;

/**
 * This is the model class for table "telefono_socio".
 *
 * @property int $id
 * @property string $nombre
 * @property float $margen_ganancia
 * @property Telefono[] $telefonos
 */
class TelefonoSocio extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%telefono_socio}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'margen_ganancia'], 'required'],
            [['margen_ganancia'], 'number', 'min' => 0, 'max' => 999.99],
            [['nombre'], 'string', 'max' => 100],
            [['nombre'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre del Socio',
            'margen_ganancia' => 'Margen de Ganancia (%)',
        ];
    }

    /**
     * Gets query for [[Telefonos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTelefonos()
    {
        return $this->hasMany(Telefono::class, ['socio_id' => 'id']);
    }

    public function getTelefonosPendientesPorPagar()
    {
        return $this->hasMany(Telefono::class, ['socio_id' => 'id'])
            ->where(['status' => Telefono::STATUS_VENDIDO]);
    }

    public function getTelefonoSocioPagos()
    {
        return $this->hasMany(TelefonoSocioPago::class, ['telefono_socio_id' => 'id']);
    }

    public function getWallet()
    {
        return $this->hasOne(TelefonoSocioWallet::class, ['telefono_socio_id' => 'id']);
    }
} 