<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\models\telefono\TelefonoCompraSearch;
use common\models\telefono\TelefonoCompra;
use yii\web\NotFoundHttpException;
use common\services\compra\CompraService;
use yii\data\ActiveDataProvider;

/**
 * TelefonoCompraController implements the actions for TelefonoCompra model.
 */
class TelefonoCompraController extends Controller
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
     * Lists all TelefonoCompra models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TelefonoCompraSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TelefonoCompra model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $summary = CompraService::getSummary($model);

        $telefonosDataProvider = new ActiveDataProvider([
            'query' => $model->getTelefonos()->with('socio'),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('view', [
            'model' => $model,
            'summary' => $summary,
            'telefonosDataProvider' => $telefonosDataProvider,
        ]);
    }

    /**
     * Finds the TelefonoCompra model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TelefonoCompra the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TelefonoCompra::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
} 