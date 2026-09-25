<?php
require_once __DIR__ . '/app/seguridad/guardia.php';
require_once __DIR__ . '/app/config/conexion.php';
$pdo = Conexion::obtener();
$ind = $pdo->query("SELECT
 (SELECT COUNT(*) FROM productos WHERE activo=1) AS productos_activos,
 (SELECT COUNT(*) FROM pedidos WHERE YEAR(fecha)=YEAR(CURDATE()) AND MONTH(fecha)=MONTH(CURDATE())) AS pedidos_mes,
 (SELECT COALESCE(SUM(total),0) FROM pedidos WHERE YEAR(fecha)=YEAR(CURDATE()) AND MONTH(fecha)=MONTH(CURDATE())) AS ventas_mes,
 (SELECT COUNT(*) FROM productos WHERE stock<5 AND activo=1) AS stock_critico")->fetch();
$titulo = 'Tablero';
require __DIR__ . '/app/vistas/parciales/cabecera.php';
require __DIR__ . '/app/vistas/parciales/menu.php';
?>
<main class="panel__contenido">
<h1>Resumen general</h1>
<p>Periodo: <?= date('F Y') ?> · Bienvenido, <?= e($_SESSION['usuario']['nombre']) ?></p>
<section class="indicadores">
<article class="tarjeta tarjeta--verde"><p class="tarjeta__rotulo">Productos activos</p><p class="tarjeta__valor"><?= (int)$ind['productos_activos'] ?></p></article>
<article class="tarjeta tarjeta--azul"><p class="tarjeta__rotulo">Pedidos del mes</p><p class="tarjeta__valor"><?= (int)$ind['pedidos_mes'] ?></p></article>
<article class="tarjeta tarjeta--naranja"><p class="tarjeta__rotulo">Ventas del mes</p><p class="tarjeta__valor">$ <?= number_format((float)$ind['ventas_mes'],0,',','.') ?></p></article>
<article class="tarjeta tarjeta--roja"><p class="tarjeta__rotulo">Stock crítico</p><p class="tarjeta__valor"><?= (int)$ind['stock_critico'] ?></p></article>
</section>
<form id="filtro-fechas" class="toolbar no-imprimir">
<div class="campo"><label for="desde">Desde</label><input type="date" id="desde" name="desde"></div>
<div class="campo"><label for="hasta">Hasta</label><input type="date" id="hasta" name="hasta"></div>
<div><button class="boton" type="submit">Filtrar gráficos</button></div>
</form>
<section class="graficos">
<div class="grafico"><h3>Ventas por mes (barras)</h3><canvas id="g-ventas"></canvas></div>
<div class="grafico"><h3>Ventas por categoría (dona)</h3><canvas id="g-categorias"></canvas></div>
<div class="grafico"><h3>Pedidos por mes (línea)</h3><canvas id="g-pedidos"></canvas></div>
</section>
</main>
<?php require __DIR__ . '/app/vistas/parciales/pie.php'; ?>
