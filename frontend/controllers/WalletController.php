<?php

namespace frontend\controllers;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\telefono\TelefonoSocio;
use common\usecases\wallet\AcreditarUseCase;
use common\usecases\wallet\DebitarUseCase;
use common\usecases\wallet\GetAvailableBalanceUseCase;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use common\models\telefono\TelefonoSocioWalletTransaction;


class WalletController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'credit' => ['post'],
                    'debit' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex($socio_id)
    {
        $socio = $this->findSocio($socio_id);
        $wallet = $socio->wallet;

        $dataProvider = new ActiveDataProvider([
            'query' => TelefonoSocioWalletTransaction::find()->where(['wallet_id' => $wallet ? $wallet->id : null]),
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]],
        ]);

        return $this->render('index', [
            'socio' => $socio,
            'wallet' => $wallet,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findTransaction($id),
        ]);
    }

    public function actionCredit($socio_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $socio = $this->findSocio($socio_id);
        $amount = Yii::$app->request->post('amount');
        $comment = Yii::$app->request->post('comment');
        $photos = UploadedFile::getInstancesByName('photos');

        $transaction = Yii::$app->db->beginTransaction();
        try {
            (new AcreditarUseCase($socio->id, $amount, $comment, $photos))->execute();
            $transaction->commit();
            return ['success' => true, 'message' => 'Credit successful.'];
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->response->statusCode = 400;
            return ['success' => false, 'message' => 'Error crediting: ' . $e->getMessage()];
        }
    }

    public function actionDebit($socio_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $socio = $this->findSocio($socio_id);
        $amount = Yii::$app->request->post('amount');
        $comment = Yii::$app->request->post('comment');
        $photos = UploadedFile::getInstancesByName('photos');

        $transaction = Yii::$app->db->beginTransaction();
        try {
            (new DebitarUseCase($socio->id, $amount, $comment, $photos))->execute();
            $transaction->commit();
            return ['success' => true, 'message' => 'Debit successful.'];
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->response->statusCode = 400;
            return ['success' => false, 'message' => 'Error debiting: ' . $e->getMessage()];
        }
    }

    public function actionGetBalance($socio_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $balance = (new GetAvailableBalanceUseCase($socio_id))->execute();

        return ['success' => true, 'balance' => Yii::$app->formatter->asCurrency($balance)];
    }

    protected function findSocio($id)
    {
        if (($model = TelefonoSocio::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findTransaction($id)
    {
        if (($model = TelefonoSocioWalletTransaction::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
} 