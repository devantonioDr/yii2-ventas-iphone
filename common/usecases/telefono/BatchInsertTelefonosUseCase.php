<?php

namespace common\usecases\telefono;

use Yii;
use common\models\telefono\Telefono;
use common\models\telefono\TelefonoSocio;

/**
 * UseCase para insertar lotes de teléfonos
 */
class BatchInsertTelefonosUseCase
{
    /**
     * @var int
     */
    private $successCount = 0;

    /**
     * Ejecuta el batch insert de teléfonos
     * 
     * @param string $marca
     * @param string $modelo
     * @param float $precioAdquisicion
     * @param float $precioVentaRecomendado
     * @param array $imeis
     * @param int|null $socioId
     * @param float $socioPorcentaje
     * @return bool
     * @throws \Exception
     */
    public function execute(
        $marca,
        $modelo,
        $precioAdquisicion,
        $precioVentaRecomendado,
        $imeis,
        $socioId,
        $socioPorcentaje
    ) {
        $transaction = Yii::$app->db->beginTransaction();

        $this->findSocio($socioId);

        try {
            foreach ($imeis as $imei) {
                $imei = trim($imei);
                if (empty($imei)) continue;
                $this->insertTelefono(
                    $imei,
                    $marca,
                    $modelo,
                    $precioAdquisicion,
                    $precioVentaRecomendado,
                    $socioId,
                    $socioPorcentaje
                );
                $this->successCount++;
            }
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    private function findSocio($socioId): ?TelefonoSocio
    {
        if (!$socioId) return null;
        $socio = TelefonoSocio::find()->where(['id' => $socioId])->one();
        if (!$socio) throw new \Exception("Socio {$socioId}: No existe en la base de datos.");
        return $socio;
    }

    /**
     * Inserta un teléfono individual
     * 
     * @param string $imei
     * @param string $marca
     * @param string $modelo
     * @param float $precioAdquisicion
     * @param float $precioVentaRecomendado
     * @param TelefonoSocio|null $socio
     * @throws \Exception
     */
    private function insertTelefono(
        $imei,
        $marca,
        $modelo,
        $precioAdquisicion,
        $precioVentaRecomendado,
        $socioId,
        $socioPorcentaje
    ) {
        // Validar formato IMEI
        if (!preg_match('/^[0-9]{15}$/', $imei)) {
            throw new \Exception("IMEI {$imei}: Formato inválido (debe tener 15 dígitos).");
        }

        // Verificar si el IMEI ya existe
        if (Telefono::find()->where(['imei' => $imei])->exists()) {
            throw new \Exception("IMEI {$imei}: Ya existe en la base de datos.");
        }

        $telefono = new Telefono();
        $telefono->imei = $imei;
        $telefono->marca = $marca;
        $telefono->modelo = $modelo;
        $telefono->precio_adquisicion = $precioAdquisicion;
        $telefono->precio_venta_recomendado = $precioVentaRecomendado;
        if ($socioId) {
            $telefono->socio_id = (int) $socioId;
            $telefono->socio_porcentaje = (float) $socioPorcentaje;
        }



        if (!$telefono->save()) {
            throw new \Exception("IMEI {$imei}: " . implode(', ', $telefono->getFirstErrors()));
        }
    }

    /**
     * Obtiene el número de teléfonos insertados exitosamente
     * 
     * @return int
     */
    public function getSuccessCount()
    {
        return $this->successCount;
    }
}
