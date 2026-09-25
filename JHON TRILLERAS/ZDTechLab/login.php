<?php
declare(strict_types=1);
require_once __DIR__ . '/app/seguridad/sesion.php';
require_once __DIR__ . '/app/seguridad/csrf.php';
require_once __DIR__ . '/app/config/app.php';
require_once __DIR__ . '/app/config/conexion.php';
iniciarSesionSegura();
if (!empty($_SESSION['usuario'])) { header('Location: ' . BASE_URL . 'dashboard.php'); exit; }
$error = '';
if (($_GET['m'] ?? '') === 'requiere_ingreso') $error = 'Debe iniciar sesión primero.';
if (($_GET['m'] ?? '') === 'sesion_expirada') $error = 'Sesión expirada por inactividad.';
if (($_GET['m'] ?? '') === 'sesion_invalida') $error = 'Sesión inválida. Ingrese de nuevo.';

function registrarIntento(PDO $pdo, string $correo, bool $exito): void {
  $st = $pdo->prepare("INSERT INTO intentos_acceso (correo, exito, ip) VALUES (:c,:e,:ip)");
  $st->execute([':c'=>$correo, ':e'=>$exito?1:0, ':ip'=>$_SERVER['REMOTE_ADDR'] ?? null]);
}
function bloqueado(PDO $pdo, string $correo): ?string {
  $st = $pdo->prepare("SELECT bloqueado_hasta FROM usuarios WHERE correo=:c LIMIT 1");
  $st->execute([':c'=>$correo]); $u = $st->fetch();
  if ($u && $u['bloqueado_hasta'] && strtotime($u['bloqueado_hasta']) > time()) return $u['bloqueado_hasta'];
  return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!validarCsrf($_POST['csrf'] ?? null)) { http_response_code(419); exit('Solicitud no válida. Recargue.'); }
  $correo = trim((string)($_POST['correo'] ?? ''));
  $clave = (string)($_POST['clave'] ?? '');
  if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($clave) < 8) {
    $error = 'Correo o contraseña incorrectos.';
  } else {
    try {
      $pdo = Conexion::obtener();
      if (bloqueado($pdo, $correo)) {
        $error = 'Cuenta bloqueada temporalmente. Intente más tarde.';
      } else {
        $st = $pdo->prepare("SELECT id,nombre,clave_hash,rol,activo,bloqueado_hasta FROM usuarios WHERE correo=:c LIMIT 1");
        $st->execute([':c'=>$correo]); $u = $st->fetch();
        if ($u && (int)$u['activo'] === 1 && password_verify($clave, $u['clave_hash'])) {
          if (password_needs_rehash($u['clave_hash'], PASSWORD_DEFAULT)) {
            $nuevo = password_hash($clave, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE usuarios SET clave_hash=:h,bloqueado_hasta=NULL WHERE id=:id")->execute([':h'=>$nuevo,':id'=>$u['id']]);
          } else {
            $pdo->prepare("UPDATE usuarios SET bloqueado_hasta=NULL WHERE id=:id")->execute([':id'=>$u['id']]);
          }
          registrarIntento($pdo, $correo, true);
          abrirSesion($u);
          header('Location: ' . BASE_URL . 'dashboard.php'); exit;
        } else {
          try { registrarIntento($pdo, $correo, false); } catch (Throwable $e) {}
          // Bloqueo tras 5 fallos en 15 min
          try {
            $st2 = $pdo->prepare("SELECT COUNT(*) c FROM intentos_acceso WHERE correo=:c AND exito=0 AND creado_en >= (NOW() - INTERVAL 15 MINUTE)");
            $st2->execute([':c'=>$correo]); $n = (int)$st2->fetch()['c'];
            if ($n >= 5) $pdo->prepare("UPDATE usuarios SET bloqueado_hasta = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE correo=:c")->execute([':c'=>$correo]);
          } catch (Throwable $e) {}
          $error = 'Correo o contraseña incorrectos.';
        }
      }
    } catch (Throwable $e) { $error = 'No hay conexión a la base de datos. Ejecute instalar.php primero.'; }
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Ingreso — JT.TechLab</title>
<link rel="stylesheet" href="<?= BASE_URL ?>css/tokens.css">
<link rel="stylesheet" href="<?= BASE_URL ?>css/estilos.css">
</head>
<body>
<main class="pantalla-ingreso">
<img src="<?= BASE_URL ?>assets/img/logo.svg" alt="Logo de JT.TechLab" width="180">
<h1>Ingreso al panel de gestión</h1>
<p>JT.TechLab · Jhon Trilleras · SENA ADSI</p>
<form method="post" action="login.php" autocomplete="on">
<input type="hidden" name="csrf" value="<?= e(tokenCsrf()) ?>">
<?php if ($error !== ''): ?><p class="alerta alerta-error" role="alert"><?= e($error) ?></p><?php endif; ?>
<fieldset><legend>Credenciales</legend>
<div class="campo"><label for="correo">Correo electrónico</label>
<input type="email" id="correo" name="correo" required autocomplete="username" value="<?= e($_POST['correo'] ?? '') ?>"></div>
<div class="campo"><label for="clave">Contraseña</label>
<input type="password" id="clave" name="clave" required autocomplete="current-password" minlength="8"></div>
</fieldset>
<button class="boton" type="submit">Iniciar sesión</button>
<p style="font-size:.85rem">Prueba: admin@jttechlab.co / Admin2026* · <a href="<?= BASE_URL ?>instalar.php">instalar BD</a></p>
</form>
</main>
</body>
</html>
