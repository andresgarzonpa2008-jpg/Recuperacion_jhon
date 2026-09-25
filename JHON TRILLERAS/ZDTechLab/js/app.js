// Día 8 — DOM, delegación, buscador, menú y validación (sin onclick)
document.addEventListener("DOMContentLoaded", () => {
  const btnMenu = document.querySelector(".boton-menu");
  const menu = document.querySelector(".panel__menu");
  if (btnMenu && menu) {
    btnMenu.addEventListener("click", () => {
      const abierto = menu.classList.toggle("abierto");
      btnMenu.setAttribute("aria-expanded", abierto ? "true" : "false");
    });
  }
  // Pintar tabla demo si existe y no hay filas del servidor
  const tbody = document.querySelector("#tabla-productos tbody");
  const buscador = document.querySelector("#buscador");
  if (tbody && tbody.children.length === 0 && typeof productosPrueba !== "undefined") {
    const pintar = (lista) => {
      tbody.replaceChildren(...lista.map(p => {
        const tr = document.createElement("tr");
        tr.innerHTML = `<td>${p.nombre}</td><td>${moneda.format(p.precio)}</td><td>${p.stock}</td>
        <td><button class="boton-mini" data-accion="editar" data-id="${p.id}">Editar</button>
        <button class="boton-mini boton-peligro" data-accion="eliminar" data-id="${p.id}">Eliminar</button></td>`;
        return tr;
      }));
    };
    pintar(productosPrueba);
    if (buscador) buscador.addEventListener("input", () => {
      const t = buscador.value.toLowerCase();
      pintar(productosPrueba.filter(p => p.nombre.toLowerCase().includes(t)));
    });
    tbody.addEventListener("click", (ev) => {
      const b = ev.target.closest("button[data-accion]");
      if (!b) return;
      const {accion, id} = b.dataset;
      if (accion === "eliminar") b.closest("tr").remove();
    });
  } else if (tbody && buscador) {
    // Filtro en vivo sobre tabla del servidor
    buscador.addEventListener("input", () => {
      const t = buscador.value.toLowerCase();
      tbody.querySelectorAll("tr").forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(t) ? "" : "none";
      });
    });
  }
  // Validación producto
  const form = document.querySelector("#form-producto");
  if (form) {
    form.addEventListener("submit", (ev) => {
      const nombre = form.elements.nombre, precio = form.elements.precio,
            stock = form.elements.stock, cat = form.elements.categoria_id;
      nombre.setCustomValidity(nombre.value.trim().length < 3 ? "El nombre debe tener al menos 3 caracteres." : "");
      precio.setCustomValidity(Number(precio.value) <= 0 ? "El precio debe ser mayor que cero." : "");
      stock.setCustomValidity(!Number.isInteger(Number(stock.value)) || Number(stock.value) < 0 ? "Stock entero no negativo." : "");
      if (cat) cat.setCustomValidity(!cat.value ? "Seleccione una categoría." : "");
      if (!form.checkValidity()) { ev.preventDefault(); form.reportValidity(); }
    });
  }
});
