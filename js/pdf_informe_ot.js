document.addEventListener("DOMContentLoaded", async () => {
    if (!errorApp) {

        document.title = data.ot.codOt + ' - ' + 'INFORME';
        document.getElementById('loader-text').textContent = 'Generando PDF';

        try {
            const areasKeys = Object.keys(data.lineas);
            const areasString = areasKeys.join(', ');
            const logoBase64 = await getImageBase64('https://dosxdos.app.iidos.com/img/logo_black.png');
            const contenido = [];
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
                                    text: data.ot.codOt + ' - INFORME',
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
                                    { text: 'Nombre:', bold: true, alignment: 'right' },
                                    '',
                                    { text: data.ot.nombreOt, alignment: 'left' }
                                ],
                                [
                                    { text: 'Tipo:', bold: true, alignment: 'right' },
                                    '',
                                    { text: data.ot.tipo, alignment: 'left' }
                                ],
                                [
                                    { text: 'Subtipo:', bold: true, alignment: 'right' },
                                    '',
                                    { text: data.ot.subtipo, alignment: 'left' }
                                ],
                                [
                                    { text: 'Cliente:', bold: true, alignment: 'right' },
                                    '',
                                    { text: data.ot.cliente, alignment: 'left' }
                                ],
                                [
                                    { text: 'Firma:', bold: true, alignment: 'right' },
                                    '',
                                    { text: data.ot.firma, alignment: 'left' }
                                ],
                                [
                                    { text: 'Contacto:', bold: true, alignment: 'right' },
                                    '',
                                    { text: data.ot.contacto, alignment: 'left' }
                                ],
                                [
                                    { text: 'Creada:', bold: true, alignment: 'right' },
                                    '',
                                    { text: data.ot.creacion, alignment: 'left' }
                                ],
                                [
                                    { text: 'Creada por:', bold: true, alignment: 'right' },
                                    '',
                                    { text: data.ot.creadaPor, alignment: 'left' }
                                ],
                                [
                                    { text: 'Áreas:', bold: true, alignment: 'right' },
                                    '',
                                    { text: areasString, alignment: 'left' }
                                ],
                            ]
                        },
                        layout: 'noBorders',
                        margin: [0, 15, 0, 15],
                        fontSize: 10
                    }
                ]
            };

            const informesAreas = {};
            informesAreas.stack = [];

            Object.entries(data.lineas).map(([areaString, area]) => {
                informesAreas.stack.push(
                    {
                        text: areaString,
                        bold: true,
                        margin: [0, 15, 0, 5],
                        fontSize: 14
                    },
                )
                Object.entries(area).map(([sector, lineas]) => {
                    informesAreas.stack.push(
                        {
                            text: sector,
                            bold: true,
                            margin: [0, 10, 0, 0]
                        },
                        {
                            width: 520, // fuerza un contenedor de ancho fijo
                            alignment: 'center', // centra el contenedor
                            table: {
                                headerRows: 1,
                                widths: calcularAnchosProporcionales([2.5, 3.5, 5.5, 7, 4, 8]),
                                body: [
                                    [
                                        { text: 'Línea', bold: true },
                                        { text: 'Previsión', bold: true },
                                        { text: 'Punto de venta', bold: true },
                                        { text: 'Dirección', bold: true },
                                        { text: 'Zona', bold: true },
                                        { text: 'Observaciones', bold: true }
                                    ],
                                    ...lineas.map(linea => [
                                        { text: linea.codigo, noWrap: false },
                                        { text: linea.prevision, noWrap: false },
                                        { text: linea.pv, noWrap: false },
                                        { text: linea.direccion, noWrap: false },
                                        { text: linea.zona, noWrap: false },
                                        linea.observaciones ? { text: linea.observaciones, noWrap: false } : { text: '', noWrap: false },
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
                            margin: [0, 10, 0, 20]
                        }
                    );
                })
            });

            contenido.push(encabezado);
            contenido.push(informesAreas);
            console.log(contenido);

            const docDefinition = {
                pageSize: 'A4',
                pageMargins: [30, 40, 30, 25],
                content: contenido,
                styles: {
                    titulo: { fontSize: 13, bold: true },
                    datos: { fontSize: 11, margin: [0, 2, 0, 0] }
                },
                defaultStyle: { fontSize: 10 },
                footer: (currentPage, pageCount) => ({
                    text: `Página ${currentPage} de ${pageCount}`,
                    alignment: 'right',
                    margin: [0, 0, 40, 20],
                    fontSize: 10
                })
            };

            pdfMake.createPdf(docDefinition).getBlob(blob => {
                const url = URL.createObjectURL(blob);
                document.getElementById('loader-text').textContent = 'PDF Generado';
                document.getElementById('loader-dots').innerHTML = '<span style="font-size: 36px; color: green;">✔</span>';
                setTimeout(() => {
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `${data.ot.codOt}.pdf`;
                    a.click();
                }, 3000);
                // const a2 = document.createElement('a');
                // a2.href = url;
                // a2.target = '_blank';
                // a2.rel = 'noopener';
                // a2.click();
            });


        } catch (error) {
            document.getElementById('loader-text').textContent = 'Error! Al generar el PDF: ' + error.message;
            document.getElementById('loader-dots').innerHTML = '<span class="error">✘</span>';
        }

    }

});

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

function calcularAnchosProporcionales(pesos, anchoDisponible = 468) {
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

function formatearFecha(fechaISO) {
    const [a, m, d] = fechaISO.split("-");
    return `${d}/${m}/${a}`;
}
