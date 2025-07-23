<?php

namespace common\usecases\socio_pago;

use common\models\telefono\Telefono;
use common\models\telefono\TelefonoSocioPago;
use common\models\telefono\TelefonoSocioPagoPhoto;
use common\services\telefono\GananciaService;
use common\usecases\wallet\AcreditarUseCase;
use yii\db\Exception;

class PagarTelefonoSocioUseCase
{
    private $photoPaths;

    public function __construct(array $photoPaths = [])
    {
        $this->photoPaths = $photoPaths;
    }
    public function execute(int $socioId)
    {
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            if (empty($this->photoPaths)) {
                throw new \Exception("Debe adjuntar al menos una foto del comprobante de pago.");
            }

            $telefonosPendientes = Telefono::find()
                ->where(['socio_id' => $socioId, 'status' => Telefono::STATUS_VENDIDO])
                ->all();

            if (empty($telefonosPendientes)) {
                throw new \Exception("No hay teléfonos pendientes de pago para este socio.");
            }

            $ganancia = GananciaService::calcularTotalGanaciaPendienteSocio($socioId);

            $lastPago = TelefonoSocioPago::find()->orderBy(['id' => SORT_DESC])->one();
            $nextId = $lastPago ? $lastPago->id + 1 : 1;
            $codigoFactura = 'S' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

            $pago = new TelefonoSocioPago();
            $pago->codigo_factura = $codigoFactura;
            $pago->socio_id = $socioId;
            $pago->fecha_pago = date('Y-m-d H:i:s');
            $pago->cantidad_telefonos = count($telefonosPendientes);
            $pago->ganancia_socio = $ganancia['socio'];
            $pago->ganancia_empresa = $ganancia['empresa'];
            $pago->ganancia_neta = $ganancia['neta'];
            $pago->gastos = $ganancia['gastos'];
            $pago->invertido = $ganancia['precioAdquisicion'];

            if (!$pago->save()) {
                throw new Exception('Error al guardar el pago del socio. ' . json_encode($pago->errors));
            }
            if (!empty($this->photoPaths)) {
                foreach ($this->photoPaths as $path) {
                    $pagoPhoto = new TelefonoSocioPagoPhoto();
                    $pagoPhoto->pago_id = $pago->id;
                    $pagoPhoto->path = $path;
                    if (!$pagoPhoto->save()) {
                        throw new Exception('Error al guardar la foto del pago. ' . json_encode($pagoPhoto->errors));
                    }
                }
            }

            foreach ($telefonosPendientes as $telefono) {
                $telefono->status = Telefono::STATUS_PAGADO;
                $telefono->telefono_socio_pago_id = $pago->id;
                if (!$telefono->save()) {
                    throw new Exception('Error al actualizar el estado del teléfono. ' . json_encode($telefono->errors));
                }
            }

            (new AcreditarUseCase(
                $socioId,
                $ganancia['precioAdquisicion'],
                "Pago de teléfonos · Reposición · Inversión ({$pago->codigo_factura})"
            ))->execute();

            $transaction->commit();
            return $pago;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}
