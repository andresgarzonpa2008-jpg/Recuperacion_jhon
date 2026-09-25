// Día 7 — datos de prueba con la misma forma de MySQL
const productosPrueba = [
  {id:1,nombre:"Teclado mecánico RGB",categoria:"Periféricos",precio:120000,stock:14},
  {id:2,nombre:"Mouse inalámbrico",categoria:"Periféricos",precio:65000,stock:32},
  {id:3,nombre:"Monitor 24 pulg",categoria:"Pantallas",precio:890000,stock:8},
  {id:4,nombre:"SSD 1 TB",categoria:"Almacenamiento",precio:320000,stock:7},
  {id:5,nombre:"Router WiFi 6",categoria:"Redes",precio:260000,stock:12},
  {id:6,nombre:"UPS 1000VA",categoria:"Energía",precio:420000,stock:6},
  {id:7,nombre:"Audífonos gamer",categoria:"Periféricos",precio:95000,stock:20},
  {id:8,nombre:"Monitor 27 pulg 4K",categoria:"Pantallas",precio:1450000,stock:4},
  {id:9,nombre:"SSD 512 GB",categoria:"Almacenamiento",precio:210000,stock:15},
  {id:10,nombre:"Mouse gamer RGB",categoria:"Periféricos",precio:85000,stock:3},
  {id:11,nombre:"Switch 8 puertos",categoria:"Redes",precio:180000,stock:9},
  {id:12,nombre:"Webcam HD",categoria:"Pantallas",precio:160000,stock:13},
  {id:13,nombre:"Memoria USB 64 GB",categoria:"Almacenamiento",precio:45000,stock:50},
  {id:14,nombre:"Regulador 2000W",categoria:"Energía",precio:150000,stock:18},
  {id:15,nombre:"Cable UTP x metro",categoria:"Redes",precio:3500,stock:200}
];
const moneda = new Intl.NumberFormat("es-CO",{style:"currency",currency:"COP",maximumFractionDigits:0});
