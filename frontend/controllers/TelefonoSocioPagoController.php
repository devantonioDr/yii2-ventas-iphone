<?php

namespace frontend\controllers;

use common\models\telefono\TelefonoSocioPago;
use common\models\telefono\TelefonoSocioPagoSearch;
use common\usecases\socio_pago\RevertirPagoTelefonoSocioUseCase;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class TelefonoSocioPagoController extends Controller
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        $searchModel = new TelefonoSocioPagoSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionRevertir($id)
    {
        $useCase = new RevertirPagoTelefonoSocioUseCase();
        try {
            $useCase->execute($id);
            Yii::$app->session->setFlash('success', 'Pago revertido exitosamente.');
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Error al revertir el pago: ' . $e->getMessage());
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = TelefonoSocioPago::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
} 