<?php
declare(strict_types=1);
// Instalador: crea BD, tablas, datos, vistas y 3 usuarios (admin/vendedor/consultor)
$cfg = require __DIR__ . '/app/config/credenciales.php';
$mensajes = [];
try {
  $pdo0 = new PDO("mysql:host={$cfg['host']};port={$cfg['puerto']};charset=utf8mb4", $cfg['usuario'], $cfg['clave'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
  foreach (['sql/estructura.sql','sql/datos.sql','sql/vistas.sql'] as $f) {
    $sql = file_get_contents(__DIR__ . '/' . $f);
    // Ejecutar por bloques (separar CREATE/VIEW/INSERT funciona con exec múltiple en MySQL)
    $pdo0->exec("CREATE DATABASE IF NOT EXISTS `{$cfg['bd']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo1 = new PDO("mysql:host={$cfg['host']};port={$cfg['puerto']};dbname={$cfg['bd']};charset=utf8mb4", $cfg['usuario'], $cfg['clave'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    // Dividir por ';' + salto para vistas con seguridad
    $bloques = array_filter(array_map('trim', explode(";\n", $sql)));
    foreach ($bloques as $b) { if ($b !== '') $pdo1->exec($b); }
    $mensajes[] = "OK $f";
  }
  require_once __DIR__ . '/app/config/conexion.php';
  $pdo = Conexion::obtener();
  $usuarios = [
    ['Administrador JT','admin@jttechlab.co','Admin2026*','administrador'],
    ['Vendedor JT','vendedor@jttechlab.co','Vendedor2026*','vendedor'],
    ['Consultor JT','consultor@jttechlab.co','Consultor2026*','consultor'],
  ];
  foreach ($usuarios as [$n,$c,$k,$r]) {
    $h = password_hash($k, PASSWORD_DEFAULT);
    $st = $pdo->prepare("INSERT INTO usuarios (nombre,correo,clave_hash,rol,activo) VALUES (:n,:c,:h,:r,1) ON DUPLICATE KEY UPDATE nombre=VALUES(nombre), clave_hash=VALUES(clave_hash), rol=VALUES(rol), activo=1, bloqueado_hasta=NULL");
    $st->execute([':n'=>$n,':c'=>$c,':h'=>$h,':r'=>$r]);
  }
  $mensajes[] = 'Usuarios creados: admin@jttechlab.co / Admin2026*, vendedor@ / Vendedor2026*, consultor@ / Consultor2026*';
} catch (Throwable $e) { $mensajes[] = 'ERROR: ' . $e->getMessage(); }
?><!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Instalar — JT.TechLab</title>
<link rel="stylesheet" href="css/estilos.css"></head><body><main class="pantalla-ingreso">
<h1>Instalación JT.TechLab</h1><ul><?php foreach ($mensajes as $m): ?><li><?= htmlspecialchars($m,ENT_QUOTES,'UTF-8') ?></li><?php endforeach; ?></ul>
<p><a class="boton" href="login.php">Ir al ingreso</a></p></main></body></html>
