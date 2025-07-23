<?php

namespace common\models\telefono;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "telefono".
 *
 * @property int $id
 * @property string|null $batch_id
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
 * @property int|null $telefono_compra_id
 * @property TelefonoCompra|null $telefonoCompra
 */
class Telefono extends ActiveRecord
{
    // status inicial del telefono cuando se ingresa a la tienda
    const STATUS_EN_TIENDA = 'en_tienda';

    // vendido en la tienda pero no se ha pagado a el socio
    const STATUS_VENDIDO = 'VENDIDO';

    // status inicial cuando aun se esta digitando el telefono
    const STATUS_IN_DRAFT = 'in_draft';

    const STATUS_DEVUELTO = 'DEVUELTO';
    const STATUS_PAGADO = 'PAGADO';

    /**
     * @var string|null IMEIs como string (para el formulario)
     */
    public $imeis_string = null;

    public $total_gastos;
    public $ganancia;

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
            [['imei', 'marca', 'modelo', 'precio_adquisicion', 'precio_venta_recomendado', 'status'], 'required'],
            [['socio_porcentaje'], 'number'],
            [['socio_id', 'telefono_compra_id'], 'integer'],
            [['batch_id','precio_adquisicion', 'precio_venta_recomendado','imeis_string'], 'safe'],
            [['imei', 'marca', 'modelo', 'status'], 'string', 'max' => 255],
            [['batch_id'], 'string', 'max' => 36],
            [['imei'], 'unique'],
            ['status', 'in', 'range' => [self::STATUS_IN_DRAFT, self::STATUS_EN_TIENDA, self::STATUS_VENDIDO, self::STATUS_DEVUELTO, self::STATUS_PAGADO]],
            [['socio_id'], 'exist', 'skipOnError' => true, 'targetClass' => TelefonoSocio::class, 'targetAttribute' => ['socio_id' => 'id']],
            [['telefono_compra_id'], 'exist', 'skipOnError' => true, 'targetClass' => TelefonoCompra::class, 'targetAttribute' => ['telefono_compra_id' => 'id']],
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
            self::STATUS_PAGADO => 'Pagado',
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
     * Gets query for [[TelefonoCompra]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTelefonoCompra()
    {
        return $this->hasOne(TelefonoCompra::class, ['id' => 'telefono_compra_id']);
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
