<?php

namespace common\usecases\telefono;

use common\models\telefono\Telefono;
use InvalidArgumentException;

class DeleteInDraftTelefonosUseCase
{
    public function execute($batch_id)
    {
        if (empty($batch_id)) {
            throw new InvalidArgumentException("Batch ID es requerido.");
        }

        Telefono::deleteAll([
            'status' => Telefono::STATUS_IN_DRAFT,
            'batch_id' => $batch_id,
        ]);
    }
}
