<?php

namespace common\usecases\telefono_marca_modelo;

use common\models\telefono\TelefonoMarcaModelo;
use Yii;
use yii\base\Exception;

class UpdateTelefonoMarcaModeloUseCase
{
    public function execute(string $id, string $marca, string $modelo): TelefonoMarcaModelo
    {
        $marcaModelo = TelefonoMarcaModelo::findOne($id);
        if ($marcaModelo === null) {
            throw new \RuntimeException('Marca y modelo no encontrado.');
        }
        // Normalizar
        $marcaModelo->marca = TelefonoMarcaModelo::normalizeName($marca);
        $marcaModelo->modelo = TelefonoMarcaModelo::normalizeName($modelo);
        
        // Generar nuevo ID si la marca o modelo cambiaron
        $newId = TelefonoMarcaModelo::generateId($marcaModelo->marca, $marcaModelo->modelo);
        if ($newId !== $id) {
            // Verificar que el nuevo ID no exista
            $existing = TelefonoMarcaModelo::findOne($newId);
            if ($existing !== null) {
                throw new \RuntimeException('Ya existe una combinación de marca y modelo con esos datos.');
            }
            $marcaModelo->id = $newId;
        }

        if ($marcaModelo->validate()) {
            if ($marcaModelo->save()) {
                return $marcaModelo;
            } else {
                throw new Exception('No se pudo actualizar la marca y modelo.');
            }
        } else {
            $errors = $marcaModelo->getErrors();
            throw new \RuntimeException('Error de validación: ' . json_encode($errors));
        }
    }
}
