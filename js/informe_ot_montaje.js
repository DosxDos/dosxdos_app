// Función para renderizar los informes filtrados en el HTML
function renderInformes(data) {
  const container = document.getElementById("contenedor-informes"); // Buscamos el contenedor
  if (!container) return;

  container.innerHTML = ""; // Limpiamos el contenido anterior para evitar duplicidades

  data.forEach(info => {
    const div = document.createElement("div");
    div.className = "informe"; // Creamos un bloque por informe

    // Construimos el HTML dinámico con los datos de cada informe
    const contenido = document.createElement("div");
    contenido.className = "contenido-informe";

    contenido.innerHTML = `
      <div class="cabecera">
        <div class="logo">
          <img src="elementos_diseno/DOS POR DOS LOGO.png" alt="Logo Empresa">
        </div>
        <div class="titulo">
          <h1>Informe de Montaje</h1>
        </div>
      </div>

      <div class="info-recuadro">
        <p class="ot"><strong>OT:</strong> ${info.ot}</p>
        <p class="empresa">${info.nombreEmpresa}</p>
      </div>

      <div class="info-header">
        <p><strong>Punto de Venta:</strong> ${info.puntoVenta}</p>
        <p><strong>Dirección:</strong> ${info.direccion}</p>
      </div>

      <table>
        <thead>
          <tr>
            <th>Tipo</th>
            <th>Firma</th>
            <th>Quitar</th>
            <th>Poner</th>
            <th>Alto x Ancho</th>
          </tr>
        </thead>
        <tbody>
          ${info.detalles.map(item => `
            <tr>
              <td>${item.tipo}</td>
              <td>${item.firma}</td>
              <td>${item.quitar}</td>
              <td>${item.poner}</td>
              <td>${item.dimensiones}</td>
            </tr>
          `).join('')}
        </tbody>
      </table>
    `;

    // Creamos el footer de firmas (estático)
    const footer = document.createElement("div");
    footer.className = "firmas-recuadro";
    footer.innerHTML = `
      <div class="firma-fecha">
        <p><strong>Firma del Instalador:</strong></p>
        <div class="linea-firma"></div>

        <p><strong>Fecha:</strong></p>
        <div class="linea-firma"></div>
      </div>
      <div class="sello-cliente">
        <p><strong>Sello del Cliente:</strong></p>
        <div class="cuadro-sello"></div>
      </div>
    `;

    // Insertamos contenido y footer dentro del informe
    div.appendChild(contenido);
    div.appendChild(footer);

    container.appendChild(div);
  });

  // Activamos el botón para generar PDF una vez renderizado
  const botonPDF = document.getElementById("btnGenerarPDF");
  if (botonPDF) {
    botonPDF.disabled = false;
    botonPDF.style.display = "inline-block";
  }

  // Verificamos visualmente los datos por consola
  console.log("🔍 Informes renderizados:", data);
}

// Función para generar el PDF del contenido HTML
function generarPDF() {
  console.log("🟡 Botón presionado - iniciando generación PDF");
  const element = document.getElementById("contenedor-informes"); // Seleccionamos el contenedor

  if (!element) {
    console.error("❌ No se encontró el contenedor de informes");
    return;
  }

  // Pequeño retraso para asegurarnos de que todo está visible/renderizado
  setTimeout(() => {
    html2pdf()
      .set({
        margin: 10,
        filename: "informe_ot_montajes.pdf",
        image: { type: "jpeg", quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: "mm", format: "a4", orientation: "portrait" }
      })
      .from(element)
      .save()
      .then(() => {
        console.log("🟢 PDF generado y descargado");
      })
      .catch((error) => {
        console.error("❌ Error al generar el PDF:", error);
      });
  }, 300);
}

window.generarPDF = generarPDF; // Registramos la función globalmente

