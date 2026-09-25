<?php
require_once __DIR__ . '/app/seguridad/guardia.php';
exigirRol('administrador','vendedor');
require_once __DIR__ . '/app/config/conexion.php';
$pdo = Conexion::obtener();
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!validarCsrf($_POST['csrf'] ?? null)) { http_response_code(419); exit; }
  $n = trim((string)($_POST['nombre'] ?? ''));
  if (mb_strlen($n) < 3) $_SESSION['aviso']=['tipo'=>'error','texto'=>'Nombre mínimo 3 caracteres.'];
  else {
    try {
      if (!empty($_POST['id'])) $pdo->prepare("UPDATE categorias SET nombre=:n, descripcion=:d WHERE id=:id")->execute([':n'=>$n,':d'=>$_POST['descripcion']??null,':id'=>(int)$_POST['id']]);
      else $pdo->prepare("INSERT INTO categorias (nombre,descripcion) VALUES (:n,:d)")->execute([':n'=>$n,':d'=>$_POST['descripcion']??null]);
      $_SESSION['aviso']=['tipo'=>'exito','texto'=>'Categoría guardada.'];
    } catch (Throwable $e) { $_SESSION['aviso']=['tipo'=>'error','texto'=>'Nombre duplicado.']; }
  }
  header('Location: categorias.php', true, 303); exit;
}
$cats = $pdo->query("SELECT * FROM categorias WHERE activo=1 ORDER BY nombre")->fetchAll();
$titulo='Categorías'; require __DIR__.'/app/vistas/parciales/cabecera.php'; require __DIR__.'/app/vistas/parciales/menu.php';
$a = aviso();
?>
<main class="panel__contenido"><h1>Categorías</h1>
<?php if ($a): ?><p class="alerta <?= $a['tipo']==='exito'?'alerta-exito':'alerta-error' ?>"><?= e($a['texto']) ?></p><?php endif; ?>
<table class="tabla"><thead><tr><th>Nombre</th><th>Descripción</th></tr></thead><tbody>
<?php foreach ($cats as $c): ?><tr><td><?= e($c['nombre']) ?></td><td><?= e($c['descripcion']??'') ?></td></tr><?php endforeach; ?>
</tbody></table>
<h2>Nueva categoría</h2>
<form method="post"><input type="hidden" name="csrf" value="<?= e(tokenCsrf()) ?>">
<div class="campo"><label for="nombre">Nombre</label><input id="nombre" name="nombre" required minlength="3"></div>
<div class="campo"><label for="descripcion">Descripción</label><input id="descripcion" name="descripcion"></div>
<button class="boton" type="submit">Guardar</button></form></main>
<?php require __DIR__.'/app/vistas/parciales/pie.php'; ?>
