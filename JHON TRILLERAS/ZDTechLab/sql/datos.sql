-- JT.TechLab — sql/datos.sql (Día 9: 20 productos, 10 clientes, 15 pedidos)
USE zdtechlab_jt;

INSERT INTO categorias (nombre, descripcion) VALUES
('Periféricos','Teclados, mouse, audífonos'),
('Pantallas','Monitores y pantallas'),
('Almacenamiento','SSD, HDD, USB'),
('Redes','Routers, switches, cableado'),
('Energía','UPS, reguladores')
ON DUPLICATE KEY UPDATE nombre=VALUES(nombre);

INSERT INTO productos (nombre, categoria_id, precio, stock, stock_minimo) VALUES
('Teclado mecánico RGB',1,120000,14,5),('Mouse inalámbrico',1,65000,32,5),
('Audífonos gamer',1,95000,20,5),('Monitor 24 pulg',2,890000,8,3),
('Monitor 27 pulg 4K',2,1450000,4,2),('SSD 1 TB',3,320000,7,5),
('SSD 512 GB',3,210000,15,5),('Disco HDD 2 TB',3,280000,10,4),
('Memoria USB 64 GB',3,45000,50,10),('Router WiFi 6',4,260000,12,4),
('Switch 8 puertos',4,180000,9,3),('Cable UTP x metro',4,3500,200,30),
('UPS 1000VA',5,420000,6,2),('Regulador 2000W',5,150000,18,5),
('Teclado membrana',1,55000,25,8),('Mouse gamer RGB',1,85000,3,5),
('Base refrigerante',1,70000,11,4),('Webcam HD',2,160000,13,4),
('NVMe 1 TB Gen4',3,410000,5,3),('Extensor WiFi',4,130000,16,5)
ON DUPLICATE KEY UPDATE precio=VALUES(precio);

INSERT INTO clientes (nombre, documento, correo, telefono) VALUES
('Laura Gómez','1020123451','laura@mail.co','3001112233'),
('Carlos Ruiz','80123456','carlos@mail.co','3114445566'),
('Tienda El Chip','900123111-1','chip@mail.co','6015556677'),
('Ana Torres','1033445566','ana@mail.co','3207778899'),
('Pedro León','79112233','pedro@mail.co','3159990011'),
('Sofía Díaz','1011223344','sofia@mail.co','3012223344'),
('Empresa Andina','900555222-3','andina@mail.co','6013334455'),
('Diego Mora','1022334455','diego@mail.co','3186667788'),
('Lucía Peña','1044556677','lucia@mail.co','3190001122'),
('Café Internet Central','900777888-9','cafe@mail.co','6012229999')
ON DUPLICATE KEY UPDATE nombre=VALUES(nombre);

-- 15 pedidos de ejemplo (septiembre 2026)
INSERT INTO pedidos (cliente_id, fecha, total, estado) VALUES
(1,'2026-09-02 10:00:00',185000,'confirmado'),(2,'2026-09-03 11:30:00',890000,'confirmado'),
(3,'2026-09-04 09:15:00',640000,'confirmado'),(4,'2026-09-05 14:00:00',225000,'confirmado'),
(5,'2026-09-06 16:20:00',420000,'confirmado'),(1,'2026-09-08 10:10:00',65000,'confirmado'),
(6,'2026-09-09 12:00:00',1450000,'confirmado'),(7,'2026-09-10 08:40:00',540000,'confirmado'),
(8,'2026-09-11 15:30:00',320000,'confirmado'),(9,'2026-09-12 09:00:00',95000,'confirmado'),
(10,'2026-09-13 13:00:00',360000,'confirmado'),(3,'2026-09-14 10:30:00',260000,'confirmado'),
(2,'2026-09-15 11:00:00',130000,'confirmado'),(4,'2026-09-16 17:00:00',890000,'confirmado'),
(5,'2026-09-17 12:30:00',210000,'confirmado');

INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio_unitario) VALUES
(1,1,1,120000),(1,2,1,65000),(2,4,1,890000),(3,6,2,320000),
(4,3,1,95000),(4,15,1,55000),(4,16,1,85000),(5,13,1,420000),
(6,2,1,65000),(7,5,1,1450000),(8,10,1,260000),(8,9,2,45000),
(8,12,50,3500),(9,6,1,320000),(10,18,1,160000),
(10,9,1,45000),(11,11,2,180000),(12,10,1,260000),
(13,20,1,130000),(14,4,1,890000),(15,7,1,210000);