// Función para filtrar los datos por fecha seleccionada
function filtrarDataPorFecha(dataOriginal, fecha) {
  const ot = dataOriginal.ot;
  const resultado = [];

  dataOriginal.pvs.forEach(pv => {
    const lineasFiltradas = (pv.lineas || []).filter(linea => linea.fechaEntrada === fecha);
    if (lineasFiltradas.length === 0) return;

    resultado.push({
      ot: ot.codOt,
      nombreEmpresa: ot.firma,
      puntoVenta: pv.nombre,
      direccion: pv.direccion,
      detalles: lineasFiltradas.map(linea => ({
        tipo: linea.tipo || "",
        firma: ot.firma || "",
        quitar: linea.quitar || "",
        poner: linea.poner || "",
        dimensiones: `${linea.alto || '-'} x ${linea.ancho || '-'}`
      }))
    });
  });

  return resultado;
}

// Al cargar la página, hacemos fetch al PHP y guardamos en localStorage
document.addEventListener("DOMContentLoaded", () => {
  const botonPDF = document.getElementById("btnGenerarPDF");
  if (botonPDF) {
    botonPDF.disabled = true; // Desactivamos al inicio como capa de seguridad
    botonPDF.style.display = "none";
  }

  // Obtenemos los parámetros desde la URL (necesarios para el fetch)
  const params = new URLSearchParams(window.location.search);
  const idOt = params.get("idOt");
  const codOt = params.get("codOt");
  const tipoOt = params.get("tipoOt");
  const cliente = params.get("cliente");
  const tokenJwt = params.get("tokenJwt");

  // Validamos que existan los parámetros obligatorios
  if (!idOt || !codOt || !tipoOt || !cliente || !tokenJwt) {
    console.warn("⚠️ Faltan parámetros en la URL (idOt, codOt, tipoOt, cliente o tokenJwt)");
    return;
  }

  // Construimos la URL para el fetch
  const url = `apirest/informe_ot_montajes.php?idOt=${idOt}&codOt=${encodeURIComponent(codOt)}&tipoOt=${encodeURIComponent(tipoOt)}&cliente=${encodeURIComponent(cliente)}&tokenJwt=${encodeURIComponent(tokenJwt)}`;

  // Hacemos la petición al backend
  fetch(url)
    .then(res => {
      if (!res.ok) throw new Error(`HTTP error ${res.status}`);
      return res.json(); // Esperamos respuesta en JSON
    })
    .then(data => {
      // Comprobamos que data.pvs exista y sea un array
      if (!data.pvs || !Array.isArray(data.pvs)) {
        console.error("❌ data.pvs no es un array o está vacío", data.pvs);
        return;
      }

      // Guardamos datos originales para uso posterior
      localStorage.setItem("informesOTRaw", JSON.stringify(data));
      console.log("🟢 Datos crudos guardados:", data);

      // Obtenemos todas las fechas únicas de las líneas
      const fechas = new Set();
      data.pvs.forEach(pv => {
        if (!pv.lineas || !Array.isArray(pv.lineas)) {
          console.warn("⚠️ pv.lineas no es array o no existe", pv.lineas);
          return;
        }
        pv.lineas.forEach(linea => {
          if (linea.fechaEntrada) fechas.add(linea.fechaEntrada);
        });
      });

      const selector = document.getElementById("selector-fecha");
      if (!selector) {
        console.error("❌ No se encontró el elemento select con id 'selector-fecha'");
        return;
      }

      selector.innerHTML = `<option value="">Selecciona una fecha</option>`;
      [...fechas].sort().forEach(f => {
        const opt = document.createElement("option");
        opt.value = f;
        opt.textContent = f;
        selector.appendChild(opt);
      });

      // Evento: al seleccionar una fecha, renderizar los informes
      selector.addEventListener("change", () => {
        const fechaSeleccionada = selector.value;
        if (!fechaSeleccionada) return;

        const dataFiltrada = filtrarDataPorFecha(data, fechaSeleccionada);
        renderInformes(dataFiltrada);
      });
    })
    .catch(err => {
      console.error("❌ Error al cargar los datos:", err);
    });
});
