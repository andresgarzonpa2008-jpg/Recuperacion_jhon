<?php
declare(strict_types=1);
require_once __DIR__ . '/app/seguridad/sesion.php';
cerrarSesion();
require_once __DIR__ . '/app/config/app.php';
header('Location: ' . BASE_URL . 'login.php'); exit;
