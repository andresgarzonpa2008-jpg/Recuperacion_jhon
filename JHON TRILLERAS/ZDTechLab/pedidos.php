<?php
require_once __DIR__ . '/app/seguridad/guardia.php';
exigirRol('administrador','vendedor');
require_once __DIR__ . '/app/config/conexion.php';
$pdo = Conexion::obtener();
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!validarCsrf($_POST['csrf'] ?? null)) { http_response_code(419); exit; }
  $clienteId = filter_input(INPUT_POST,'cliente_id',FILTER_VALIDATE_INT);
  $ids = $_POST['producto_id'] ?? []; $cants = $_POST['cantidad'] ?? [];
  if (!$clienteId) $_SESSION['aviso']=['tipo'=>'error','texto'=>'Seleccione cliente.'];
  else {
    $items = [];
    foreach ($ids as $i => $pid) {
      $pid=(int)$pid; $cant=(int)($cants[$i]??0);
      if ($pid>0 && $cant>0) {
        $st=$pdo->prepare("SELECT id,precio,stock FROM productos WHERE id=:id AND activo=1"); $st->execute([':id'=>$pid]); $p=$st->fetch();
        if ($p && $p['stock'] >= $cant) $items[]=['id'=>$pid,'cant'=>$cant,'precio'=>(float)$p['precio']];
      }
    }
    if (!$items) $_SESSION['aviso']=['tipo'=>'error','texto'=>'Agregue productos con stock suficiente.'];
    else {
      $total = array_sum(array_map(fn($it)=>$it['cant']*$it['precio'],$items));
      $pdo->beginTransaction();
      try {
        $pdo->prepare("INSERT INTO pedidos (cliente_id,usuario_id,fecha,total) VALUES (:c,:u,NOW(),:t)")
          ->execute([':c'=>$clienteId,':u'=>$_SESSION['usuario']['id'],':t'=>$total]);
        $pedId=(int)$pdo->lastInsertId();
        $det=$pdo->prepare("INSERT INTO detalle_pedido (pedido_id,producto_id,cantidad,precio_unitario) VALUES (:p,:pr,:c,:pu)");
        $upd=$pdo->prepare("UPDATE productos SET stock=stock-:c WHERE id=:id");
        foreach ($items as $it){ $det->execute([':p'=>$pedId,':pr'=>$it['id'],':c'=>$it['cant'],':pu'=>$it['precio']]); $upd->execute([':c'=>$it['cant'],':id'=>$it['id']]); }
        $pdo->commit();
        $_SESSION['aviso']=['tipo'=>'exito','texto'=>"Pedido #$pedId registrado por $$total."];
      } catch (Throwable $e) { $pdo->rollBack(); $_SESSION['aviso']=['tipo'=>'error','texto'=>'No fue posible registrar el pedido.']; }
    }
  }
  header('Location: pedidos.php', true, 303); exit;
}
$clientes=$pdo->query("SELECT id,nombre FROM clientes WHERE activo=1 ORDER BY nombre")->fetchAll();
$productos=$pdo->query("SELECT p.id,p.nombre,p.precio,p.stock,c.nombre cat FROM productos p INNER JOIN categorias c ON c.id=p.categoria_id WHERE p.activo=1 ORDER BY p.nombre")->fetchAll();
$peds=$pdo->query("SELECT p.id,cl.nombre cliente,p.fecha,p.total,p.estado FROM pedidos p INNER JOIN clientes cl ON cl.id=p.cliente_id ORDER BY p.id DESC LIMIT 20")->fetchAll();
$titulo='Pedidos'; require __DIR__.'/app/vistas/parciales/cabecera.php'; require __DIR__.'/app/vistas/parciales/menu.php';
$a=aviso();
?>
<main class="panel__contenido"><h1>Pedidos (transacción + descuento de stock)</h1>
<?php if($a): ?><p class="alerta <?= $a['tipo']==='exito'?'alerta-exito':'alerta-error' ?>"><?= e($a['texto']) ?></p><?php endif; ?>
<h2>Nuevo pedido</h2>
<form method="post"><input type="hidden" name="csrf" value="<?= e(tokenCsrf()) ?>">
<div class="campo"><label for="cliente_id">Cliente</label><select id="cliente_id" name="cliente_id" required><option value="">— Seleccione —</option>
<?php foreach($clientes as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['nombre']) ?></option><?php endforeach; ?></select></div>
<table class="tabla"><thead><tr><th>Producto (stock/precio)</th><th>Cantidad</th></tr></thead><tbody>
<?php for($i=0;$i<4;$i++): ?><tr><td><select name="producto_id[]"><option value="0">— Ninguno —</option>
<?php foreach($productos as $p): ?><option value="<?= (int)$p['id'] ?>"><?= e($p['nombre']) ?> (<?= (int)$p['stock'] ?> · $<?= number_format((float)$p['precio'],0,',','.') ?>)</option><?php endforeach; ?></select></td>
<td><input type="number" name="cantidad[]" min="0" value="0"></td></tr><?php endfor; ?>
</tbody></table>
<button class="boton" type="submit">Registrar pedido</button></form>
<h2>Últimos pedidos</h2>
<table class="tabla"><thead><tr><th>#</th><th>Cliente</th><th>Fecha</th><th>Total</th><th>Estado</th></tr></thead><tbody>
<?php foreach($peds as $p): ?><tr><td><?= (int)$p['id'] ?></td><td><?= e($p['cliente']) ?></td><td><?= e($p['fecha']) ?></td><td>$<?= number_format((float)$p['total'],0,',','.') ?></td><td><?= e($p['estado']) ?></td></tr><?php endforeach; ?>
</tbody></table></main>
<?php require __DIR__.'/app/vistas/parciales/pie.php'; ?>
