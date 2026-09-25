-- JT.TechLab — sql/vistas.sql (Día 14: 4 vistas mínimo)
USE zdtechlab_jt;

-- 1. Ventas consolidadas por mes
CREATE OR REPLACE VIEW v_ventas_mes AS
SELECT DATE_FORMAT(p.fecha,'%Y-%m') AS periodo,
  COUNT(DISTINCT p.id) AS cantidad_pedidos,
  SUM(d.cantidad) AS unidades,
  SUM(d.cantidad*d.precio_unitario) AS total_vendido
FROM pedidos p INNER JOIN detalle_pedido d ON d.pedido_id=p.id
WHERE p.estado='confirmado'
GROUP BY DATE_FORMAT(p.fecha,'%Y-%m');

-- 2. Participación por categoría
CREATE OR REPLACE VIEW v_ventas_categoria AS
SELECT c.id AS categoria_id, c.nombre AS categoria,
  COALESCE(SUM(d.cantidad),0) AS unidades,
  COALESCE(SUM(d.cantidad*d.precio_unitario),0) AS total_vendido
FROM categorias c
LEFT JOIN productos pr ON pr.categoria_id=c.id
LEFT JOIN detalle_pedido d ON d.producto_id=pr.id
LEFT JOIN pedidos p ON p.id=d.pedido_id AND p.estado='confirmado'
GROUP BY c.id, c.nombre;

-- 3. Stock crítico
CREATE OR REPLACE VIEW v_stock_critico AS
SELECT pr.id, pr.nombre, c.nombre AS categoria, pr.stock, pr.stock_minimo, pr.precio
FROM productos pr INNER JOIN categorias c ON c.id=pr.categoria_id
WHERE pr.activo=1 AND pr.stock<=pr.stock_minimo;

-- 4. Clientes con mayor compra acumulada
CREATE OR REPLACE VIEW v_clientes_top AS
SELECT cl.id, cl.nombre, cl.documento, COUNT(DISTINCT p.id) AS pedidos,
  COALESCE(SUM(d.cantidad*d.precio_unitario),0) AS total_comprado
FROM clientes cl
LEFT JOIN pedidos p ON p.cliente_id=cl.id AND p.estado='confirmado'
LEFT JOIN detalle_pedido d ON d.pedido_id=p.id
GROUP BY cl.id, cl.nombre, cl.documento;
