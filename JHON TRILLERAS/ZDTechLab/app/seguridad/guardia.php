<?php
declare(strict_types=1);
// Guardián — primera línea de toda página privada (Día 11)
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/../config/app.php';
iniciarSesionSegura();
if (empty($_SESSION['usuario'])) { header('Location: ' . BASE_URL . 'login.php?m=requiere_ingreso'); exit; }
if (($_SESSION['huella'] ?? '') !== hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '')) {
  cerrarSesion(); header('Location: ' . BASE_URL . 'login.php?m=sesion_invalida'); exit;
}
$ahora = time();
if ($ahora - ($_SESSION['ultima_actividad'] ?? $ahora) > INACTIVIDAD_MAX || $ahora - ($_SESSION['inicio'] ?? $ahora) > SESION_MAX) {
  cerrarSesion(); header('Location: ' . BASE_URL . 'login.php?m=sesion_expirada'); exit;
}
$_SESSION['ultima_actividad'] = $ahora;
function exigirRol(string ...$roles): void {
  if (!in_array($_SESSION['usuario']['rol'], $roles, true)) { http_response_code(403); exit('403 — No tiene permiso para esta operación.'); }
}
function puede(string ...$roles): bool {
  return in_array($_SESSION['usuario']['rol'] ?? '', $roles, true);
}
