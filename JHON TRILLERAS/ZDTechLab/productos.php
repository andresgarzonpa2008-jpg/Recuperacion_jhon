<?php
require_once __DIR__ . '/app/seguridad/guardia.php';
exigirRol('administrador','vendedor');
require_once __DIR__ . '/app/config/conexion.php';
require_once __DIR__ . '/app/modelos/ProductoModelo.php';
$pdo = Conexion::obtener(); $modelo = new ProductoModelo($pdo);
$b = trim((string)($_GET['b'] ?? '')); $pag = max(1,(int)($_GET['pag'] ?? 1));
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!validarCsrf($_POST['csrf'] ?? null)) { http_response_code(419); exit('CSRF inválido'); }
  $accion = $_POST['accion'] ?? 'guardar';
  if ($accion === 'eliminar') {
    exigirRol('administrador');
    $modelo->desactivar((int)$_POST['id']);
    $_SESSION['aviso'] = ['tipo'=>'exito','texto'=>'Producto desactivado (borrado lógico).'];
    header('Location: productos.php', true, 303); exit;
  }
  $errores = [];
  $nombre = trim((string)($_POST['nombre'] ?? ''));
  $precio = filter_input(INPUT_POST,'precio',FILTER_VALIDATE_FLOAT);
  $stock = filter_input(INPUT_POST,'stock',FILTER_VALIDATE_INT);
  $catId = filter_input(INPUT_POST,'categoria_id',FILTER_VALIDATE_INT);
  if (mb_strlen($nombre) < 3) $errores[] = 'El nombre debe tener al menos 3 caracteres.';
  if ($precio === false || $precio <= 0) $errores[] = 'El precio debe ser mayor que cero.';
  if ($stock === false || $stock < 0) $errores[] = 'El stock no puede ser negativo.';
  if (!$catId) $errores[] = 'Seleccione una categoría.';
  if (!$errores) {
    $id = (int)($_POST['id'] ?? 0);
    $datos = ['nombre'=>$nombre,'precio'=>$precio,'stock'=>$stock,'categoria_id'=>$catId];
    $id > 0 ? $modelo->actualizar($id,$datos) : $modelo->crear($datos);
    $_SESSION['aviso'] = ['tipo'=>'exito','texto'=>'Producto guardado correctamente.'];
    header('Location: productos.php', true, 303); exit;
  }
  $_SESSION['aviso'] = ['tipo'=>'error','texto'=>implode(' ',$errores)];
  header('Location: productos.php', true, 303); exit;
}
$filas = $modelo->listar($b,$pag,10); $total = $modelo->contar($b); $paginas = max(1,(int)ceil($total/10));
$cats = $modelo->categorias(); $editar = null;
if (!empty($_GET['editar'])) $editar = $modelo->obtener((int)$_GET['editar']);
$titulo='Productos';
require __DIR__.'/app/vistas/parciales/cabecera.php'; require __DIR__.'/app/vistas/parciales/menu.php';
$a = aviso();
?>
<main class="panel__contenido">
<h1>Productos</h1>
<?php if ($a): ?><p class="alerta <?= $a['tipo']==='exito'?'alerta-exito':'alerta-error' ?>" role="alert"><?= e($a['texto']) ?></p><?php endif; ?>
<div class="toolbar">
<form method="get" action="productos.php"><div class="campo"><label for="buscador">Buscar</label><input id="buscador" name="b" value="<?= e($b) ?>"></div>
<button class="boton" type="submit">Buscar</button></form>
</div>
<table class="tabla" id="tabla-productos">
<caption>Listado (<?= $total ?> registros · borrado lógico)</caption>
<thead><tr><th scope="col">Producto</th><th scope="col">Categoría</th><th scope="col">Precio</th><th scope="col">Stock</th><th scope="col">Acciones</th></tr></thead>
<tbody>
<?php foreach ($filas as $p): ?>
<tr><td><?= e($p['nombre']) ?></td><td><?= e($p['categoria']) ?></td>
<td>$ <?= number_format((float)$p['precio'],0,',','.') ?></td><td><?= (int)$p['stock'] ?></td>
<td><a class="boton-mini" href="productos.php?editar=<?= (int)$p['id'] ?>">Editar</a>
<form method="post" action="productos.php" style="display:inline" onsubmit="return confirm('¿Desactivar este producto?')">
<input type="hidden" name="csrf" value="<?= e(tokenCsrf()) ?>"><input type="hidden" name="accion" value="eliminar">
<input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
<button class="boton-mini boton-peligro" type="submit" <?= puede('administrador')?'':'disabled' ?>>Eliminar</button></form></td></tr>
<?php endforeach; ?>
</tbody>
</table>
<div class="paginacion"><?php for($i=1;$i<=$paginas;$i++): ?><a href="productos.php?b=<?= urlencode($b) ?>&pag=<?= $i ?>"> <?= $i ?> </a><?php endfor; ?></div>
<h2><?= $editar?'Editar':'Nuevo' ?> producto</h2>
<form id="form-producto" method="post" action="productos.php">
<input type="hidden" name="csrf" value="<?= e(tokenCsrf()) ?>">
<input type="hidden" name="id" value="<?= (int)($editar['id'] ?? 0) ?>">
<div class="campo"><label for="nombre">Nombre</label><input id="nombre" name="nombre" required minlength="3" value="<?= e($editar['nombre'] ?? '') ?>"><span class="error-campo" id="err-nombre"></span></div>
<div class="campo"><label for="categoria_id">Categoría</label><select id="categoria_id" name="categoria_id" required>
<option value="">— Seleccione —</option>
<?php foreach ($cats as $c): ?><option value="<?= (int)$c['id'] ?>" <?= ((int)($editar['categoria_id']??0)===(int)$c['id'])?'selected':'' ?>><?= e($c['nombre']) ?></option><?php endforeach; ?>
</select></div>
<div class="campo"><label for="precio">Precio</label><input id="precio" name="precio" type="number" step="1" min="1" required value="<?= e((string)($editar['precio'] ?? '')) ?>"></div>
<div class="campo"><label for="stock">Stock</label><input id="stock" name="stock" type="number" step="1" min="0" required value="<?= e((string)($editar['stock'] ?? '0')) ?>"></div>
<button class="boton" type="submit">Guardar</button>
</form>
</main>
<?php require __DIR__.'/app/vistas/parciales/pie.php'; ?>
