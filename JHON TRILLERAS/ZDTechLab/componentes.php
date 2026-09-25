<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Componentes — JT.TechLab</title>
<link rel="stylesheet" href="css/tokens.css"><link rel="stylesheet" href="css/estilos.css"></head>
<body><main class="panel__contenido">
<h1>Guía de componentes JT.TechLab (Día 6)</h1>
<p>Paleta complementaria: verde #39A900 + naranja #F08A00 sobre neutros. Contrastes AA.</p>
<h2>Botones (5 estados: normal, hover, focus-visible, activo, deshabilitado)</h2>
<p><button class="boton">Primario</button> <button class="boton boton--secundario">Secundario</button> <button class="boton boton--peligro">Peligro</button> <button class="boton" disabled>Deshabilitado</button></p>
<h2>Campos + error</h2>
<div class="campo"><label for="c1">Nombre</label><input id="c1" value="Teclado"><span class="error-campo">Ejemplo de error junto al campo.</span></div>
<h2>Tarjeta indicador</h2>
<section class="indicadores"><article class="tarjeta tarjeta--verde"><p class="tarjeta__rotulo">Ventas del mes</p><p class="tarjeta__valor">$ 18.400.000</p></article></section>
<h2>Tabla</h2>
<table class="tabla"><caption>Productos</caption><thead><tr><th>Producto</th><th>Precio</th></tr></thead><tbody><tr><td>Teclado</td><td>$120.000</td></tr></tbody></table>
<h2>Alerta</h2><p class="alerta alerta-exito" role="alert">✓ Éxito: registro guardado.</p>
<p class="alerta alerta-error" role="alert">✕ Error: credenciales incorrectas.</p>
<p><a href="login.php">Ir al login</a></p>
</main></body>
</html>
