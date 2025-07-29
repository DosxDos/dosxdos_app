// Función para truncar texto si excede el número máximo de caracteres elegidos
function truncarTexto(texto, maxCaracteres) {
  if (!texto) return '-';
  return texto.length > maxCaracteres ? texto.slice(0, maxCaracteres - 1) + "…" : texto;
}

// Función para renderizar los informes filtrados en el HTML
function renderInformes(data) {
  const container = document.getElementById("contenedor-informes");
  if (!container) return;

  console.log("🔍 Renderizando informes de producción", data);
  container.innerHTML = ""; // Limpiamos el contenido anterior

  data.forEach(info => {
    const detalles = info.detalles;
    const lotes = [];
    let index = 0;

    if (detalles.length <= 11) {
      lotes.push(detalles);
    } else {
      lotes.push(detalles.slice(0, 11));
      index = 11;
      while (index < detalles.length) {
        lotes.push(detalles.slice(index, index + 15));
        index += 15;
      }
    }

    lotes.forEach((lote, index) => {
      const div = document.createElement("div");
      div.className = "informe";

      const contenido = document.createElement("div");
      contenido.className = "contenido-informe";

      let cabeceraHTML = "";
      if (index === 0) {
        cabeceraHTML = `
          <div class="cabecera">
            <div class="logo">
              <img src="elementos_diseno/LOGO.png" alt="Logo Empresa">
            </div>
            <div class="titulo">
              <h1>Informe de Producción</h1>
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

      contenido.innerHTML = cabeceraHTML + tablaHTML;
      div.appendChild(contenido);

      // Footer eliminado

      container.appendChild(div);
    });
  });

  const botonPDF = document.getElementById("btnGenerarPDF");
  if (botonPDF) {
    botonPDF.disabled = false;
    botonPDF.style.display = "inline-block";
  }

  console.log("✅ Informes renderizados");
}

// Función para generar el PDF del contenido HTML
async function generarPDF() {
  const { PDFDocument } = PDFLib;
  const informes = Array.from(document.querySelectorAll("#contenedor-informes .informe"));
  if (informes.length === 0) {
    console.warn("No hay informes para generar PDF");
    return;
  }

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
        .outputPdf('blob');

      const pdfBytes = await pdfBlob.arrayBuffer();
      const pdfDoc = await PDFDocument.load(pdfBytes);
      const paginas = await pdfLote.copyPages(pdfDoc, pdfDoc.getPageIndices());
      paginas.forEach(page => pdfLote.addPage(page));
    }

    return pdfLote;
  }

  try {
    const pdfFinal = await PDFDocument.create();
    const batchSize = 20;

    for (let i = 0; i < informes.length; i += batchSize) {
      const lote = informes.slice(i, i + batchSize);
      console.log(`📦 Procesando lote ${Math.floor(i / batchSize) + 1}`);

      const pdfLote = await procesarLote(lote);
      const pdfLoteBytes = await pdfLote.save();
      const pdfLoteDoc = await PDFDocument.load(pdfLoteBytes);
      const paginas = await pdfFinal.copyPages(pdfLoteDoc, pdfLoteDoc.getPageIndices());
      paginas.forEach(page => pdfFinal.addPage(page));
    }

    const pdfFinalBytes = await pdfFinal.save();
    const blobFinal = new Blob([pdfFinalBytes], { type: "application/pdf" });
    const url = URL.createObjectURL(blobFinal);

    const a = document.createElement('a');
    a.href = url;
    a.download = "informe_ot_produccion_completo.pdf";
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);

    console.log("✅ PDF descargado");
  } catch (error) {
    console.error("❌ Error al generar PDF:", error);
  }
}

window.generarPDF = generarPDF;

// Lógica de carga y render sin filtros
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
    console.warn("⚠️ Faltan parámetros en la URL");
    return;
  }

  const url = `apirest/informe_ot_visuales_produccion.php?idOt=${idOt}&codOt=${encodeURIComponent(codOt)}&tipoOt=${encodeURIComponent(tipoOt)}&cliente=${encodeURIComponent(cliente)}&tokenJwt=${encodeURIComponent(tokenJwt)}`;

  fetch(url)
    .then(res => {
      if (!res.ok) throw new Error(`HTTP error ${res.status}`);
      return res.json();
    })
    .then(data => {
      if (!data.pvs || !Array.isArray(data.pvs)) {
        console.error("❌ data.pvs no es un array", data.pvs);
        return;
      }

      localStorage.setItem("informesOTRaw", JSON.stringify(data));
      console.log("🟢 Datos cargados");

      const informesListos = data.pvs.map(pv => ({
        ot: data.ot.codOt,
        cliente: data.ot.cliente,
        nombreEmpresa: data.ot.cliente,
        puntoVenta: pv.nombre,
        direccion: pv.direccion,
        telefono: pv.telefono,
        area: pv.area,
        zona: pv.zona,
        nombreOt: pv.nombreOt,
        detalles: (pv.lineas || []).map(linea => ({
          tipo: linea.tipo || "",
          firma: data.ot.firma || "",
          quitar: linea.quitar || "",
          poner: linea.poner || "",
          dimensiones: `${linea.ancho || '-'} x ${linea.alto || '-'}`,
          linea: linea.linea || "",
          ubicacion: linea.ubicacion || ""
        }))
      }));

      renderInformes(informesListos);
    })
    .catch(err => console.error("❌ Error cargando datos:", err));
});
