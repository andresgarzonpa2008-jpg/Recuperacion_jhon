// Día 14 — gráficos Chart.js desde vistas (vía api/graficos.php)
const PALETA = ["#39A900","#15506B","#F08A00","#8FD46A","#B0209E","#00A0C6"];
const monedaCOP = new Intl.NumberFormat("es-CO",{style:"currency",currency:"COP",maximumFractionDigits:0});
let graficoVentas, graficoCategorias, graficoPedidos;
async function dibujarGraficos(desde="", hasta=""){
  const url = "api/graficos.php" + ((desde||hasta) ? `?desde=${encodeURIComponent(desde)}&hasta=${encodeURIComponent(hasta)}` : "");
  let datos;
  try { datos = await fetch(url,{credentials:"same-origin"}).then(r=>r.json()); }
  catch(e){ return; }
  const cv1 = document.getElementById("g-ventas"), cv2 = document.getElementById("g-categorias"), cv3 = document.getElementById("g-pedidos");
  if (cv1 && window.Chart) {
    graficoVentas?.destroy();
    graficoVentas = new Chart(cv1,{type:"bar",
      data:{labels:datos.ventasMes.etiquetas,datasets:[{label:"Ventas del mes",data:datos.ventasMes.valores,backgroundColor:PALETA[0],borderRadius:2}]},
      options:{responsive:true,maintainAspectRatio:false,
        plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>monedaCOP.format(c.parsed.y)}}},
        scales:{y:{beginAtZero:true,ticks:{callback:v=>(v/1000000)+" M"}}}}});
  }
  if (cv2 && window.Chart) {
    graficoCategorias?.destroy();
    graficoCategorias = new Chart(cv2,{type:"doughnut",
      data:{labels:datos.categorias.etiquetas,datasets:[{data:datos.categorias.valores,backgroundColor:PALETA}]},
      options:{responsive:true,maintainAspectRatio:false,cutout:"62%",
        plugins:{legend:{position:"right"},tooltip:{callbacks:{label:c=>`${c.label}: ${monedaCOP.format(c.parsed)}`}}}}});
  }
  if (cv3 && window.Chart) {
    graficoPedidos?.destroy();
    graficoPedidos = new Chart(cv3,{type:"line",
      data:{labels:datos.ventasMes.etiquetas,datasets:[{label:"Pedidos",data:datos.pedidosMes.valores,borderColor:PALETA[1],backgroundColor:PALETA[1],tension:.3}]},
      options:{responsive:true,maintainAspectRatio:false}});
  }
}
document.addEventListener("DOMContentLoaded", () => {
  dibujarGraficos();
  const f = document.querySelector("#filtro-fechas");
  if (f) f.addEventListener("submit",(ev)=>{ev.preventDefault();dibujarGraficos(f.elements.desde.value,f.elements.hasta.value);});
});
