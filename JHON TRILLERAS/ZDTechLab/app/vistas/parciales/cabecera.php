<?php
// Parcial cabecera (Día 12) — requiere $titulo y $_SESSION['usuario']
$u = $_SESSION['usuario'] ?? ['nombre'=>'','rol'=>''];
?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($titulo ?? 'Panel') ?> — JT.TechLab</title>
<link rel="stylesheet" href="<?= BASE_URL ?>css/tokens.css">
<link rel="stylesheet" href="<?= BASE_URL ?>css/estilos.css">
<link rel="stylesheet" href="<?= BASE_URL ?>css/reporte.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
<script src="<?= BASE_URL ?>js/datos-prueba.js" defer></script>
<script src="<?= BASE_URL ?>js/app.js" defer></script>
</head>
<body>
<div class="panel">
<header class="panel__barra">
<button class="boton-menu" aria-expanded="false" aria-controls="menu-lateral" aria-label="Abrir menú">☰</button>
<span><strong>JT.TechLab</strong> · Jhon Trilleras</span>
<span><?= e($u['nombre']) ?> (<?= e($u['rol']) ?>) · <a href="<?= BASE_URL ?>salir.php">Salir</a></span>
</header>
