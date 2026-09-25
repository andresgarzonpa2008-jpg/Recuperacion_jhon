# EVIDENCIAS AUTOMÁTICAS — 24/09/2026 17:45
## Usuarios (hash bcrypt, no texto plano)
- admin@jttechlab.co → $2y$10$... (60 chars)
- vendedor@jttechlab.co → $2y$10$... (60 chars)
- consultor@jttechlab.co → $2y$10$... (60 chars)
## Vistas (4)
- v_clientes_top
- v_stock_critico
- v_ventas_categoria
- v_ventas_mes
## Conteos
- usuarios: 3
- categorias: 5
- productos: 20
- clientes: 10
- pedidos: 15
- detalle_pedido: 21
- intentos_acceso: 8
## Prueba inyección (buscador con `' OR '1'='1` debe devolver 0 o solo coincidencias literales)
- Resultado: 0 filas (inyección neutralizada por sentencia preparada)
## Prueba XSS (nombre con <script> debe guardarse como texto)
- login.php usa htmlspecialchars() en errores y cabecera.php en nombre/rol. Verificado en código.