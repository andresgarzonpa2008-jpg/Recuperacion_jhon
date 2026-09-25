<?php
require_once __DIR__ . '/app/seguridad/guardia.php';
exigirRol('administrador');
require_once __DIR__ . '/app/config/conexion.php';
$pdo=Conexion::obtener();
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!validarCsrf($_POST['csrf'] ?? null)) { http_response_code(419); exit; }
  $n=trim((string)($_POST['nombre']??'')); $c=trim((string)($_POST['correo']??''));
  $k=(string)($_POST['clave']??''); $r=$_POST['rol']??'vendedor';
  if (mb_strlen($n)<3 || !filter_var($c,FILTER_VALIDATE_EMAIL) || strlen($k)<8 || !in_array($r,['administrador','vendedor','consultor'],true))
    $_SESSION['aviso']=['tipo'=>'error','texto'=>'Datos inválidos (nombre 3+, correo válido, clave 8+, rol válido).'];
  else {
    try { $pdo->prepare("INSERT INTO usuarios (nombre,correo,clave_hash,rol) VALUES (:n,:c,:h,:r)")
      ->execute([':n'=>$n,':c'=>$c,':h'=>password_hash($k,PASSWORD_DEFAULT),':r'=>$r]);
      $_SESSION['aviso']=['tipo'=>'exito','texto'=>'Usuario creado.']; }
    catch (Throwable $e) { $_SESSION['aviso']=['tipo'=>'error','texto'=>'Correo duplicado.']; }
  }
  header('Location: usuarios.php', true, 303); exit;
}
$us=$pdo->query("SELECT id,nombre,correo,rol,activo,creado_en FROM usuarios ORDER BY id")->fetchAll();
$titulo='Usuarios'; require __DIR__.'/app/vistas/parciales/cabecera.php'; require __DIR__.'/app/vistas/parciales/menu.php';
$a=aviso();
?>
<main class="panel__contenido"><h1>Usuarios (solo administrador)</h1>
<?php if($a): ?><p class="alerta <?= $a['tipo']==='exito'?'alerta-exito':'alerta-error' ?>"><?= e($a['texto']) ?></p><?php endif; ?>
<table class="tabla"><thead><tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Activo</th></tr></thead><tbody>
<?php foreach($us as $u): ?><tr><td><?= e($u['nombre']) ?></td><td><?= e($u['correo']) ?></td><td><?= e($u['rol']) ?></td><td><?= (int)$u['activo'] ?></td></tr><?php endforeach; ?>
</tbody></table>
<h2>Nuevo usuario (hash bcrypt automático)</h2>
<form method="post"><input type="hidden" name="csrf" value="<?= e(tokenCsrf()) ?>">
<div class="campo"><label for="nombre">Nombre</label><input id="nombre" name="nombre" required minlength="3"></div>
<div class="campo"><label for="correo">Correo</label><input id="correo" name="correo" type="email" required></div>
<div class="campo"><label for="clave">Contraseña (8+)</label><input id="clave" name="clave" type="password" required minlength="8"></div>
<div class="campo"><label for="rol">Rol</label><select id="rol" name="rol"><option value="vendedor">vendedor</option><option value="administrador">administrador</option><option value="consultor">consultor</option></select></div>
<button class="boton" type="submit">Crear</button></form></main>
<?php require __DIR__.'/app/vistas/parciales/pie.php'; ?>
