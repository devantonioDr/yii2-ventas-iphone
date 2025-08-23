<?php

namespace common\usecases\telefono;

use common\models\telefono\Telefono;
use common\models\telefono\TelefonoMarcaModelo;
use Yii;

/**
 * Caso de uso para editar un teléfono existente
 */
class EditTelefonoUseCase
{
    /**
     * Ejecuta la edición del teléfono
     * 
     * @param int $id ID del teléfono a editar
     * @param string $imei Nuevo IMEI
     * @param string $marca Nueva marca
     * @param string $modelo Nuevo modelo
     * @param float $precioAdquisicion Nuevo precio de adquisición
     * @param float $precioVentaRecomendado Nuevo precio de venta recomendado
     * @param int|null $socioId Nuevo ID del socio (opcional)
     * @param int|null $socioPorcentaje Porcentaje del socio (opcional)
     * @param string $status Estado del teléfono
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function execute($id, $imei, $marca, $modelo, $precioAdquisicion, $precioVentaRecomendado, $socioId = null, $socioPorcentaje = null, $status)
    {
        // Validar que el teléfono existe
        $telefono = Telefono::findOne($id);
        if (!$telefono) {
            throw new \InvalidArgumentException('El teléfono no existe.');
        }

        // Validar que el IMEI no esté duplicado (excepto para el mismo teléfono)
        $existingTelefono = Telefono::find()
            ->where(['imei' => $imei])
            ->andWhere(['!=', 'id', $id])
            ->one();

        if ($existingTelefono) {
            throw new \InvalidArgumentException('El IMEI ya existe en otro teléfono.');
        }

        // Normalizar y asegurar que la combinación marca/modelo exista en el catálogo
        $catalogEntry = TelefonoMarcaModelo::findOrCreate($marca, $modelo);

        // Actualizar el teléfono
        $telefono->imei = $imei;
        $telefono->marca = $catalogEntry->marca;
        $telefono->modelo = $catalogEntry->modelo;
        $telefono->precio_adquisicion = $precioAdquisicion;
        $telefono->precio_venta_recomendado = $precioVentaRecomendado;
        $telefono->socio_id = $socioId;
        $telefono->socio_porcentaje = $socioId ? $socioPorcentaje : null;
        $telefono->status = $status;

        if (!$telefono->save()) {
            throw new \InvalidArgumentException('Error al guardar el teléfono: ' . implode(', ', $telefono->getFirstErrors()));
        }

        return true;
    }

    /**
     * Obtiene un teléfono por ID
     * 
     * @param int $id
     * @return Telefono|null
     */
    public function getTelefono($id)
    {
        return Telefono::findOne($id);
    }
}
