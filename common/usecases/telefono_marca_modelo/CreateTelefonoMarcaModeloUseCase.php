<?php

namespace common\usecases\telefono_marca_modelo;

use common\models\telefono\TelefonoMarcaModelo;
use Yii;
use yii\base\Exception;

class CreateTelefonoMarcaModeloUseCase
{
    public function execute(
        string $marca,
        string $modelo
    ): TelefonoMarcaModelo {
        // Normalizar a Title Case y crear si no existe
        return TelefonoMarcaModelo::findOrCreate($marca, $modelo);
    }
}
