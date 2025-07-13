<?php

namespace frontend\controllers;

use common\usecases\telefono\AddTelefonoSocioUseCase;
use common\models\telefono\TelefonoSocio;
use common\models\telefono\TelefonoSocioSearch;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

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

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
}
