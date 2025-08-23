<?php

namespace common\usecases\telefono_marca_modelo;

use common\models\telefono\TelefonoMarcaModelo;
use common\models\telefono\Telefono;
use yii\base\Exception;

class DeleteTelefonoMarcaModeloUseCase
{
    public function execute(string $id): void
    {
        $marcaModelo = TelefonoMarcaModelo::findOne($id);
        if ($marcaModelo === null) {
            throw new \RuntimeException('Marca y modelo no encontrado.');
        }

        // Verificar que no haya teléfonos usando esta marca y modelo
        $telefonosCount = Telefono::find()
            ->where(['marca' => $marcaModelo->marca, 'modelo' => $marcaModelo->modelo])
            ->count();
            
        if ($telefonosCount > 0) {
            throw new \RuntimeException('No se puede eliminar porque existen teléfonos con esta marca y modelo.');
        }

        if ($marcaModelo->delete() === false) {
            throw new Exception('No se pudo eliminar la marca y modelo.');
        }
    }
}
