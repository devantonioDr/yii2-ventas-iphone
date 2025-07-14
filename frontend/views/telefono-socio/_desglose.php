<?php

/** @var array $ganancia */
?>
<div class="table-responsive">
<table class="table table-bordered">
    <tr>
        <th>Ganancia Neta Total</th>
        <td><?= 'RD$ ' . number_format($ganancia['neta'], 2) ?></td>
    </tr>
    <tr>
        <th>Ganancia Socio</th>
        <td><?= 'RD$ ' . number_format($ganancia['socio'], 2) ?></td>
    </tr>
    <tr>
        <th>Ganancia Empresa</th>
        <td><?= 'RD$ ' . number_format($ganancia['empresa'], 2) ?></td>
    </tr>
    <tr>
        <th>Total Precio Adquisición</th>
        <td><?= 'RD$ ' . number_format($ganancia['precioAdquisicion'], 2) ?></td>
    </tr>
    <tr>
        <th>Total Gastos</th>
        <td><?= 'RD$ ' . number_format($ganancia['gastos'], 2) ?></td>
    </tr>
</table>
</div> 