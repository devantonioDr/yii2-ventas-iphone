<?php

namespace common\usecases\socio;

use common\models\telefono\TelefonoSocio;
use common\models\telefono\TelefonoSocioWallet;

/**
 * Use case para añadir un nuevo socio de teléfono
 */
class CreateSocioUseCase
{
    /**
     * Ejecuta el use case para crear un nuevo socio
     * 
     * @param string $nombre Nombre del socio
     * @param float $margenGanancia Margen de ganancia en porcentaje
     * @return array Resultado de la operación
     */
    public function execute(string $nombre, float $margenGanancia)
    {

        // Crear el nuevo socio
        $socio = new TelefonoSocio();
        $socio->nombre = $nombre;
        $socio->margen_ganancia = $margenGanancia;
        if (!$socio->save()) {
            throw new \Exception("Error al guardar el socio: " . implode(', ', $socio->getFirstErrors()));
        }
        $wallet = TelefonoSocioWallet::find()->where(['socio_id' => $socio->id])->one();
        if (!$wallet) {
            $wallet = new TelefonoSocioWallet();
            $wallet->socio_id = $socio->id;
            $wallet->current_balance = 0;
            $wallet->save();
            if (!$wallet->save()) {
                throw new \Exception("Error al guardar el saldo inicial del socio: " . implode(', ', $wallet->getFirstErrors()));
            }
        }
    }
}
