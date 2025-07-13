<?php

namespace common\usecases\telefono;

use common\models\telefono\TelefonoSocio;

/**
 * Use case para añadir un nuevo socio de teléfono
 */
class AddTelefonoSocioUseCase
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
    }
}
