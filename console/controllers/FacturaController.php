<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\telefono\TelefonoCompra;
use common\models\telefono\Telefono;

/**
 * Manages invoices.
 */
class FacturaController extends Controller
{
    /**
     * Generates invoice codes for purchases that don't have one.
     */
    public function actionGenerateMissingCodes()
    {
        $compras = TelefonoCompra::find()->where(['codigo_factura' => null])->all();
        $count = 0;
        foreach ($compras as $compra) {
            $compra->codigo_factura = str_pad($compra->id, 6, '0', STR_PAD_LEFT);
            if ($compra->save(false, ['codigo_factura'])) {
                $count++;
            }
        }
        $this->stdout("Successfully generated codes for $count purchases.\n");
    }

    /**
     * Creates invoices for phones that are not associated with any purchase.
     * It groups phones by supplier and entry date.
     */
    public function actionCreateInvoicesForOrphanedPhones()
    {
        $orphanTelefonos = Telefono::find()
            ->where(['telefono_compra_id' => null])
            ->orderBy(['fecha_ingreso' => SORT_ASC])
            ->all();

        if (empty($orphanTelefonos)) {
            $this->stdout("No orphaned phones found.\n");
            return;
        }

        // Group by date (ignoring time)
        $groupedTelefonos = [];
        foreach ($orphanTelefonos as $telefono) {
            $date = date('Y-m-d', strtotime($telefono->fecha_ingreso));
            $groupedTelefonos[$date][] = $telefono;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $createdCount = 0;
            foreach ($groupedTelefonos as $date => $telefonos) {

                $compra = new TelefonoCompra();
                $compra->fecha_compra = $date;
                $compra->suplidor = 'N/A';
                
                if ($compra->save()) {
                    $createdCount++;
                    $telefonoIds = \yii\helpers\ArrayHelper::getColumn($telefonos, 'id');
                    Telefono::updateAll(['telefono_compra_id' => $compra->id], ['id' => $telefonoIds]);
                }
            }
            $transaction->commit();
            $this->stdout("Successfully created $createdCount new purchases for orphaned phones.\n");

        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->stderr("An error occurred: " . $e->getMessage() . "\n");
        }
    }
} 