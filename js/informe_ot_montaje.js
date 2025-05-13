// ✅ Función para pintar los informes en el HTML
function renderInformes(data) {
  const container = document.getElementById("contenedor-informes"); // Buscamos el contenedor
  if (!container) return;

  container.innerHTML = ""; // Limpiamos el contenido anterior

  data.forEach(info => {
    const div = document.createElement("div");
    div.className = "informe"; // Creamos un bloque por informe

    // Construimos el HTML dinámico con los datos de cada informe
    div.innerHTML = ` 
      <div class="cabecera">
        <div class="logo">
          <img src="elementos_diseno/DOS POR DOS LOGO.png" alt="Logo Empresa">
        </div>
        <div class="titulo">
          <h1>Informe de Montaje</h1>
        </div>
      </div>

      <div class="info-header">
        <p><strong>OT:</strong> ${info.ot}</p>
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

      <div class="firmas">
        <p><strong>Sello del Cliente:</strong> <span></span></p>
        <p><strong>Firma del Instalador:</strong> <span></span></p>
        <p><strong>Fecha:</strong> <span></span></p>
      </div>
    `;

    container.appendChild(div); // Insertamos el informe en el DOM
  });

  // ✅ Activamos el botón para generar PDF una vez renderizado
  const botonPDF = document.getElementById("btnGenerarPDF");
  if (botonPDF) {
    botonPDF.disabled = false;
    botonPDF.style.display = "inline-block";
  }
}

// ✅ Función para generar el PDF del contenido HTML
function generarPDF() {
  console.log("🟡 Botón presionado - iniciando generación PDF");
  const element = document.getElementById("contenedor-informes"); // Seleccionamos el contenedor

  if (!element) {
    console.error("❌ No se encontró el contenedor de informes");
    return;
  }

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
}
window.generarPDF = generarPDF; // Registramos la función globalmente

// ✅ Al cargar la página, hacemos fetch al PHP y guardamos en localStorage
document.addEventListener("DOMContentLoaded", () => {
  const botonPDF = document.getElementById("btnGenerarPDF");
  if (botonPDF) {
    botonPDF.disabled = true;          // 🔒 Desactivamos al inicio
    botonPDF.style.display = "none";   // Ocultamos hasta que se carguen los datos
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
      localStorage.setItem("informesOT", JSON.stringify(data)); // Guardamos para uso futuro
      console.log("🟢 Datos guardados en localStorage:", data);
      renderInformes(data); // Pintamos en pantalla
    })
    .catch(err => {
      console.error("❌ Error al cargar los datos:", err);
    });
});
