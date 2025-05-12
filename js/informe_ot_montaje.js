document.addEventListener("DOMContentLoaded", () => {
  const informes = [
    {
      ot: "V-17725 L'OREAL ESPAÑA, S.A.",
      puntoVenta: "Arkay Luis Morote 928 27.04.19",
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
  ];

  renderInformes(informes);
});

function renderInformes(data) {
  const container = document.getElementById("contenedor-informes");
  if (!container) return;

  data.forEach(info => {
    const div = document.createElement("div");
    div.className = "informe";

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

    container.appendChild(div);
  });
}

function generarPDF() {
  const element = document.getElementById("contenedor-informes");

  html2pdf()
    .set({
      margin: 10,
      filename: "informe_ot_montajes.pdf",
      image: { type: "jpeg", quality: 0.98 },
      html2canvas: { scale: 2 },
      jsPDF: { unit: "mm", format: "a4", orientation: "portrait" }
    })
    .from(element)
    .save();
}

window.generarPDF = generarPDF;
