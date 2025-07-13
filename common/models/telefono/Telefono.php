<?php

namespace common\models\telefono;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "telefono".
 *
 * @property int $id
 * @property string $imei
 * @property string $marca
 * @property string $modelo
 * @property int $precio_adquisicion
 * @property int $precio_venta_recomendado
 * @property string $fecha_ingreso
 * @property int|null $socio_id
 * @property TelefonoSocio|null $socio
 * @property float $socio_porcentaje
 * @property string $status
 */
class Telefono extends ActiveRecord
{
    const STATUS_EN_TIENDA = 'en_tienda';
    const STATUS_VENDIDO = 'vendido';

    /**
     * @var string|null IMEIs como string (para el formulario)
     */
    public $imeis_string = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%telefono}}';
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['fecha_ingreso'],
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
            [['imei', 'marca', 'modelo', 'precio_adquisicion', 'precio_venta_recomendado'], 'required'],
            // [[], 'number', 'min' => 0],
            [['fecha_ingreso','precio_adquisicion', 'precio_venta_recomendado'], 'safe'],
            [['socio_id'], 'integer'],
            [['imei'], 'string', 'max' => 255],
            [['marca', 'modelo'], 'string', 'max' => 100],
            [['imei'], 'unique'],
            [['imei'], 'match', 'pattern' => '/^[0-9]{15}$/', 'message' => 'El IMEI debe tener exactamente 15 dígitos numéricos.'],
            [['socio_porcentaje'], 'number', 'min' => 0, 'max' => 100],
            [['status'], 'string'],
            [['status'], 'in', 'range' => [self::STATUS_EN_TIENDA, self::STATUS_VENDIDO]],
            [['status'], 'default', 'value' => self::STATUS_EN_TIENDA],
            [['imeis_string'], 'safe'],
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
            'imei' => 'IMEI',
            'marca' => 'Marca',
            'modelo' => 'Modelo',
            'precio_adquisicion' => 'Precio de Adquisición',
            'precio_venta_recomendado' => 'Precio de Venta Recomendado',
            'fecha_ingreso' => 'Fecha de Ingreso',
            'imeis_string' => 'IMEIs',
            'socio_id' => 'Socio',
            'socio_porcentaje' => 'Porcentaje del Socio',
            'status' => 'Estado',
        ];
    }

    /**
     * Devuelve las opciones de estado para un DropDownList
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_EN_TIENDA => 'En tienda',
            self::STATUS_VENDIDO => 'Vendido',
        ];
    }

    /**
     * Convierte el string de IMEIs en un array
     * @return array
     */
    public function imeisStringToArray()
    {
        if (empty($this->imeis_string)) {
            return [];
        }

        // Dividir por líneas y limpiar cada IMEI
        $imeis = explode("\n", $this->imeis_string);
        $imeis = array_map('trim', $imeis);

        // Remover duplicados manteniendo el orden
        return array_values(array_unique($imeis));
    }

    /**
     * Obtiene el margen de ganancia en porcentaje
     * @return float
     */
    public function getMargenGanancia()
    {
        if ($this->precio_adquisicion <= 0) {
            return 0;
        }

        $margen = (($this->precio_venta_recomendado - $this->precio_adquisicion) / $this->precio_adquisicion) * 100;
        return round($margen, 2);
    }

    /**
     * Obtiene el margen de ganancia absoluto en RD$
     * @return float
     */
    public function getMargenGananciaAbsoluto()
    {
        return $this->precio_venta_recomendado - $this->precio_adquisicion;
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
     * Gets query for [[TelefonoGastos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTelefonoGastos()
    {
        return $this->hasMany(TelefonoGasto::class, ['telefono_id' => 'id']);
    }

    /**
     * Obtiene el total de gastos del teléfono
     * @return float
     */
    public function getTotalGastos()
    {
        return $this->getTelefonoGastos()->sum('monto_gasto') ?: 0;
    }

    /**
     * Obtiene el costo total (adquisición + gastos)
     * @return float
     */
    public function getCostoTotal()
    {
        return $this->precio_adquisicion + $this->getTotalGastos();
    }

    /**
     * Obtiene el margen de ganancia actualizado en porcentaje
     * @return float
     */
    public function getMargenGananciaActualizado()
    {
        $costoTotal = $this->getCostoTotal();
        if ($costoTotal <= 0) {
            return 0;
        }

        $margen = (($this->precio_venta_recomendado - $costoTotal) / $costoTotal) * 100;
        return round($margen, 2);
    }
}
