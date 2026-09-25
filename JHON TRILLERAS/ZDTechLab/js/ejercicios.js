// Día 7 — ejercicios con filter/map/reduce/sort (sin for)
const masCaro = productosPrueba.reduce((a,b)=> b.precio>a.precio?b:a);
const porCategoria = productosPrueba.reduce((acc,p)=>{acc[p.categoria]=(acc[p.categoria]??0)+p.stock;return acc;},{});
const stockBajo = productosPrueba.filter(p=>p.stock<5);
const promedio = productosPrueba.map(p=>p.precio).reduce((a,b)=>a+b,0)/productosPrueba.length;
console.log("Más caro:", masCaro.nombre, moneda.format(masCaro.precio));
console.log("Por categoría:", porCategoria);
console.table(stockBajo);
console.log("Promedio:", moneda.format(Math.round(promedio)));
// map vs forEach (bitácora): map devuelve un arreglo nuevo, forEach no devuelve nada (undefined) y solo itera.
