<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use common\models\telefono\Telefono;
use common\models\telefono\TelefonoMarcaModelo;
use common\usecases\telefono\BatchInsertTelefonosUseCase;
use yii\helpers\ArrayHelper;
use common\models\telefono\TelefonoSearch;
use common\models\telefono\TelefonoSocio;
use common\usecases\telefono\EditTelefonoUseCase;

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

        // Si hay una marca seleccionada, obtener los modelos de esa marca
        $modelos = isset($searchModel->marca)
            ? TelefonoMarcaModelo::find()
            ->select('modelo')
            ->where(['marca' => $searchModel->marca])
            ->asArray()
            ->all()
            : [];

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
                    $successCount = $useCase->getSuccessCount();
                    Yii::$app->session->setFlash('success', "Se insertaron {$successCount} teléfonos exitosamente.");
                    return $this->redirect(['/telefono/batch-insert']);
                }
            } catch (\InvalidArgumentException $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', 'Error al procesar el lote de teléfonos: ' . $e->getMessage());
            }
        }

        return $this->render('batch-insert', [
            'model' => $model,
            'marcas' => $marcas,
            'modelos' => $modelos,
            'socios' => $socios,
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
}
