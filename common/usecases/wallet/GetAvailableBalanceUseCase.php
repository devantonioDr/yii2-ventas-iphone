<?php

namespace common\usecases\wallet;

use common\models\telefono\TelefonoSocio;
use common\models\telefono\TelefonoSocioWallet;
use yii\web\NotFoundHttpException;

class GetAvailableBalanceUseCase
{
    private $telefonoSocioId;

    public function __construct($telefonoSocioId)
    {
        $this->telefonoSocioId = $telefonoSocioId;
    }

    public function execute()
    {
        $telefonoSocio = TelefonoSocio::findOne($this->telefonoSocioId);
        if ($telefonoSocio === null) {
            throw new NotFoundHttpException("El socio con ID {$this->telefonoSocioId} no existe.");
        }
        $wallet = $telefonoSocio->wallet;

        if ($wallet === null) {
            return 0;
        }

        return $wallet->balance;
    }
} 