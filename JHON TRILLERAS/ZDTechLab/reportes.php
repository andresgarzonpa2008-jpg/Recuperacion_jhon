<?php
require_once __DIR__ . '/app/seguridad/guardia.php';
exigirRol('administrador','consultor');
require_once __DIR__ . '/app/config/conexion.php';
$pdo = Conexion::obtener();
$desde = $_GET['desde'] ?? '2026-01-01'; $hasta = $_GET['hasta'] ?? date('Y-m-d');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/',$desde)) $desde='2026-01-01';
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/',$hasta)) $hasta=date('Y-m-d');
$numeroReporte = 'R-'.date('Ym').'-0148';
// Ventas por categoría en rango (usa detalle + pedidos, misma lógica de la vista)
$st = $pdo->prepare("SELECT c.nombre categoria, SUM(d.cantidad) unidades, COUNT(DISTINCT p.id) pedidos, SUM(d.cantidad*d.precio_unitario) total
 FROM categorias c LEFT JOIN productos pr ON pr.categoria_id=c.id
 LEFT JOIN detalle_pedido d ON d.producto_id=pr.id
 LEFT JOIN pedidos p ON p.id=d.pedido_id AND p.estado='confirmado' AND DATE(p.fecha) BETWEEN :d AND :h
 GROUP BY c.id,c.nombre ORDER BY total DESC");
$st->execute([':d'=>$desde,':h'=>$hasta]); $filas=$st->fetchAll();
$total = array_sum(array_map(fn($f)=>(float)$f['total'],$filas));
$unidades = array_sum(array_map(fn($f)=>(int)$f['unidades'],$filas));
$stock = $pdo->query("SELECT * FROM v_stock_critico")->fetchAll();
$top = $pdo->query("SELECT * FROM v_clientes_top ORDER BY total_comprado DESC LIMIT 10")->fetchAll();
$titulo='Reportes'; require __DIR__.'/app/vistas/parciales/cabecera.php'; require __DIR__.'/app/vistas/parciales/menu.php';
?>
<main class="panel__contenido">
<h1>Reportes con identidad institucional</h1>
<form method="get" class="toolbar no-imprimir">
<div class="campo"><label for="desde">Desde</label><input type="date" id="desde" name="desde" value="<?= e($desde) ?>"></div>
<div class="campo"><label for="hasta">Hasta</label><input type="date" id="hasta" name="hasta" value="<?= e($hasta) ?>"></div>
<div><button class="boton" type="submit">Filtrar</button>
<a class="boton boton--secundario" href="reportes/csv.php?desde=<?= urlencode($desde) ?>&hasta=<?= urlencode($hasta) ?>">CSV</a>
<button class="boton" type="button" onclick="window.print()">Imprimir / PDF</button></div>
<input type="hidden" id="csrf" value="<?= e(tokenCsrf()) ?>">
</form>

<section class="reporte">
<?php require __DIR__.'/app/vistas/reportes/encabezado.php'; ?>
<h2>Reporte de ventas por categoría</h2>
<p class="reporte__filtros">Filtros: <?= e($desde) ?> al <?= e($hasta) ?> · Estado: confirmado · Todas las categorías</p>
<div class="graficos no-imprimir"><div class="grafico"><h3>Evolución mensual</h3><canvas id="g-ventas"></canvas></div>
<div class="grafico"><h3>Participación</h3><canvas id="g-categorias"></canvas></div></div>
<table class="tabla"><thead><tr><th>Categoría</th><th>Unidades</th><th>Pedidos</th><th>Total vendido</th><th>%</th></tr></thead><tbody>
<?php foreach($filas as $f): $pct=$total>0?((float)$f['total']/$total*100):0; ?>
<tr><td><?= e($f['categoria']) ?></td><td><?= (int)$f['unidades'] ?></td><td><?= (int)$f['pedidos'] ?></td>
<td>$<?= number_format((float)$f['total'],0,',','.') ?></td><td><?= number_format($pct,1) ?></td></tr><?php endforeach; ?>
<tr><td><strong>Total general</strong></td><td><strong><?= $unidades ?></strong></td><td><strong>—</strong></td>
<td><strong>$<?= number_format($total,0,',','.') ?></strong></td><td><strong>100</strong></td></tr>
</tbody></table>
<div class="reporte__pie"><span>Documento generado automáticamente por JT.TechLab. Información de uso interno.</span><span>Página 1 de 1</span></div>
</section>

<section class="reporte" style="margin-top:1rem">
<h2>Inventario con stock crítico (vista v_stock_critico)</h2>
<table class="tabla"><thead><tr><th>Producto</th><th>Categoría</th><th>Stock</th><th>Mínimo</th><th>Precio</th></tr></thead><tbody>
<?php foreach($stock as $s): ?><tr><td><?= e($s['nombre']) ?></td><td><?= e($s['categoria']) ?></td><td><?= (int)$s['stock'] ?></td><td><?= (int)$s['stock_minimo'] ?></td><td>$<?= number_format((float)$s['precio'],0,',','.') ?></td></tr><?php endforeach; ?>
</tbody></table></section>

<section class="reporte" style="margin-top:1rem">
<h2>Pedidos por cliente (vista v_clientes_top)</h2>
<table class="tabla"><thead><tr><th>Cliente</th><th>Documento</th><th>Pedidos</th><th>Total comprado</th></tr></thead><tbody>
<?php $tt=0; foreach($top as $t): $tt+=(float)$t['total_comprado']; ?><tr><td><?= e($t['nombre']) ?></td><td><?= e($t['documento']) ?></td><td><?= (int)$t['pedidos'] ?></td><td>$<?= number_format((float)$t['total_comprado'],0,',','.') ?></td></tr><?php endforeach; ?>
<tr><td colspan="3"><strong>Total</strong></td><td><strong>$<?= number_format($tt,0,',','.') ?></strong></td></tr>
</tbody></table></section>
</main>
<?php require __DIR__.'/app/vistas/parciales/pie.php'; ?>
