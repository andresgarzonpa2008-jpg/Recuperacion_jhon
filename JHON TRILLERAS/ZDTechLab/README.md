# JT.TechLab — Jhon Trilleras · SENA ADSI
Panel de gestión de tienda tecnológica. Plan de mejoramiento 15 días (días 6-15 funcionales).

## Instalar (XAMPP, PHP 8+, MySQL/MariaDB)
1. Copiar la carpeta `ZDTechLab` a `C:\xampp\htdocs\jttechlab` (o abrir `http://localhost/...` según su ruta).
2. Encender Apache + MySQL en XAMPP Control.
3. Abrir `http://localhost/jttechlab/instalar.php` — crea BD `zdtechlab_jt`, tablas, 20 productos, 10 clientes, 15 pedidos, 4 vistas y 3 usuarios.
4. Ingresar en `login.php`:
   - admin@jttechlab.co / Admin2026* (administrador)
   - vendedor@jttechlab.co / Vendedor2026* (vendedor)
   - consultor@jttechlab.co / Consultor2026* (consultor)

## Estructura
- `css/` tokens.css, estilos.css (Grid+Flex, mobile first, responsive), reporte.css (@media print)
- `js/` datos-prueba.js, ejercicios.js (filter/map/reduce), app.js (DOM, delegación, validación), graficos.js (Chart.js)
- `sql/` estructura.sql, datos.sql, vistas.sql (v_ventas_mes, v_ventas_categoria, v_stock_critico, v_clientes_top)
- `app/config/` conexion.php (PDO preparado), credenciales.php (no versionar)
- `app/seguridad/` csrf.php, sesion.php (HttpOnly/SameSite/regeneración), guardia.php + exigirRol
- `app/modelos/` ProductoModelo, ClienteModelo
- `app/vistas/parciales/` cabecera, menu (por rol), pie
- `api/graficos.php` JSON protegido por sesión
- `dashboard.php` 4 indicadores reales + 3 gráficos (barra, dona, línea) + filtro fechas
- `productos.php`, `clientes.php` CRUD con búsqueda, paginación, POST/Redirect/GET, borrado lógico
- `pedidos.php` transacción que descuenta stock
- `reportes.php` 3 reportes con logo, filtros, totales + CSV + Imprimir/PDF
- `componentes.html` guía de estilo día 6

## Seguridad (para sustentar)
- `password_hash/password_verify`, mensajes genéricos, bloqueo 15 min tras 5 fallos, CSRF con `random_bytes+hash_equals`, `htmlspecialchars` contra XSS, PDO con `ATTR_EMULATE_PREPARES=false`, guardián en toda página privada, roles verificados en servidor, cookie HttpOnly+SameSite, `session_regenerate_id(true)`.

## Sustentación (10 min)
1. Flujo formulario→BD (productos.php → ProductoModelo → PDO prepared).
2. Cambiar un campo del CRUD en vivo. 3. Cambiar `type:"bar"` a `"line"` en graficos.js. 4. Justificar paleta: complementario verde-naranja, 60-30-10, contrastes AA.
