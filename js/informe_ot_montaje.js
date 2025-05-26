// Función para renderizar los informes filtrados en el HTML
function renderInformes(data) {
  const container = document.getElementById("contenedor-informes");
  if (!container) return;

  container.innerHTML = ""; // Limpiamos el contenido anterior

  data.forEach(info => {
    const div = document.createElement("div");
    div.className = "informe";

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
        <p><strong>Teléfono:</strong> ${info.telefono}</p>
        <p><strong>Área:</strong> ${info.area}</p>
        <p><strong>Zona:</strong> ${info.zona}</p>
        <p><strong>Nombre OT:</strong> ${info.nombreOt}</p>
      </div>

      <table>
  <thead>
    <tr>
      <th>Línea</th>
      <th>Ubicación</th>
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
        <td>${item.linea || '-'}</td>
        <td>${item.ubicacion || '-'}</td>
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

    const footer = document.createElement("div");
    footer.className = "firmas-recuadro";
    footer.innerHTML = `
      <div class="firma-fecha">
      <p><strong>Fecha:</strong></p>
        
        <div class="linea-firma"></div>
        <p><strong>Firma del Instalador:</strong></p>
        <div class="linea-firma"></div>
      </div>
      <div class="sello-cliente">
        <p><strong>Sello del Cliente:</strong></p>
        <div class="cuadro-sello"></div>
      </div>
    `;

    div.appendChild(contenido);
    div.appendChild(footer);

    container.appendChild(div);
  });

  const botonPDF = document.getElementById("btnGenerarPDF");
  if (botonPDF) {
    botonPDF.disabled = false;
    botonPDF.style.display = "inline-block";
  }

  console.log("🔍 Informes renderizados:", data);
}

// Función para generar el PDF del contenido HTML
function generarPDF() {
  console.log("🟡 Botón presionado - iniciando generación PDF");
  const element = document.getElementById("contenedor-informes");

  if (!element) {
    console.error("❌ No se encontró el contenedor de informes");
    return;
  }

  setTimeout(() => {
    html2pdf()
      .set({
        margin: [10, 10, 0, 10],
        filename: "informe_ot_montajes.pdf",
        image: { type: "jpeg", quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: "mm", format: "a4", orientation: "portrait" },
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

window.generarPDF = generarPDF;

// Función para filtrar los datos por múltiples fechas seleccionadas
function filtrarDataPorFechas(dataOriginal, fechasSeleccionadas) {
  const ot = dataOriginal.ot;
  const resultado = [];

  dataOriginal.pvs.forEach(pv => {
    const lineasFiltradas = (pv.lineas || []).filter(linea =>
      fechasSeleccionadas.includes(linea.fechaEntrada)
    );
    if (lineasFiltradas.length === 0) return;

    resultado.push({
      ot: ot.codOt,
      cliente: ot.cliente,
      nombreEmpresa: ot.cliente, // reemplazamos firma por cliente
      puntoVenta: pv.nombre,
      direccion: pv.direccion,
      telefono: pv.telefono,
      area: pv.area,
      zona: pv.zona,
      nombreOt: pv.nombreOt,
      detalles: lineasFiltradas.map(linea => ({
        tipo: linea.tipo || "",
        firma: ot.firma || "", // se mantiene solo en la tabla inferior
        quitar: linea.quitar || "",
        poner: linea.poner || "",
        dimensiones: `${linea.alto || '-'} x ${linea.ancho || '-'}`,
        linea: linea.linea || "", // si tienes este dato
        ubicacion: linea.ubicacion || "" // si tienes este dato
      }))
    });

  });

  return resultado;
}

document.addEventListener("DOMContentLoaded", () => {
  const botonPDF = document.getElementById("btnGenerarPDF");
  if (botonPDF) {
    botonPDF.disabled = true;
    botonPDF.style.display = "none";
  }

  const params = new URLSearchParams(window.location.search);
  const idOt = params.get("idOt");
  const codOt = params.get("codOt");
  const tipoOt = params.get("tipoOt");
  const cliente = params.get("cliente");
  const tokenJwt = params.get("tokenJwt");

  if (!idOt || !codOt || !tipoOt || !cliente || !tokenJwt) {
    console.warn("⚠️ Faltan parámetros en la URL (idOt, codOt, tipoOt, cliente o tokenJwt)");
    return;
  }

  const url = `apirest/informe_ot_montajes.php?idOt=${idOt}&codOt=${encodeURIComponent(codOt)}&tipoOt=${encodeURIComponent(tipoOt)}&cliente=${encodeURIComponent(cliente)}&tokenJwt=${encodeURIComponent(tokenJwt)}`;

  fetch(url)
    .then(res => {
      if (!res.ok) throw new Error(`HTTP error ${res.status}`);
      return res.json();
    })
    .then(data => {
      if (!data.pvs || !Array.isArray(data.pvs)) {
        console.error("❌ data.pvs no es un array o está vacío", data.pvs);
        return;
      }

      localStorage.setItem("informesOTRaw", JSON.stringify(data));
      console.log("🟢 Datos crudos guardados:", data);

      const fechas = new Set();
      data.pvs.forEach(pv => {
        (pv.lineas || []).forEach(linea => {
          if (linea.fechaEntrada) fechas.add(linea.fechaEntrada);
        });
      });

      const fechasOrdenadas = [...fechas].sort();
      const container = document.getElementById("fechas-checkboxes");
      const checkTodas = document.getElementById("check-todas");

      fechasOrdenadas.forEach(fecha => {
        const label = document.createElement("label");
        label.innerHTML = `
          <input type="checkbox" class="check-fecha" value="${fecha}" />
          ${fecha}
        `;
        container.appendChild(label);
        container.appendChild(document.createElement("br"));
      });

      const actualizarInformes = () => {
        const seleccionadas = Array.from(document.querySelectorAll(".check-fecha:checked"))
          .map(el => el.value);
        const dataFiltrada = filtrarDataPorFechas(data, seleccionadas);
        renderInformes(dataFiltrada);
      };

      checkTodas.addEventListener("change", () => {
        const todos = document.querySelectorAll(".check-fecha");
        todos.forEach(cb => cb.checked = checkTodas.checked);
        actualizarInformes();
      });

      container.addEventListener("change", (e) => {
        if (e.target.classList.contains("check-fecha")) {
          const todas = document.querySelectorAll(".check-fecha");
          const seleccionadas = document.querySelectorAll(".check-fecha:checked");
          checkTodas.checked = todas.length === seleccionadas.length;
          actualizarInformes();
        }
      });
    })
    .catch(err => {
      console.error("❌ Error al cargar los datos:", err);
    });
});
