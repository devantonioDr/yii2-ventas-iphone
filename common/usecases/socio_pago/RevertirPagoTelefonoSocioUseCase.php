<?php

namespace common\usecases\socio_pago;

use common\models\telefono\Telefono;
use common\models\telefono\TelefonoSocioPago;
use common\usecases\wallet\DebitarUseCase;
use yii\db\Exception;
use Throwable;

class RevertirPagoTelefonoSocioUseCase
{
    /**
     * @param int $pagoId
     * @return void
     * @throws Exception
     * @throws Throwable
     */
    public function execute(int $pagoId): void
    {
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $pago = TelefonoSocioPago::findOne($pagoId);

            if ($pago === null) {
                throw new \Exception("El pago con ID $pagoId no existe.");
            }

            $telefonos = Telefono::find()
                ->where(['telefono_socio_pago_id' => $pagoId])
                ->all();

            foreach ($telefonos as $telefono) {
                $telefono->status = Telefono::STATUS_VENDIDO;
                $telefono->telefono_socio_pago_id = null;
                if (!$telefono->save()) {
                    throw new Exception('Error al revertir el estado del teléfono. ' . json_encode($telefono->errors));
                }
            }

            if ($pago->delete() === false) {
                throw new Exception('Error al eliminar el registro de pago. ' . json_encode($pago->errors));
            }

            (new DebitarUseCase(
                $pago->socio_id,
                $pago->invertido,
                "Reversión de pago de teléfonos ({$pago->codigo_factura})"
            ))->execute();

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}
