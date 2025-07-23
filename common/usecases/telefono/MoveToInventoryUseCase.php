<?php

namespace common\usecases\telefono;

use common\models\telefono\Telefono;
use common\models\telefono\TelefonoCompra;
use common\models\telefono\TelefonoSocio;
use common\models\telefono\TelefonoSocioWallet;
use common\usecases\wallet\DebitarUseCase;
use yii\db\Expression;
use InvalidArgumentException;
use Yii;

class MoveToInventoryUseCase
{
    public function execute($suplidor)
    {
        if (empty($suplidor)) {
            throw new InvalidArgumentException("El suplidor es requerido.");
        }

        $telefonos_in_draft = Telefono::findAll(['status' => Telefono::STATUS_IN_DRAFT]);

        if (empty($telefonos_in_draft)) {
            throw new InvalidArgumentException("No se encontraron teléfonos en borrador para mover.");
        }

        $indexTotalPrecioAdquisicionSocios = $this->indexTotalPrecioAdquisicionSocios($telefonos_in_draft);
        $indexByIdSocios = $this->indexSociosById(TelefonoSocio::find()->all());    
        $indexByIdWallets = $this->indexWalletsBySocioId(TelefonoSocioWallet::find()->all());

        // var_dump($indexTotalPrecioAdquisicionSocios);
        // var_dump($indexByIdSocios);

        // Check if the total price of the phones is grater than the balance of the socios  
        foreach ($indexTotalPrecioAdquisicionSocios as $socio_id => $total_precio_adquisicion) {
            if(!isset($indexByIdWallets[$socio_id])){
                throw new InvalidArgumentException("El socio {$indexByIdSocios[$socio_id]['nombre']} no tiene saldo en la cuenta.");
            }
            if($total_precio_adquisicion >= $indexByIdWallets[$socio_id]){
                $total_precio = number_format($total_precio_adquisicion, 2, '.');
                $balance = number_format($indexByIdWallets[$socio_id], 2, '.');
                throw new InvalidArgumentException("El saldo de la cuenta del socio {$indexByIdSocios[$socio_id]['nombre']} no es suficiente para comprar los teléfonos. Se necesitan {$total_precio} y solo hay {$balance}");
            }
        }

        $compra = new TelefonoCompra([
            'fecha_compra' => new Expression('NOW()'),
            'suplidor' => $suplidor,
        ]);

        if (!$compra->save()) {
            throw new \Exception('Error al guardar el registro de compra.');
        }

        #Debitar a cada socio el total de la compra
        foreach ($indexTotalPrecioAdquisicionSocios as $socio_id => $total_precio_adquisicion) {
            $debitarUseCase = new DebitarUseCase(
                $socio_id, 
                $total_precio_adquisicion,
                "Compra - {$compra->codigo_factura} - {$suplidor}"
            );
            $debitarUseCase->execute();
        }

        // Move phones from IN_DRAFT to IN_STOCK
        foreach ($telefonos_in_draft as $telefono) {
            $telefono->status = Telefono::STATUS_EN_TIENDA;
            $telefono->telefono_compra_id = $compra->id;
            if (!$telefono->save()) {
                throw new \Exception('Error al actualizar el estado de los teléfonos.');
            }
        }

        return true;
    }

    private function indexSociosById($socios){
        $socios_array = [];
        foreach ($socios as $socio) {
            $socios_array[$socio->id] = $socio;
        }
        return $socios_array;
    }

    private function indexWalletsBySocioId($wallets){
        $balances = [];
        foreach ($wallets as $wallet) {
            $balances[$wallet->telefono_socio_id] = $wallet->balance;
        }
        return $balances;
    }

    private function indexTotalPrecioAdquisicionSocios($telefonos_in_draft){
        $total_precio_adquisicion_socios = [];

        foreach ($telefonos_in_draft as $telefono) {
            if(!$telefono->socio_id) continue;
            if(!isset($total_precio_adquisicion_socios[$telefono->socio_id])){
                $total_precio_adquisicion_socios[$telefono->socio_id] = 0;
            }
            $total_precio_adquisicion_socios[$telefono->socio_id] += $telefono->precio_adquisicion;
        }

        return $total_precio_adquisicion_socios;
    }
} 