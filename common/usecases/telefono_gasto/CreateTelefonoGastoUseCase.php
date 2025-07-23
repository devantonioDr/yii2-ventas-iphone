<?php

namespace common\usecases\telefono_gasto;

use common\models\telefono\TelefonoGasto;
use Yii;
use yii\base\Exception;

class CreateTelefonoGastoUseCase
{
    public function execute(
        int $telefonoId,
        string $descripcion,
        string $monto_gasto
    ): TelefonoGasto {
        
        $gasto = new TelefonoGasto();
        $gasto->telefono_id = $telefonoId;
        $gasto->descripcion = $descripcion;
        $gasto->monto_gasto = str_replace(',', '', $monto_gasto);

        if ($gasto->validate()) {
            if ($gasto->save()) {
                return $gasto;
            } else {
                throw new Exception('No se pudo guardar el gasto.');
            }
        } else {
            $errors = $gasto->getErrors();
            throw new \RuntimeException('Error de validación: ' . json_encode($errors));
        }
    }
}
