<?php

/* @var $this yii\web\View */

use common\components\MyHelpers;
use common\models\Clients;
use common\models\Municipio;
use frontend\models\JceForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Panel de Control';
$this->params['breadcrumbs'][] = $this->title;


$today = date('d-m-Y');
$tomorrow = date('d-m-Y', strtotime('+1 day'));


?>

