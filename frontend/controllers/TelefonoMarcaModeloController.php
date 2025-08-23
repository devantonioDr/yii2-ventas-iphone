<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\telefono\TelefonoMarcaModelo;
use common\models\telefono\TelefonoMarcaModeloSearch;
use common\usecases\telefono_marca_modelo\CreateTelefonoMarcaModeloUseCase;
use common\usecases\telefono_marca_modelo\UpdateTelefonoMarcaModeloUseCase;
use common\usecases\telefono_marca_modelo\DeleteTelefonoMarcaModeloUseCase;

/**
 * TelefonoMarcaModeloController implements the CRUD actions for TelefonoMarcaModelo model.
 */
class TelefonoMarcaModeloController extends Controller
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
     * Lists all TelefonoMarcaModelo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TelefonoMarcaModeloSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TelefonoMarcaModelo model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        // Contar teléfonos que usan esta marca y modelo
        $telefonosCount = \common\models\telefono\Telefono::find()
            ->where(['marca' => $model->marca, 'modelo' => $model->modelo])
            ->count();

        return $this->render('view', [
            'model' => $model,
            'telefonosCount' => $telefonosCount,
        ]);
    }

    /**
     * Creates a new TelefonoMarcaModelo model.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TelefonoMarcaModelo();
        $useCase = new CreateTelefonoMarcaModeloUseCase();

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            try {
                $result = $useCase->execute(
                    $post['TelefonoMarcaModelo']['marca'],
                    $post['TelefonoMarcaModelo']['modelo']
                );

                if ($result) {
                    Yii::$app->session->setFlash('success', 'Marca y modelo creado exitosamente.');
                    return $this->redirect(['view', 'id' => $result->id]);
                }
            } catch (\RuntimeException $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', 'Error al crear marca y modelo: ' . $e->getMessage());
            }
        }

        // Obtener todas las marcas existentes para el dropdown
        $marcas = TelefonoMarcaModelo::find()
            ->select('marca')
            ->distinct()
            ->orderBy('marca')
            ->asArray()
            ->all();

        return $this->render('create', [
            'model' => $model,
            'marcas' => $marcas,
        ]);
    }

    /**
     * Updates an existing TelefonoMarcaModelo model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $useCase = new UpdateTelefonoMarcaModeloUseCase();

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            try {
                $result = $useCase->execute(
                    $id,
                    $post['TelefonoMarcaModelo']['marca'],
                    $post['TelefonoMarcaModelo']['modelo']
                );

                if ($result) {
                    Yii::$app->session->setFlash('success', 'Marca y modelo actualizado exitosamente.');
                    return $this->redirect(['view', 'id' => $result->id]);
                }
            } catch (\RuntimeException $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', 'Error al actualizar marca y modelo: ' . $e->getMessage());
            }
        }

        // Obtener todas las marcas existentes para el dropdown
        $marcas = TelefonoMarcaModelo::find()
            ->select('marca')
            ->distinct()
            ->orderBy('marca')
            ->asArray()
            ->all();

        // Obtener modelos de la marca actual para sugerencias
        $modelos = TelefonoMarcaModelo::find()
            ->select('modelo')
            ->where(['marca' => $model->marca])
            ->distinct()
            ->orderBy('modelo')
            ->asArray()
            ->all();

        return $this->render('update', [
            'model' => $model,
            'marcas' => $marcas,
            'modelos' => $modelos,
        ]);
    }

    /**
     * Deletes an existing TelefonoMarcaModelo model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $useCase = new DeleteTelefonoMarcaModeloUseCase();

        try {
            $useCase->execute($id);
            Yii::$app->session->setFlash('success', 'Marca y modelo eliminado exitosamente.');
        } catch (\RuntimeException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Error al eliminar marca y modelo: ' . $e->getMessage());
        }

        return $this->redirect(['index']);
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
            ->distinct()
            ->orderBy('modelo')
            ->asArray()
            ->all();

        if ($modelos) {
            return ['success' => true, 'modelos' => $modelos];
        }

        return ['success' => false, 'modelos' => []];
    }

    /**
     * Obtiene todas las marcas existentes via AJAX
     * @return array
     */
    public function actionGetMarcas()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $marcas = TelefonoMarcaModelo::find()
            ->select('marca')
            ->distinct()
            ->orderBy('marca')
            ->asArray()
            ->all();

        return ['success' => true, 'marcas' => $marcas];
    }

    /**
     * Finds the TelefonoMarcaModelo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return TelefonoMarcaModelo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TelefonoMarcaModelo::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('La página solicitada no existe.');
    }
}
