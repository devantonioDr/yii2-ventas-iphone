<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use common\models\telefono\Telefono;
use common\models\telefono\TelefonoGasto;
use common\usecases\telefono_gasto\CreateTelefonoGastoUseCase;
use common\usecases\telefono_gasto\UpdateTelefonoGastoUseCase;
use common\usecases\telefono_gasto\DeleteTelefonoGastoUseCase;

/**
 * TelefonoGastoController implements the CRUD actions for TelefonoGasto model.
 */
class TelefonoGastoController extends Controller
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
     * Lists all TelefonoGasto models for a specific telefono.
     * @param int $telefono_id
     * @return mixed
     */
    public function actionIndex($telefono_id)
    {
        $telefono = $this->findTelefono($telefono_id);
        
        $dataProvider = new ActiveDataProvider([
            'query' => TelefonoGasto::find()->where(['telefono_id' => $telefono_id]),
            'sort' => [
                'defaultOrder' => ['fecha_gasto' => SORT_DESC]
            ]
        ]);

        return $this->render('index', [
            'telefono' => $telefono,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new TelefonoGasto model.
     * @param int $telefono_id
     * @return mixed
     */
    public function actionCreate($telefono_id)
    {
        $telefono = $this->findTelefono($telefono_id);
        $gasto = new TelefonoGasto();
        $gasto->telefono_id = $telefono_id;
        $postData = Yii::$app->request->post('TelefonoGasto', []);


        if ($this->request->isPost) {
            try {
                $useCase = new CreateTelefonoGastoUseCase();
                $useCase->execute(
                    $telefono_id,
                    $postData['descripcion'] ?? '',
                    $postData['monto_gasto'] ?? '0'
                );
                Yii::$app->session->setFlash('success', 'Gasto agregado exitosamente.');
                return $this->redirect(['index', 'telefono_id' => $telefono_id]);
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', 'Error al crear el gasto: ' . $e->getMessage());
            }
        }

        return $this->render('create', [
            'gasto' => $gasto,
            'telefono' => $telefono,
        ]);
    }

    /**
     * Updates an existing TelefonoGasto model.
     * @param int $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $gasto = $this->findModel($id);
        $telefono = $gasto->telefono;
        $postData = Yii::$app->request->post('TelefonoGasto', []);

        if ($this->request->isPost) {
            try {
                $useCase = new UpdateTelefonoGastoUseCase();
                $useCase->execute(
                    $id,
                    $postData['descripcion'] ?? '',
                    $postData['monto_gasto'] ?? '0'
                );
                Yii::$app->session->setFlash('success', 'Gasto actualizado exitosamente.');
                return $this->redirect(['index', 'telefono_id' => $gasto->telefono_id]);
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', 'Error al actualizar el gasto: ' . $e->getMessage());
            }
        }

        return $this->render('update', [
            'gasto' => $gasto,
            'telefono' => $telefono,
        ]);
    }

    /**
     * Deletes an existing TelefonoGasto model.
     * @param int $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $gasto = $this->findModel($id);
        $telefonoId = $gasto->telefono_id;

        try {
            $useCase = new DeleteTelefonoGastoUseCase();
            $useCase->execute($id);
            Yii::$app->session->setFlash('success', 'Gasto eliminado exitosamente.');
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Error al eliminar el gasto: ' . $e->getMessage());
        }

        return $this->redirect(['index', 'telefono_id' => $telefonoId]);
    }

    /**
     * Finds the TelefonoGasto model based on its primary key value.
     * @param int $id
     * @return TelefonoGasto the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TelefonoGasto::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('El gasto solicitado no existe.');
    }

    /**
     * Finds the Telefono model based on its primary key value.
     * @param int $id
     * @return Telefono the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findTelefono($id)
    {
        if (($model = Telefono::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('El teléfono solicitado no existe.');
    }
} 