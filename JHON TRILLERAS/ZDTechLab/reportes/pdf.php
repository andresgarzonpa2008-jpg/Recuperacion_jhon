<?php
// Día 15 — PDF: si Dompdf está instalado (composer require dompdf/dompdf) se usa; si no, se muestra HTML imprimible
declare(strict_types=1);
require_once __DIR__ . '/../app/seguridad/guardia.php';
exigirRol('administrador','consultor');
require_once __DIR__ . '/../app/config/conexion.php';
$pdo = Conexion::obtener();
$filas = $pdo->query("SELECT categoria,unidades,total_vendido FROM v_ventas_categoria WHERE total_vendido>0 ORDER BY total_vendido DESC")->fetchAll();
$total = array_sum(array_column($filas,'total_vendido'));
$numeroReporte = 'R-'.date('Ym').'-PDF';
ob_start();
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Reporte PDF</title>
<link rel="stylesheet" href="../css/tokens.css"><link rel="stylesheet" href="../css/estilos.css"><link rel="stylesheet" href="../css/reporte.css"></head>
<body><section class="reporte">
<?php require __DIR__ . '/../app/vistas/reportes/encabezado.php'; ?>
<h2>Ventas por categoría</h2>
<table class="tabla"><thead><tr><th>Categoría</th><th>Unidades</th><th>Total</th></tr></thead><tbody>
<?php foreach($filas as $f): ?><tr><td><?= htmlspecialchars($f['categoria']) ?></td><td><?= (int)$f['unidades'] ?></td><td>$<?= number_format((float)$f['total_vendido'],0,',','.') ?></td></tr><?php endforeach; ?>
<tr><td><strong>Total</strong></td><td></td><td><strong>$<?= number_format($total,0,',','.') ?></strong></td></tr>
</tbody></table></section>
<p class="no-imprimir" style="text-align:center"><button class="boton" onclick="window.print()">Guardar como PDF (imprimir)</button></p>
</body></html>
<?php
$html = ob_get_clean();
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
  require_once __DIR__ . '/../vendor/autoload.php';
  $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled'=>true]);
  $dompdf->loadHtml($html,'UTF-8'); $dompdf->setPaper('A4','portrait'); $dompdf->render();
  $dompdf->stream('reporte-ventas-'.date('Ymd-Hi').'.pdf',['Attachment'=>false]); exit;
}
echo $html;
