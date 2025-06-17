// Función para truncar texto si excede el número máximo de caracteres elegidos luego en la tabla
function truncarTexto(texto, maxCaracteres) {
  if (!texto) return '-';
  return texto.length > maxCaracteres ? texto.slice(0, maxCaracteres - 1) + "…" : texto;
}
// Función para renderizar los informes filtrados en el HTML
function renderInformes(data) {
  const container = document.getElementById("contenedor-informes");
  if (!container) return;

  container.innerHTML = ""; // Limpiamos el contenido anterior

  data.forEach(info => {
    const detalles = info.detalles;
    const lotes = [];
    let index = 0;

    // Dividimos los detalles en lotes para paginación
    if (detalles.length <= 11) {
      lotes.push(detalles); // Todo entra en una sola página
    } else {
      lotes.push(detalles.slice(0, 11)); // Primera página con 11 líneas
      index = 11; // Iniciamos el índice en 11 para las siguientes páginas
      while (index < detalles.length) {
        lotes.push(detalles.slice(index, index + 15)); // Páginas siguientes con 15 líneas
        index += 15; // Avanzamos 15 líneas para la siguiente página
      }
    }
    //Generador de informes, un bloque por cada lote 
    lotes.forEach((lote, index) => {
      const div = document.createElement("div");
      div.className = "informe";

      const contenido = document.createElement("div");
      contenido.className = "contenido-informe";

      // Cabecera solo en la primera página
      let cabeceraHTML = "";
      if (index === 0) {
        cabeceraHTML = `
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
        `;
      }

      const tablaHTML = `
        <table>
          <thead>
            <tr>
              <th>Línea</th>
              <th>Ubicación</th>
              <th>Tipo</th>
              <th>Firma</th>
              <th>Quitar</th>
              <th>Poner</th>
              <th>Ancho x Alto</th>
            </tr>
          </thead>
          <tbody>
            ${lote.map(item =>
        `<tr>
    <td>${truncarTexto(item.linea, 20)}</td>
    <td>${truncarTexto(item.ubicacion, 30)}</td>
    <td>${truncarTexto(item.tipo, 41)}</td>
    <td>${truncarTexto(item.firma, 30)}</td>
    <td>${truncarTexto(item.quitar, 30)}</td>
    <td>${truncarTexto(item.poner, 30)}</td>
    <td>${item.dimensiones}</td>
  </tr>`
      ).join('')}

          </tbody>
        </table>
      `;

      contenido.innerHTML = cabeceraHTML + tablaHTML; //55-80
      div.appendChild(contenido);

      // Footer solo en el último bloque
      if (index === lotes.length - 1) {
        const footer = document.createElement("div");
        footer.className = "firmas-recuadro";
        footer.style = "page-break-inside: avoid; page-break-before: auto;";
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
        div.appendChild(footer);
      }

      container.appendChild(div);
    });
  });

  const botonPDF = document.getElementById("btnGenerarPDF");
  if (botonPDF) {
    botonPDF.disabled = false;
    botonPDF.style.display = "inline-block";
  }

  console.log("🔍 Informes renderizados:", data);
}




