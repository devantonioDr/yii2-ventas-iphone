<?php

namespace common\usecases\telefono_gasto;

use common\models\telefono\TelefonoGasto;
use Yii;
use yii\base\Exception;

class UpdateTelefonoGastoUseCase
{
    public function execute(int $gastoId, string $descripcion, string $monto_gasto): TelefonoGasto
    {
        $gasto = TelefonoGasto::findOne($gastoId);
        if ($gasto === null) {
            throw new \RuntimeException('Gasto no encontrado.');
        }
        
        $gasto->descripcion = $descripcion;
        $gasto->monto_gasto = str_replace(',', '', $monto_gasto);

        if ($gasto->validate()) {
            if ($gasto->save()) {
                return $gasto;
            } else {
                throw new Exception('No se pudo actualizar el gasto.');
            }
        } else {
            $errors = $gasto->getErrors();
            throw new \RuntimeException('Error de validación: ' . json_encode($errors));
        }
    }
} 