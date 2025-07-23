<?php

namespace common\usecases\wallet;

use common\models\telefono\TelefonoSocio;
use common\models\telefono\TelefonoSocioWallet;
use common\models\telefono\TelefonoSocioWalletTransaction;
use common\models\telefono\TelefonoSocioWalletTransactionPhoto;
use Yii;
use yii\db\Exception;
use yii\web\NotFoundHttpException;

class AcreditarUseCase
{
    private $telefonoSocioId;
    private $amount;
    private $comment;
    private $photos;

    public function __construct(
        $telefonoSocioId,
        $amount,
        $comment,
        array $photos = []
    ) {
        $this->telefonoSocioId = $telefonoSocioId;
        $this->amount = $amount;
        $this->comment = $comment;
        $this->photos = $photos;
    }

    public function execute()
    {
        $telefonoSocio = TelefonoSocio::findOne($this->telefonoSocioId);
        if ($telefonoSocio === null) {
            throw new NotFoundHttpException("El socio con ID {$this->telefonoSocioId} no existe.");
        }
        
        // Find wallet or create if it doesn't exist.
        $wallet = TelefonoSocioWallet::findOne(['telefono_socio_id' => $this->telefonoSocioId]);

        if ($wallet === null) {
            $wallet = new TelefonoSocioWallet();
            $wallet->telefono_socio_id = $this->telefonoSocioId;
            $wallet->balance = 0;
            if (!$wallet->save()) {
                throw new Exception('Could not create wallet: ' . json_encode($wallet->errors));
            }
        }

        $walletTransaction = new TelefonoSocioWalletTransaction();
        $walletTransaction->wallet_id = $wallet->id;
        $walletTransaction->type = TelefonoSocioWalletTransaction::TYPE_CREDIT;
        $walletTransaction->amount = $this->amount;
        $walletTransaction->comment = $this->comment;
        
        $wallet->balance += $this->amount;
        if (!$wallet->save()) {
            throw new Exception('Could not update wallet balance: ' . json_encode($wallet->errors));
        }

        $walletTransaction->current_balance = $wallet->balance;

        if (!$walletTransaction->save()) {
            throw new Exception('Could not save transaction: ' . json_encode($walletTransaction->errors));
        }

        $uploadDir = 'uploads/socios/' . $this->telefonoSocioId . '/wallet/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($this->photos as $photo) {
            $path = $uploadDir . Yii::$app->security->generateRandomString() . '.' . $photo->extension;
            if ($photo->saveAs($path)) {
                $transactionPhoto = new TelefonoSocioWalletTransactionPhoto();
                $transactionPhoto->transaction_id = $walletTransaction->id;
                $transactionPhoto->path = $path;
                if (!$transactionPhoto->save()) {
                    throw new Exception('Could not save transaction photo: ' . json_encode($transactionPhoto->errors));
                }
            }
        }
        return true;
    }
}
