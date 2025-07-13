<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return [
      'access' => [
        'class' => AccessControl::class,
        'only' => ['login', 'logout', 'signup', 'index'],
        'rules' => [
          [
            'actions' => ['login'],
            'allow' => true,
            'roles' => ['?'],
          ],
          [
            'actions' => ['logout', 'index', 'signup', 'index'],
            'allow' => true,
            'roles' => ['@'],
          ],
        ],
      ],
      'verbs' => [
        'class' => VerbFilter::class,
        'actions' => [
          'logout' => ['post'],
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function actions()
  {
    return [
      'error' => [
        'class' => 'yii\web\ErrorAction',
      ],
      'captcha' => [
        'class' => 'yii\captcha\CaptchaAction',
        'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
      ],
    ];
  }

  /**
   * Displays homepage.
   *
   * @return mixed
   */
  public function actionIndex()
  {
    return $this->render('index');
  }

  /**
   * Logs in a user.
   *
   * @return mixed
   */
  public function actionLogin()
  {

    $this->layout = 'main-login';


    

    $model = new LoginForm();
    if ($model->load(Yii::$app->request->post()) && $model->login()) {
      return $this->goBack();
    }

    $model->password = '';

    return $this->render('login', [
      'model' => $model,
    ]);
  }

  public function actionLogout()
  {
    Yii::$app->user->logout();

    return $this->goHome();
  }

}
