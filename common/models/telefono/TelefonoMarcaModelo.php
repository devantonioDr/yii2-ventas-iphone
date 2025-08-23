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
     * Normaliza una cadena a Title Case (primera letra mayúscula en cada palabra)
     * y quita espacios extra.
     *
     * @param string $value
     * @return string
     */
    public static function normalizeName(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return '';
        }
        // Convertir todo a minúsculas y luego capitalizar cada palabra de forma multibyte
        $lower = mb_strtolower($trimmed, 'UTF-8');
        return mb_convert_case($lower, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Se asegura de normalizar marca y modelo antes de validar y generar el ID.
     * @return bool
     */
    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        if ($this->marca !== null) {
            $this->marca = self::normalizeName((string)$this->marca);
        }
        if ($this->modelo !== null) {
            $this->modelo = self::normalizeName((string)$this->modelo);
        }

        if (empty($this->id) && $this->marca && $this->modelo) {
            $this->id = self::generateId($this->marca, $this->modelo);
        }

        return true;
    }

    /**
     * Busca por marca y modelo (ignorando mayúsculas/minúsculas) y si no existe lo crea.
     * Devuelve el registro existente o el recién creado.
     *
     * @param string $marca
     * @param string $modelo
     * @return self
     * @throws \yii\base\Exception Si no se puede guardar
     */
    public static function findOrCreate(string $marca, string $modelo): self
    {
        $normalizedMarca = self::normalizeName($marca);
        $normalizedModelo = self::normalizeName($modelo);

        // Búsqueda case-insensitive
        $existing = self::find()
            ->where('LOWER(marca) = :m AND LOWER(modelo) = :mo', [
                ':m' => mb_strtolower($normalizedMarca, 'UTF-8'),
                ':mo' => mb_strtolower($normalizedModelo, 'UTF-8'),
            ])
            ->one();

        if ($existing instanceof self) {
            return $existing;
        }

        $record = new self();
        $record->marca = $normalizedMarca;
        $record->modelo = $normalizedModelo;
        $record->id = self::generateId($record->marca, $record->modelo);

        if (!$record->save()) {
            throw new \yii\base\Exception('No se pudo crear la marca/modelo: ' . implode(', ', $record->getFirstErrors()));
        }

        return $record;
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