//pdfMake
//anchoTotal = 520
//anchoUtil = 480 (he puesto márgenes de 20 en cada lado)
// pageMargins: [left, top, right, bottom]
document.addEventListener("DOMContentLoaded", () => {
    loaderOff();

    const fechasEntrada = data.ot.fechasEntrada;
    //console.log(fechasEntrada);

    document.getElementById('tituloPrincipal').innerHTML = data.ot.codOt + ' - ' + 'INFORME VISUALES - MONTAJES';
    document.getElementById('tituloSecundario').innerHTML = data.ot.nombreOt;

    const fechasObjeto = {};
    const contenedor = document.getElementById("fechas-container");
    const btnPdf = document.getElementById("btn-pdf");
    const checkTodas = document.getElementById("check-todas");

    checkTodas.checked = false; // ← asegúrate que arranca desmarcado
    btnPdf.disabled = true;

    function formatearFecha(fechaISO) {
        const [a, m, d] = fechaISO.split("-");
        return `${d}/${m}/${a}`;
    }

    // Crear checkboxes individuales
    fechasEntrada.forEach(fecha => {
        const label = document.createElement("label");
        label.className = "flex items-center space-x-2";

        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.value = fecha;
        checkbox.className = "form-checkbox text-red-600";
        checkbox.addEventListener("change", () => {
            if (checkbox.checked) {
                fechasObjeto[fecha] = true;
            } else {
                delete fechasObjeto[fecha];
                checkTodas.checked = false;
            }
            actualizarBoton();
        });

        const span = document.createElement("span");
        span.textContent = formatearFecha(fecha);
        span.className = "text-gray-700";

        label.appendChild(checkbox);
        label.appendChild(span);
        contenedor.appendChild(label);
    });

    checkTodas.addEventListener("change", () => {
        const checkboxes = contenedor.querySelectorAll("input[type=checkbox]");
        if (checkTodas.checked) {
            fechasEntrada.forEach(fecha => fechasObjeto[fecha] = true);
            checkboxes.forEach(cb => cb.checked = true);
        } else {
            fechasEntrada.forEach(fecha => delete fechasObjeto[fecha]);
            checkboxes.forEach(cb => cb.checked = false);
        }
        actualizarBoton();
    });

    function actualizarBoton() {
        const activo = Object.keys(fechasObjeto).length > 0;
        activo ? btnPdf.classList.replace('bg-gray-600', 'bg-red-600') : btnPdf.classList.replace('bg-red-600', 'bg-gray-600');
        btnPdf.disabled = !activo;
    }

    //GENERACIÓN DEL PDF
    btnPdf.addEventListener("click", () => {
        loaderOn();
        const fechasFiltradas = Object.keys(fechasObjeto);
        //console.log("Fechas seleccionadas:", fechasFiltradas);

        const datos = {
            ot: data.ot,
            pvs: []
        };

        for (const pv of data.pvs) {
            const nuevoPv = {
                area: pv.area,
                direccion: pv.direccion,
                id: pv.id,
                nombre: pv.nombre,
                telefono: pv.telefono,
                zona: pv.zona,
                lineas: []
            };

            for (const linea of pv.lineas) {
                for (const fechaFiltrada of fechasFiltradas) {
                    if (linea.fechaEntrada == fechaFiltrada) {
                        nuevoPv.lineas.push(linea);
                    }
                }
            }

            if (nuevoPv.lineas.length > 0) {
                datos.pvs.push(nuevoPv);
            }
        }

        generarPdf(datos);
    });

    actualizarBoton();
});

