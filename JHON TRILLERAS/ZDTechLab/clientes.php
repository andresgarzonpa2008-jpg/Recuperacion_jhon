<?php
require_once __DIR__ . '/app/seguridad/guardia.php';
exigirRol('administrador','vendedor');
require_once __DIR__ . '/app/config/conexion.php';
require_once __DIR__ . '/app/modelos/ClienteModelo.php';
$pdo = Conexion::obtener(); $m = new ClienteModelo($pdo);
$b = trim((string)($_GET['b'] ?? '')); $pag = max(1,(int)($_GET['pag'] ?? 1));
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!validarCsrf($_POST['csrf'] ?? null)) { http_response_code(419); exit; }
  if (($_POST['accion'] ?? '') === 'eliminar') {
    exigirRol('administrador');
    $m->desactivar((int)$_POST['id']);
    $_SESSION['aviso'] = ['tipo'=>'exito','texto'=>'Cliente desactivado.'];
    header('Location: clientes.php', true, 303); exit;
  }
  $nombre = trim((string)($_POST['nombre'] ?? '')); $doc = trim((string)($_POST['documento'] ?? ''));
  $correo = trim((string)($_POST['correo'] ?? '')); $tel = trim((string)($_POST['telefono'] ?? ''));
  $err = [];
  if (mb_strlen($nombre) < 3) $err[] = 'Nombre mínimo 3 caracteres.';
  if ($doc === '') $err[] = 'Documento obligatorio y único.';
  if ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL)) $err[] = 'Correo inválido.';
  if (!$err) {
    try {
      $id = (int)($_POST['id'] ?? 0);
      $d = ['nombre'=>$nombre,'documento'=>$doc,'correo'=>$correo,'telefono'=>$tel];
      $id > 0 ? $m->actualizar($id,$d) : $m->crear($d);
      $_SESSION['aviso'] = ['tipo'=>'exito','texto'=>'Cliente guardado.'];
    } catch (Throwable $e) { $_SESSION['aviso'] = ['tipo'=>'error','texto'=>'Documento o correo duplicado.']; }
    header('Location: clientes.php', true, 303); exit;
  }
  $_SESSION['aviso'] = ['tipo'=>'error','texto'=>implode(' ',$err)];
  header('Location: clientes.php', true, 303); exit;
}
$filas = $m->listar($b,$pag,10); $total = $m->contar($b); $pags = max(1,(int)ceil($total/10));
$editar = !empty($_GET['editar']) ? $m->obtener((int)$_GET['editar']) : null;
$titulo='Clientes'; require __DIR__.'/app/vistas/parciales/cabecera.php'; require __DIR__.'/app/vistas/parciales/menu.php';
$a = aviso();
?>
<main class="panel__contenido"><h1>Clientes</h1>
<?php if ($a): ?><p class="alerta <?= $a['tipo']==='exito'?'alerta-exito':'alerta-error' ?>"><?= e($a['texto']) ?></p><?php endif; ?>
<form method="get" class="toolbar"><div class="campo"><label for="b">Buscar</label><input id="b" name="b" value="<?= e($b) ?>"></div><button class="boton" type="submit">Buscar</button></form>
<table class="tabla"><caption><?= $total ?> clientes</caption>
<thead><tr><th>Nombre</th><th>Documento</th><th>Correo</th><th>Acciones</th></tr></thead><tbody>
<?php foreach ($filas as $c): ?><tr><td><?= e($c['nombre']) ?></td><td><?= e($c['documento']) ?></td><td><?= e($c['correo'] ?? '') ?></td>
<td><a class="boton-mini" href="clientes.php?editar=<?= (int)$c['id'] ?>">Editar</a>
<form method="post" style="display:inline" onsubmit="return confirm('¿Desactivar?')"><input type="hidden" name="csrf" value="<?= e(tokenCsrf()) ?>"><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?= (int)$c['id'] ?>"><button class="boton-mini boton-peligro" <?= puede('administrador')?'':'disabled' ?>>Eliminar</button></form></td></tr><?php endforeach; ?>
</tbody></table>
<div class="paginacion"><?php for($i=1;$i<=$pags;$i++): ?><a href="clientes.php?b=<?= urlencode($b) ?>&pag=<?= $i ?>"><?= $i ?></a><?php endfor; ?></div>
<h2><?= $editar?'Editar':'Nuevo' ?> cliente</h2>
<form method="post" action="clientes.php"><input type="hidden" name="csrf" value="<?= e(tokenCsrf()) ?>"><input type="hidden" name="id" value="<?= (int)($editar['id']??0) ?>">
<div class="campo"><label for="nombre">Nombre</label><input id="nombre" name="nombre" required minlength="3" value="<?= e($editar['nombre']??'') ?>"></div>
<div class="campo"><label for="documento">Documento</label><input id="documento" name="documento" required value="<?= e($editar['documento']??'') ?>"></div>
<div class="campo"><label for="correo">Correo</label><input id="correo" name="correo" type="email" value="<?= e($editar['correo']??'') ?>"></div>
<div class="campo"><label for="telefono">Teléfono</label><input id="telefono" name="telefono" value="<?= e($editar['telefono']??'') ?>"></div>
<button class="boton" type="submit">Guardar</button></form></main>
<?php require __DIR__.'/app/vistas/parciales/pie.php'; ?>
