<?php

use yii\widgets\ActiveForm;
use frontend\models\JceForm;


?>

<aside class="main-sidebar">

  <section class="sidebar">


    <?= dmstr\widgets\Menu::widget(
      [
        'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
        'items' => [
          [
            'label' => 'Teléfonos',
            'icon' => 'mobile',
            'url' => '#',
            'items' => [
              ['label' => 'Insertar Lote', 'icon' => 'circle', 'url' => ['/telefono/batch-insert'],],
              ['label' => 'Listado', 'icon' => 'circle', 'url' => ['/telefono/index'],],
            ]
          ],
          [
            'label' => 'Socios',
            'icon' => 'users',
            'url' => '#',
            'items' => [
              ['label' => 'Listado de Socios', 'icon' => 'circle', 'url' => ['/telefono-socio/index'],],
              ['label' => 'Crear Socio', 'icon' => 'circle', 'url' => ['/telefono-socio/create'],],
            ]
          ],
        ],
      ]
    ) ?>

  </section>

</aside>


<style>
  .skin-blue .sidebar-form {
    border: none !important;
  }
</style>