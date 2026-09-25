<?php
declare(strict_types=1);
// Datos institucionales + BASE_URL (Día 12 y 15)
const APP = [
  'nombre' => 'JT.TechLab',
  'lema' => 'Sistema de gestión de tienda tecnológica',
  'version' => '1.0',
  'nit' => '900.123.456-7',
  'ciudad' => 'Bogotá D.C.',
  'correo' => 'soporte@jttechlab.co',
  'autor' => 'Jhon Trilleras',
];
if (!defined('BASE_URL')) {
  $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
  // Si el script está en subcarpeta (ej. /jttechlab), conservarla; si es raíz, '/'
  define('BASE_URL', ($base === '' || $base === '.') ? '/' : $base . '/');
}
function e(?string $v): string { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
function aviso(): ?array {
  if (!empty($_SESSION['aviso'])) { $a = $_SESSION['aviso']; unset($_SESSION['aviso']); return $a; }
  return null;
}
