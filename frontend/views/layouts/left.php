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
              ['label' => 'Insertar Lote', 'icon' => 'fa fa-plus-circle', 'url' => ['/telefono/batch-insert'],],
              ['label' => 'Listado', 'icon' => 'fa fa-list', 'url' => ['/telefono/index'],],
              ['label' => 'Marcas y Modelos', 'icon' => 'fa fa-tags', 'url' => ['/telefono-marca-modelo/index'],],
            ]
          ],
          [
            'label' => 'Compras',
            'icon' => 'shopping-cart',
            'url' => ['/telefono-compra/index'],
          ],
          [
            'label' => 'Socios',
            'icon' => 'users',
            'url' => '#',
            'items' => [
                ['label' => 'Lista de socios', 'icon' => 'fa fa-list', 'url' => ['/telefono-socio/index'],],
            ]
          ],
          [
            'label' => 'Pagos',
            'icon' => 'money',
            'url' => '#',
            'items' => [
              ['label' => 'Lista de pagos', 'icon' => 'fa fa-list', 'url' => ['/telefono-socio-pago/index'],],
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