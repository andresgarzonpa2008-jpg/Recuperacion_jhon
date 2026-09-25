<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/seguridad/guardia.php';
exigirRol('administrador','consultor');
require_once __DIR__ . '/../app/config/conexion.php';
$desde = $_GET['desde'] ?? '2026-01-01'; $hasta = $_GET['hasta'] ?? date('Y-m-d');
$pdo = Conexion::obtener();
$st = $pdo->prepare("SELECT c.nombre categoria, SUM(d.cantidad) unidades, SUM(d.cantidad*d.precio_unitario) total
 FROM categorias c LEFT JOIN productos pr ON pr.categoria_id=c.id
 LEFT JOIN detalle_pedido d ON d.producto_id=pr.id
 LEFT JOIN pedidos p ON p.id=d.pedido_id AND p.estado='confirmado' AND DATE(p.fecha) BETWEEN :d AND :h
 GROUP BY c.id,c.nombre ORDER BY total DESC");
$st->execute([':d'=>$desde,':h'=>$hasta]); $filas=$st->fetchAll();
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="ventas-' . date('Ymd') . '.csv"');
$out = fopen('php://output','w');
fwrite($out, "\xEF\xBB\xBF");
fputcsv($out, ['Categoría','Unidades','Total vendido'], ';');
foreach ($filas as $f) fputcsv($out, [$f['categoria'],$f['unidades'],$f['total']], ';');
fclose($out);
