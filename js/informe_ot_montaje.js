document.addEventListener("DOMContentLoaded", () => {
  const informes = [
    {
      ot: "V-17725 L'OREAL ESPAÑA, S.A.",
      puntoVenta: "Arkay Luis Morote 928 270 419",
      direccion: "C. Luis Morote, 17, 35007 Las Palmas de Gran Canaria",
      detalles: [
        {
          tipo: "EST PROD",
          firma: "580156 BACKLIGHT LANCOME",
          quitar: "10.2-TRIPLE SERUM OJOS-24 (ESP)",
          poner: "RETINOL + HPN-25 (ESP)",
          dimensiones: "45 x 45"
        }
      ]
      
    }
  ]; //Datos mockeados

  renderInformes(informes); //Convertimos los datos mockeados a HTML
});

function renderInformes(data) {
  const container = document.getElementById("contenedor-informes"); //Buscamos el contenedor en el HTML
  if (!container) return; //Si no existe el contenedor, no hacemos nada

  data.forEach(info => {
    const div = document.createElement("div");
    div.className = "informe"; //Creamos un div para cada informe

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
    `;/* Construimos el HTML dinámnico con los datos */

    container.appendChild(div); //agregamos el div al contenedor
  });
}

function generarPDF() {
    console.log("🟡 Botón presionado - iniciando generación PDF");
  const element = document.getElementById("contenedor-informes"); //Seleccionamos el contenedor que queremos convertir a PDF
  if (!element) {console.error("❌ No se encontró el contenedor de informes");return;} //Si no existe el contenedor, no hacemos nada

  html2pdf()
    .set({
      margin: 10, // Margen del PDF en milímetros
      filename: "informe_ot_montajes.pdf", // Nombre del archivo PDF
      image: { type: "jpeg", quality: 0.98 }, // Tipo de imagen y calidad al renderizar
      html2canvas: { scale: 2,
        useCORS: true
       }, // Escala de la imagen al renderizar
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
