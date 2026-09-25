<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/seguridad/guardia.php';
require_once __DIR__ . '/../app/config/conexion.php';
header('Content-Type: application/json; charset=utf-8');
$pdo = Conexion::obtener();
$desde = $_GET['desde'] ?? ''; $hasta = $_GET['hasta'] ?? '';
$filtro = '';
$params = [];
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $desde) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $hasta)) {
  $filtro = ' AND DATE(p.fecha) BETWEEN :d AND :h ';
  $params = [':d'=>$desde, ':h'=>$hasta];
}
$sqlMes = "SELECT DATE_FORMAT(p.fecha,'%Y-%m') periodo, SUM(d.cantidad*d.precio_unitario) total, COUNT(DISTINCT p.id) pedidos
  FROM pedidos p INNER JOIN detalle_pedido d ON d.pedido_id=p.id WHERE p.estado='confirmado' $filtro
  GROUP BY periodo ORDER BY periodo DESC LIMIT 12";
$st = $pdo->prepare($sqlMes); $st->execute($params); $meses = array_reverse($st->fetchAll());
$sqlCat = "SELECT c.nombre categoria, COALESCE(SUM(d.cantidad*d.precio_unitario),0) total
  FROM categorias c LEFT JOIN productos pr ON pr.categoria_id=c.id
  LEFT JOIN detalle_pedido d ON d.producto_id=pr.id
  LEFT JOIN pedidos p ON p.id=d.pedido_id AND p.estado='confirmado'
  GROUP BY c.id,c.nombre HAVING total>0 ORDER BY total DESC";
$cats = $pdo->query($sqlCat)->fetchAll();
echo json_encode([
  'ventasMes'=>['etiquetas'=>array_column($meses,'periodo'),'valores'=>array_map('floatval',array_column($meses,'total'))],
  'pedidosMes'=>['etiquetas'=>array_column($meses,'periodo'),'valores'=>array_map('intval',array_column($meses,'pedidos'))],
  'categorias'=>['etiquetas'=>array_column($cats,'categoria'),'valores'=>array_map('floatval',array_column($cats,'total'))],
], JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);
