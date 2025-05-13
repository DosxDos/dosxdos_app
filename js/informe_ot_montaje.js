// Función para pintar los informes en el HTML
function renderInformes(data) {
  const container = document.getElementById("contenedor-informes"); // Buscamos el contenedor en el HTML
  if (!container) return; // Si no existe el contenedor, no hacemos nada

  container.innerHTML = ""; // Limpiamos el contenido anterior para evitar duplicaciones

  data.forEach(info => {
    const div = document.createElement("div");
    div.className = "informe"; // Creamos un div para cada informe

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
    `; // Construimos el HTML dinámico con los datos

    container.appendChild(div); // Agregamos el div al contenedor
  });

  // Activamos el botón para generar PDF una vez que todo está renderizado
  const botonPDF = document.getElementById("btnGenerarPDF");
  if (botonPDF) {
    botonPDF.disabled = false;
    botonPDF.style.display = "inline-block"; // Mostramos el botón una vez que los informes están listos
  }
}

// Función para generar el PDF del contenedor
function generarPDF() {
  console.log("🟡 Botón presionado - iniciando generación PDF");
  const element = document.getElementById("contenedor-informes"); // Seleccionamos el contenedor que queremos convertir a PDF

  if (!element) {
    console.error("❌ No se encontró el contenedor de informes");
    return;
  }

  html2pdf()
    .set({
      margin: 10, // Margen del PDF en milímetros
      filename: "informe_ot_montajes.pdf", // Nombre del archivo PDF
      image: { type: "jpeg", quality: 0.98 }, // Tipo de imagen y calidad al renderizar
      html2canvas: { scale: 2, useCORS: true }, // Escala de la imagen al renderizar, el cors no hace nada en este caso pero lo dejo
      jsPDF: { unit: "mm", format: "a4", orientation: "portrait" } // Formato y orientación del PDF
    })
    .from(element)
    .save() // Guardamos el PDF
    .then(() => {
      console.log("🟢 PDF generado y descargado");
    })
    .catch((error) => {
      console.error("❌ Error al generar el PDF:", error);
    });
}
window.generarPDF = generarPDF; // Hacemos la función accesible desde el HTML para que esté disponible al hacer clic en el botón

// Esperamos a que el DOM esté completamente cargado para ejecutar el script
document.addEventListener("DOMContentLoaded", () => {
  const botonPDF = document.getElementById("btnGenerarPDF");
  if (botonPDF) {
    botonPDF.disabled = true; // Desactivamos el botón hasta que esté listo, doble capa de seguridad
  }

  const datosLocal = localStorage.getItem("informesOT"); // Buscamos en localStorage si ya hay datos guardados

  if (datosLocal) {
    console.log("🟡 Cargando datos desde localStorage");
    const datos = JSON.parse(datosLocal); // Convertimos a objeto JS
    renderInformes(datos); // Renderizamos los datos
  } else {
    console.log("🟡 Cargando datos desde PHP");
    fetch('informe_ot_montajes.php') // Si no hay datos en el localStorage, llamamos al backend
      .then(response => response.json()) // Convertimos la respuesta en JSON
      .then(data => {
        localStorage.setItem('informesOT', JSON.stringify(data)); // Guardamos en localStorage
        console.log('🟢 Datos guardados en localStorage:', data);
        renderInformes(data); // Imprimimos los datos en pantalla
      })
      .catch(error => {
        console.error('❌ Error al cargar datos:', error);
      });
  }
});
