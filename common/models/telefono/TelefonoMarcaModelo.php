<?php

namespace common\models\telefono;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "telefono_marca_modelo".
 *
 * @property string $id
 * @property string $marca
 * @property string $modelo
 */
class TelefonoMarcaModelo extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%telefono_marca_modelo}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'marca', 'modelo'], 'required'],
            [['id'], 'string', 'max' => 255],
            [['marca', 'modelo'], 'string', 'max' => 100],
            [['id'], 'unique'],
            [['marca', 'modelo'], 'unique', 'targetAttribute' => ['marca', 'modelo']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'marca' => 'Marca',
            'modelo' => 'Modelo',
        ];
    }

    /**
     * Genera un ID único basado en marca y modelo
     * @param string $marca
     * @param string $modelo
     * @return string
     */
    public static function generateId($marca, $modelo)
    {
        return strtolower(str_replace(' ', '_', $marca . '_' . $modelo));
    }


    /**
     * Obtiene todos los modelos de una marca específica
     * @param string $marca
     * @return array
     */
    public static function getModelosByMarca($marca)
    {
        return self::find()
            ->select('modelo')
            ->where(['marca' => $marca])
            ->asArray()
            ->all();
    }

    /**
     * Obtiene la representación en string del modelo
     * @return string
     */
    public function __toString()
    {
        return $this->marca . ' ' . $this->modelo;
    }
} 