// Función para generar el PDF del contenido HTML
async function generarPDF() {
  const { PDFDocument } = PDFLib;
  const informes = Array.from(document.querySelectorAll("#contenedor-informes .informe"));
  if (informes.length === 0) {
    console.warn("No hay informes para generar PDF");
    return;
  }

  // Función para procesar un lote (batch) de informes y devolver un PDFDocument con sus páginas
  async function procesarLote(loteInformes) {
    const pdfLote = await PDFDocument.create();

    for (const informe of loteInformes) {
      const pdfBlob = await html2pdf()
        .set({
          margin: [10, 10, 0, 10],
          image: { type: "jpeg", quality: 0.98 },
          html2canvas: { scale: 2, useCORS: true, dpi: 48, letterRendering: true },
          jsPDF: { unit: "mm", format: "a4", orientation: "portrait" },
          pagebreak: { mode: ['css', 'legacy'] }
        })
        .from(informe)
        .outputPdf('blob'); //lo devolvemos como un Blob (Binary Large Object)

      const pdfBytes = await pdfBlob.arrayBuffer(); // Convertimos el Blob a ArrayBuffer para PDFLib (necesario para cargarlo)
      const pdfDoc = await PDFDocument.load(pdfBytes); // Cargamos el PDF generado por html2pdf

      const paginas = await pdfLote.copyPages(pdfDoc, pdfDoc.getPageIndices()); // Copiamos las páginas del PDF generado al nuevo PDF de lote
      paginas.forEach(page => pdfLote.addPage(page)); // Añadimos las páginas copiadas al PDF del lote
    }
    return pdfLote; // Devolvemos el PDF del lote con todas las páginas procesadas
  }

  try {
    const pdfFinal = await PDFDocument.create();

    // Procesar lotes de la cantidad de informes que quiera manejar a la vez
    const batchSize = 20;
    for (let i = 0; i < informes.length; i += batchSize) {
      const lote = informes.slice(i, i + batchSize);
      console.log(`Procesando lote ${Math.floor(i / batchSize) + 1} de ${Math.ceil(informes.length / batchSize)}`);

      const pdfLote = await procesarLote(lote); // Generamos PDF con los informes de este Lote

      // Copiar páginas del lote al PDF final
      const pdfLoteBytes = await pdfLote.save(); // Guardamos el PDF del lote como ArrayBuffer (bytes)
      const pdfLoteDoc = await PDFDocument.load(pdfLoteBytes); // Cargamos el PDF del lote en un PDFDocument
      const paginas = await pdfFinal.copyPages(pdfLoteDoc, pdfLoteDoc.getPageIndices()); // Copiamos las páginas del PDF del lote al PDF final
      paginas.forEach(page => pdfFinal.addPage(page)); // Añadimos las páginas copiadas al PDF final
    }

    const pdfFinalBytes = await pdfFinal.save(); // Guardamos el PDF final como ArrayBuffer (bytes)
    const blobFinal = new Blob([pdfFinalBytes], { type: "application/pdf" });   // Creamos un Blob con los bytes para permitir la descarga
    const url = URL.createObjectURL(blobFinal); // Generamos URL temporal para el Blob

    const a = document.createElement('a'); // Creamos un enlace temporal para descargar el PDF
    a.href = url;
    a.download = "informe_ot_montajes_completo.pdf"; // Configuramos nombre predeterminado para la descarga
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url); // Con esto al hacer click se descarga el PDF y eliminamos la URL temporal y liberamos memoria

    console.log("🟢 PDF combinado generado y descargado"); // Notificamos que salió bien
  } catch (error) {
    console.error("❌ Error al generar PDF combinado:", error); // Notificamos si salió mal
  }
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
      nombreEmpresa: ot.cliente,
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
        dimensiones: `${linea.ancho || '-'} x ${linea.alto || '-'}`,
        linea: linea.linea || "",
        ubicacion: linea.ubicacion || ""
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

  const url = `apirest/informe_ot_visuales_montajes.php?idOt=${idOt}&codOt=${encodeURIComponent(codOt)}&tipoOt=${encodeURIComponent(tipoOt)}&cliente=${encodeURIComponent(cliente)}&tokenJwt=${encodeURIComponent(tokenJwt)}`;

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

      localStorage.setItem("informesOTRaw", JSON.stringify(data)); // Guardamos los datos en el LocalStorage
      console.log("🟢 Datos crudos guardados:", data);

      const fechas = new Set();
      data.pvs.forEach(pv => {
        (pv.lineas || []).forEach(linea => {
          if (linea.fechaEntrada) fechas.add(linea.fechaEntrada);
        });
      });

      // Convertimos el Set a un Array y lo ordenamos
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
