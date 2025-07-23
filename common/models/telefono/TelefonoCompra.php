<?php

namespace common\models\telefono;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "telefono_compra".
 *
 * @property int $id
 * @property string $codigo_factura
 * @property string $fecha_compra
 * @property string $suplidor
 * 
 * @property Telefono[] $telefonos
 */
class TelefonoCompra extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%telefono_compra}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fecha_compra'], 'required'],
            [['fecha_compra'], 'safe'],
            [['suplidor'], 'trim'],
            [['suplidor'], 'default', 'value' => null],
            [['suplidor'], 'string', 'max' => 255],
            [['codigo_factura'], 'unique'],
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->codigo_factura = $this->generateInvoiceCode();
            }
            return true;
        }
        return false;
    }

    private function generateInvoiceCode()
    {
        $lastRecord = self::find()->orderBy(['id' => SORT_DESC])->one();
        $nextId = ($lastRecord) ? $lastRecord->id + 1 : 1;
        return str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo_factura' => 'Código Factura',
            'fecha_compra' => 'Fecha Compra',
            'suplidor' => 'Suplidor',
        ];
    }

    /**
     * Gets query for [[Telefonos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTelefonos()
    {
        return $this->hasMany(Telefono::class, ['telefono_compra_id' => 'id']);
    }
} 