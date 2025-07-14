<?php

namespace frontend\controllers;

use common\usecases\telefono\AddTelefonoSocioUseCase;
use common\models\telefono\TelefonoSocio;
use common\models\telefono\TelefonoSocioSearch;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\services\telefono\GananciaService;
use yii\web\NotFoundHttpException;
use common\models\telefono\Telefono;

/**
 * TelefonoSocioController maneja las operaciones relacionadas con socios de teléfonos
 */
class TelefonoSocioController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Muestra la lista de socios con GridView
     */
    public function actionIndex()
    {
        $searchModel = new TelefonoSocioSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $ganciasSocios = [];

        $socios = $dataProvider->getModels();

        foreach ($socios as $socio) {
            $ganancia = GananciaService::calcularTotalGanaciaPendienteSocio($socio->id);
            $ganciasSocios[$socio->id] = $ganancia;
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'ganciasSocios' => $ganciasSocios,
        ]);
    }

    /**
     * Crea un nuevo socio
     */
    public function actionCreate()
    {
        $post = Yii::$app->request->post();
        $model = new TelefonoSocio();

        if (Yii::$app->request->isPost) {
            try {
                $model->load($post);
                $useCase = new AddTelefonoSocioUseCase();
                $useCase->execute(
                    $post['TelefonoSocio']['nombre'],
                    (float) $post['TelefonoSocio']['margen_ganancia']
                );
                Yii::$app->session->setFlash('success', 'Socio creado exitosamente');
                return $this->redirect(['index']);
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Socio actualizado exitosamente');
            return $this->redirect(['index']);
        }

        $ganancia = GananciaService::calcularTotalGanaciaPendienteSocio($id);

        return $this->render('update', [
            'model' => $model,
            'ganancia' => $ganancia,
        ]);
    }


    public function actionPagar($id)
    {
        $model = $this->findModel($id);
        $ganancia = GananciaService::calcularTotalGanaciaPendienteSocio($id);
        $montoTotalAPagar = $ganancia['precioAdquisicion'] + $ganancia['socio'];

        $telefonosPendientes = Telefono::find()
            ->where(['socio_id' => $id, 'status' => Telefono::STATUS_VENDIDO])
            ->all();

        if (Yii::$app->request->isPost) {
            // Aquí se ejecutará el UseCase para procesar el pago
            Yii::$app->session->setFlash('success', 'El pago se ha procesado exitosamente (simulación).');
            return $this->redirect(['index']);
        }

        return $this->render('pagar', [
            'model' => $model,
            'ganancia' => $ganancia,
            'montoTotalAPagar' => $montoTotalAPagar,
            'telefonos' => $telefonosPendientes,
        ]);
    }


    /**
     * Obtiene la información de un socio específico via AJAX
     * @return array
     */
    public function actionGetSocioInfo($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $socio = TelefonoSocio::find()
            ->select(['id', 'nombre', 'margen_ganancia'])
            ->where(['id' => $id])
            ->asArray()
            ->one();

        if ($socio) {
            return [
                'success' => true,
                'socio' => $socio
            ];
        }

        return [
            'success' => false,
            'message' => 'Socio no encontrado'
        ];
    }

    /**
     * Finds the TelefonoSocio model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TelefonoSocio the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TelefonoSocio::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
