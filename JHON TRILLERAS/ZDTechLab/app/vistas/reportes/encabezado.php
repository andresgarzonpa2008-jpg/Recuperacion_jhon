<?php
// Encabezado reutilizable de reportes (Día 15)
$numeroReporte = $numeroReporte ?? ('R-'.date('Y').'-'.str_pad((string)random_int(1,9999),4,'0',STR_PAD_LEFT));
$logoPath = __DIR__ . '/../../../assets/img/logo.svg';
$logoSrc = BASE_URL . 'assets/img/logo.svg';
?>
<header class="reporte__encabezado">
<img src="<?= $logoSrc ?>" alt="Logo de JT.TechLab" class="reporte__logo" width="64">
<div class="reporte__marca">
<h1><?= APP['nombre'] ?></h1>
<p><?= APP['lema'] ?> · v<?= APP['version'] ?></p>
<p>NIT <?= APP['nit'] ?> · <?= APP['ciudad'] ?> · <?= APP['correo'] ?></p>
</div>
<div class="reporte__meta">
<p>Generado: <?= date('d/m/Y H:i') ?></p>
<p>Usuario: <?= e($_SESSION['usuario']['nombre'] ?? '') ?> (<?= e($_SESSION['usuario']['rol'] ?? '') ?>)</p>
<p>Reporte N.º <?= e($numeroReporte) ?></p>
</div>
</header>
