<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use common\models\telefono\Telefono;
use common\models\telefono\TelefonoMarcaModelo;
use common\usecases\telefono\BatchInsertTelefonosUseCase;
use common\models\telefono\TelefonoSearch;
use common\models\telefono\TelefonoSocio;
use common\usecases\telefono\EditTelefonoUseCase;
use common\services\telefono\GananciaService;
use common\usecases\telefono\DeleteInDraftTelefonosUseCase;
use common\usecases\telefono\MoveToInventoryUseCase;
use common\usecases\telefono\MarkTelefonoAsVendidoUseCase;

/**
 * TelefonoController implements the CRUD actions for Telefono model.
 */
class TelefonoController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'mark-as-vendido' => ['POST'],
                    'update-precio-adquisicion' => ['POST'],
                    'preview-ganancia' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Telefono models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TelefonoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // Obtener todas las marcas únicas
        $marcas = TelefonoMarcaModelo::find()->select('marca')->distinct()->asArray()->all();

        $modelos = [];
        if (!empty($searchModel->marca)) {
            $modelos = TelefonoMarcaModelo::find()
                ->select('modelo')
                ->where(['marca' => $searchModel->marca])
                ->asArray()
                ->all();
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'marcas' => $marcas,
            'modelos' => $modelos,
        ]);
    }

    /**
     * Batch insert de teléfonos
     * @return mixed
     */
    public function actionBatchInsert()
    {
        $post = Yii::$app->request->post();
        $model = new Telefono();

        // Obtener todas las marcas únicas
        $marcas = TelefonoMarcaModelo::find()->select('marca')->distinct()->asArray()->all();

        // Obtener todos los socios
        $socios = TelefonoSocio::find()->asArray()->all();

        // Obtener los modelos de una marca específica
        $modelos = isset($post['Telefono']['marca'])
            ? TelefonoMarcaModelo::find()
            ->select('modelo')
            ->where(['marca' => $post['Telefono']['marca']])
            ->asArray()
            ->all()
            : [];

        if (Yii::$app->request->isPost) {
            // Cargar los datos del formulario en el modelo para preservarlos
            if (isset($post['Telefono'])) {
                $model->load($post);
            }

            $transaction = Yii::$app->db->beginTransaction();

            try {
                $useCase = new BatchInsertTelefonosUseCase();

                $result = $useCase->execute(
                    $model->marca,
                    $model->modelo,
                    $model->precio_adquisicion,
                    $model->precio_venta_recomendado,
                    $model->imeisStringToArray(),
                    $model->socio_id,
                    $model->socio_porcentaje
                );

                if ($result) {
                    $transaction->commit();
                    $successCount = $useCase->getSuccessCount();
                    Yii::$app->session->setFlash('success', "Se insertaron {$successCount} teléfonos exitosamente.");
                    return $this->redirect(['/telefono/batch-insert']);
                }
            } catch (\InvalidArgumentException $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Error al procesar el lote de teléfonos: ' . $e->getMessage());
            }
        }

        $inDraftSummary = GananciaService::getGananciaSummaryInDraft();

        return $this->render('batch-insert', [
            'model' => $model,
            'marcas' => $marcas,
            'modelos' => $modelos,
            'socios' => $socios,
            'inDraftSummary' => $inDraftSummary,
        ]);
    }

    /**
     * Edita un teléfono existente
     * @param int $id
     * @return mixed
     */
    public function actionEdit($id)
    {
        $useCase = new EditTelefonoUseCase();
        $telefono = $useCase->getTelefono($id);

        if (!$telefono) {
            Yii::$app->session->setFlash('error', 'El teléfono no existe.');
            return $this->redirect(['index']);
        }

        // Obtener todas las marcas únicas
        $marcas = TelefonoMarcaModelo::find()->select('marca')->distinct()->asArray()->all();

        // Obtener los modelos de la marca actual del teléfono
        $modelos = TelefonoMarcaModelo::find()
            ->select('modelo')
            ->where(['marca' => $telefono->marca])
            ->asArray()
            ->all();

        // Obtener todos los socios
        $socios = TelefonoSocio::find()->asArray()->all();

        // Preparar datos para el desglose de ganancias
        $gastos = [];
        foreach ($telefono->telefonoGastos as $gasto) {
            $gastos[] = [
                'descripcion' => $gasto->descripcion,
                'monto_gasto' => $gasto->monto_gasto,
            ];
        }

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            try {
                $result = $useCase->execute(
                    $id,
                    $post['Telefono']['imei'],
                    $post['Telefono']['marca'],
                    $post['Telefono']['modelo'],
                    $post['Telefono']['precio_adquisicion'],
                    $post['Telefono']['precio_venta_recomendado'],
                    $post['Telefono']['socio_id'] ?? null,
                    $post['Telefono']['socio_porcentaje'] ?? null,
                    $post['Telefono']['status']
                );

                if ($result) {
                    Yii::$app->session->setFlash('success', 'Teléfono actualizado exitosamente.');
                    return $this->redirect(['edit', 'id' => $id]);
                }
            } catch (\InvalidArgumentException $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', 'Error al actualizar el teléfono: ' . $e->getMessage());
            }
        }

        return $this->render('edit', [
            'telefono' => $telefono,
            'marcas' => $marcas,
            'modelos' => $modelos,
            'socios' => $socios,
            'gastos' => $gastos,
        ]);
    }



    /**
     * Obtiene los modelos de una marca específica via AJAX
     * @return array
     */
    public function actionGetModelosByMarca($marca)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $modelos = TelefonoMarcaModelo::find()
            ->select('modelo')
            ->where(['marca' => $marca])
            ->asArray()
            ->all();
        if ($modelos) return ['success' => true, 'modelos' => $modelos];

        return ['success' => false, 'modelos' => []];
    }


    public function actionGetDesglose($id)
    {
        $telefono = Telefono::findOne($id);
        if (!$telefono) {
            return $this->asJson(['success' => false, 'error' => 'Teléfono no encontrado']);
        }
        $gastos = [];
        foreach ($telefono->telefonoGastos as $gasto) {
            $gastos[] = [
                'descripcion' => $gasto->descripcion,
                'monto_gasto' => $gasto->monto_gasto,
            ];
        }
        $html = $this->renderPartial('_desglose-content', [
            'telefono' => $telefono,
            'gastos' => $gastos,
        ]);
        return $this->asJson(['success' => true, 'html' => $html]);
    }

    public function actionDeleteInDraft($batch_id)
    {
        try {
            $useCase = new DeleteInDraftTelefonosUseCase();
            $useCase->execute($batch_id);
            Yii::$app->session->setFlash('success', 'Se eliminó el grupo de teléfonos en borrador.');
        } catch (\InvalidArgumentException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Ocurrió un error al eliminar los teléfonos: ' . $e->getMessage());
        }

        return $this->redirect(['batch-insert']);
    }

    public function actionMoveToInventory()
    {
        $suplidor = Yii::$app->request->post('suplidor');
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $useCase = new MoveToInventoryUseCase();
            $useCase->execute($suplidor);
            $transaction->commit();
            Yii::$app->session->setFlash('success', 'Los teléfonos se movieron al inventario exitosamente.');
        } catch (\InvalidArgumentException $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Ocurrió un error al mover los teléfonos al inventario: ' . $e->getMessage());
        }

        return $this->redirect(['batch-insert']);
    }

    public function actionUpdatePrecioAdquisicion($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $precio = (float)Yii::$app->request->post('precio_adquisicion');
        if ($precio <= 0) {
            return ['success' => false, 'message' => 'El precio de adquisición debe ser mayor a 0.'];
        }

        $telefono = \common\models\telefono\Telefono::findOne((int)$id);
        if (!$telefono) {
            return ['success' => false, 'message' => 'Teléfono no encontrado'];
        }

        // No persistimos; solo calculamos usando el precio temporal
        $precioOriginal = $telefono->precio_adquisicion;
        $telefono->precio_adquisicion = $precio;
        $ganancia = GananciaService::calcular($telefono);
        // Restaurar en memoria (no estrictamente necesario)
        $telefono->precio_adquisicion = $precioOriginal;

        return [
            'success' => true,
            'ganancia' => $ganancia,
            'precio_adquisicion' => $precio,
            'precio_venta_recomendado' => $telefono->precio_venta_recomendado,
        ];
    }

    public function actionPreviewGanancia($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $precioVenta = (float)Yii::$app->request->post('precio_venta');
        if ($precioVenta <= 0) {
            return ['success' => false, 'message' => 'El precio de venta debe ser mayor a 0.'];
        }

        $telefono = Telefono::findOne((int)$id);
        if (!$telefono) {
            return ['success' => false, 'message' => 'Teléfono no encontrado'];
        }

        // Calcular sin persistir
        $precioOriginal = $telefono->precio_venta_recomendado;
        $telefono->precio_venta_recomendado = $precioVenta;
        $ganancia = GananciaService::calcular($telefono);
        $telefono->precio_venta_recomendado = $precioOriginal;

        return [
            'success' => true,
            'ganancia' => $ganancia,
            'precio_venta' => $precioVenta,
            'precio_adquisicion' => $telefono->precio_adquisicion,
            'gastos' => $telefono->getTotalGastos(),
            'costo_total' => $telefono->getCostoTotal(),
        ];
    }

    /**
     * Devuelve el HTML del modal de venta para un teléfono (AJAX)
     * @param int $id
     * @return string
     */
    public function actionSellModal($id)
    {
        $telefono = Telefono::findOne((int)$id);
        if (!$telefono) {
            return $this->renderContent('<div class="alert alert-danger">Teléfono no encontrado.</div>');
        }

        $ganancia = GananciaService::calcular($telefono);
        return $this->renderPartial('_sell-modal-ajax', [
            'telefono' => $telefono,
            'ganancia' => $ganancia,
            'costo_total' => $telefono->getCostoTotal(),
        ]);
    }

    public function actionMarkAsVendido($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $useCase = new MarkTelefonoAsVendidoUseCase();
            $precioVenta = (float)Yii::$app->request->post('precio_venta');
            $useCase->execute((int)$id, $precioVenta);
            $transaction->commit();
            return ['success' => true, 'message' => 'Teléfono marcado como vendido'];
        } catch (\InvalidArgumentException $e) {
            $transaction->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return ['success' => false, 'message' => 'Error al marcar como vendido'];
        }
    }
}