async function generarPdf(datos) {

    const logoBase64 = await getImageBase64('https://dosxdos.app.iidos.com/img/logo_black.png');
    const contenido = [];

    datos.pvs.forEach((dato, index) => {
        const encabezado = {
            stack: [
                {
                    table: {
                        widths: [120, '*'],
                        body: [[
                            {
                                image: logoBase64,
                                width: 120,
                                height: 50,
                                margin: [0, 0, 0, 0]
                            },
                            {
                                text: datos.ot.codOt + ' - INFORME DE MONTAJE',
                                style: 'titulo',
                                alignment: 'right',
                                margin: [0, 15, 0, 0]  // ajusta para centrar vertical con el logo
                            }
                        ]]
                    },
                    layout: 'noBorders',
                    margin: [0, 0, 0, 5]
                },
                {
                    alignment: 'center',
                    table: {
                        widths: ['auto', 10, '*'],
                        body: [
                            [
                                { text: 'Punto de Venta:', bold: true, alignment: 'right' },
                                '',
                                { text: dato.nombre, alignment: 'left' }
                            ],
                            [
                                { text: 'Teléfono:', bold: true, alignment: 'right' },
                                '',
                                { text: dato.telefono, alignment: 'left' }
                            ],
                            [
                                { text: 'Dirección:', bold: true, alignment: 'right' },
                                '',
                                { text: dato.direccion, alignment: 'left' }
                            ],
                            [
                                { text: 'Área:', bold: true, alignment: 'right' },
                                '',
                                { text: dato.area, alignment: 'left' }
                            ],
                            [
                                { text: 'Zona:', bold: true, alignment: 'right' },
                                '',
                                { text: dato.zona, alignment: 'left' }
                            ],
                            [
                                { text: 'Nombre OT:', bold: true, alignment: 'right' },
                                '',
                                { text: datos.ot.nombreOt, alignment: 'left' }
                            ],
                            [
                                { text: 'Cliente:', bold: true, alignment: 'right' },
                                '',
                                { text: datos.ot.cliente, alignment: 'left' }
                            ]
                        ]
                    },
                    layout: 'noBorders',
                    margin: [0, 15, 0, 15],
                    fontSize: 12
                }
            ]
        };

        const tabla = {
            width: 520, // fuerza un contenedor de ancho fijo
            alignment: 'center', // centra el contenedor
            table: {
                headerRows: 1,
                widths: calcularAnchosProporcionales([1.3, 2.3, 2, 2, 3, 3, 1.5, 1.5]),
                body: [
                    [
                        { text: 'Línea', bold: true },
                        { text: 'Ubicación', bold: true },
                        { text: 'Tipo', bold: true },
                        { text: 'Firma', bold: true },
                        { text: 'Quitar', bold: true },
                        { text: 'Poner', bold: true },
                        { text: 'Ancho', bold: true },
                        { text: 'Alto', bold: true }
                    ],
                    ...dato.lineas.map(linea => [
                        { text: linea.codigo, noWrap: false },
                        { text: linea.ubicacion, noWrap: false },
                        linea.material ? { text: linea.material + ' - ' + linea.tipo, noWrap: false } : { text: linea.tipo, noWrap: false },
                        { text: datos.ot.firma, noWrap: false },
                        { text: linea.quitar, noWrap: false },
                        { text: linea.poner, noWrap: false },
                        linea.ancho ? { text: linea.ancho, noWrap: false } : '',
                        linea.alto ? { text: linea.alto, noWrap: false } : '',
                    ])
                ]
            },
            layout: {
                hLineWidth: () => 0.8,
                vLineWidth: () => 0.8,
                hLineColor: () => '#aaa',
                vLineColor: () => '#aaa',
                paddingLeft: () => 5,
                paddingRight: () => 5,
                paddingTop: () => 4,
                paddingBottom: () => 4
            },
            margin: [0, 20, 0, 20]
        };

        const firma = {
            width: 555,
            alignment: 'center',
            unbreakable: true,
            columns: [
                {
                    width: '*',
                    table: {
                        widths: ['*'],
                        body: [[
                            {
                                stack: [
                                    { text: 'Sello del Cliente:', bold: true },
                                    { text: '\n\n\n\n\n\n\n' }
                                ],
                                margin: [5, 5, 5, 5],
                                fontSize: 11
                            }
                        ]]
                    },
                    layout: {
                        hLineWidth: () => 1,
                        vLineWidth: () => 1,
                        hLineColor: () => '#aaa',
                        vLineColor: () => '#aaa'
                    }
                },
                {
                    width: '*',
                    table: {
                        widths: ['*'],
                        body: [[
                            {
                                stack: [
                                    { text: 'Firma del Instalador:', bold: true },
                                    { text: '\n\n\n\n\n\n\n' }
                                ],
                                margin: [5, 5, 5, 5],
                                fontSize: 11
                            }
                        ]]
                    },
                    layout: {
                        hLineWidth: () => 1,
                        vLineWidth: () => 1,
                        hLineColor: () => '#aaa',
                        vLineColor: () => '#aaa'
                    }
                },
                {
                    width: '*',
                    table: {
                        widths: ['*'],
                        body: [[
                            {
                                stack: [
                                    { text: 'Fecha:', bold: true },
                                    { text: '\n\n\n\n\n\n\n' }
                                ],
                                margin: [5, 5, 5, 5],
                                fontSize: 11
                            }
                        ]]
                    },
                    layout: {
                        hLineWidth: () => 1,
                        vLineWidth: () => 1,
                        hLineColor: () => '#aaa',
                        vLineColor: () => '#aaa'
                    }
                }
            ],
            columnGap: 20,
            margin: [0, 30, 0, 0]
        };

        const stackCompleto = {
            stack: [encabezado, tabla, firma]
        };

        // Solo añadir salto de página si NO es el último punto de venta
        if (index < datos.pvs.length - 1) {
            stackCompleto.pageBreak = 'after';
        }

        contenido.push(stackCompleto);
    });

    const docDefinition = {
        pageSize: 'A4',
        pageMargins: [20, 30, 20, 20],
        content: contenido,
        styles: {
            titulo: { fontSize: 14, bold: true },
            datos: { fontSize: 12, margin: [0, 2, 0, 0] }
        },
        defaultStyle: { fontSize: 10 },
        footer: (currentPage, pageCount) => ({
            text: `Página ${currentPage} de ${pageCount}`,
            alignment: 'right',
            margin: [0, 0, 40, 20],
            fontSize: 11
        })
    };

    pdfMake.createPdf(docDefinition).getBlob(blob => {
        const url = URL.createObjectURL(blob);
        document.getElementById('loader-text').textContent = 'PDF Generado';
        document.getElementById('loader-dots').innerHTML = '<span style="font-size: 36px; color: green;">✔</span>';
        const a = document.createElement('a');
        a.href = url;
        a.download = `${datos.ot.codOt}.pdf`;
        a.click();
    });

    loaderOff();
}

function getImageBase64(url) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = function () {
            const canvas = document.createElement('canvas');
            canvas.width = img.width;
            canvas.height = img.height;
            canvas.getContext('2d').drawImage(img, 0, 0);
            resolve(canvas.toDataURL('image/png'));
        };
        img.onerror = reject;
        img.src = url;
    });
}

function calcularAnchosProporcionales(pesos, anchoDisponible = 480) {
    sumaPesos = 0;
    proporcion = 0;
    pesos.map(peso => {
        sumaPesos += peso;
    });
    //console.log('sumaPesos: ' + sumaPesos);
    proporcion = anchoDisponible / sumaPesos;
    //console.log('proporcion: ' + proporcion);
    anchosProporcionales = [];
    pesos.map(peso => {
        anchosProporcionales.push(peso * proporcion);
    });
    //console.log('anchosProporcionales' + anchosProporcionales);
    return anchosProporcionales;
}