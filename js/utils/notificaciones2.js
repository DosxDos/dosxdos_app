function fetchNotificaciones() {
  return new Promise(async (resolve, reject) => {
    try {
      const url =
        "https://dosxdos.app.iidos.com/apirest/rutas_notificaciones.php/notificaciones/" +
        usuario.id;
      const res = await fetch(url);

      if (!res.ok) {
        throw new Error(
          `Error en la solicitud de las notificaciones de ${usuario.nombre}`
        );
      }

      const resJson = await res.json();

      if (resJson.message) {
        const db = await new Promise((dbResolve, dbReject) => {
          const request = indexedDB.open("dosxdos");
          request.onsuccess = (event) => {
            const db = event.target.result;
            dbResolve(db);
          };
          request.onerror = (event) => {
            dbReject(
              new Error(
                `Error al abrir la base de datos para ingresar las notificaciones de ${usuario.nombre}`
              )
            );
          };
        });

        const transaction = db.transaction("notificaciones", "readwrite");
        const datosStore = transaction.objectStore("notificaciones");
        await datosStore.clear();
        const datos = resJson.message;
        if (Array.isArray(datos)) {
          datos.forEach((item) => {
            datosStore.add(item);
          });
        } else {
          datosStore.add(datos);
        }
      } else {
        throw new Error(
          `Error al solicitar las notificaciones de ${usuario.nombre}`
        );
      }

      resolve(true);
    } catch (err) {
      console.error(err);
      reject(err);
    }
  });
}

async function sincronizarNotificaciones() {
  try {
    if (navigator.onLine) {
      loaderOn();
      const limpiarNotificaciones = await limpiarDatos("notificaciones");
      if (limpiarNotificaciones) {
        notificacionesActuales = await fetchNotificaciones();
        if (notificacionesActuales) {
          return true;
        } else {
          alerta("Error al cargar las notificaciones del servidor");
          loaderOff();
          return false;
        }
      }
    }
  } catch (error) {
    console.error(error);
    const mensaje =
      "No se han sincronizado las notificaciones: " + error.message;
    alerta(mensaje);
    localStorage.setItem("mensaje", mensaje);
    return false;
  }
}

function notificar() {
  return new Promise(async (resolve) => {
    try {
      const sincronizacionInicial = await sincronizarNotificaciones();
      if (sincronizacionInicial) {
        notificationsStore = await leerDatos("notificaciones");
        notificacionesSinAcpetar = false;
        notificacionesSinAcpetarNumero = 0;
        notificationsStore.map(not => {
          if (!not.visto) {
            notificacionesSinAcpetar = true;
            notificacionesSinAcpetarNumero++;
          }
        })
        console.log('notificacionesSinAcpetar: ' + notificacionesSinAcpetar)
        console.log('notificacionesSinAcpetarNumero: ' + notificacionesSinAcpetarNumero)
        const notificaciones = document.getElementById("notificaciones");
        notificaciones.addEventListener("click", () => {
          window.location.href = "https://dosxdos.app.iidos.com/notificaciones.html";
        });
        const sinNotificaciones = document.getElementById("sinNotificaciones");
        sinNotificaciones.addEventListener("click", () => {
          window.location.href = "https://dosxdos.app.iidos.com/notificaciones.html";
        });
        if (notificacionesSinAcpetarNumero) {
          notificaciones.classList.remove('displayOff');
          notificaciones.classList.add('displayOn');
          document.getElementById('numNtf').innerHTML = notificacionesSinAcpetarNumero;
        } else {
          sinNotificaciones.classList.remove('displayOff');
          sinNotificaciones.classList.add('displayOn');
        }
      }
    } catch (err) {
      console.error(err);
      const mensaje =
        "Error en el sistema de notificaciones: " + error.message;
      alerta(mensaje);
    }
  });
}

function notificarOffline() {
  return new Promise(async (resolve) => {
    try {
      console.warn('Entra a la megaputa función')
      notificationsStore = await leerDatos("notificaciones");
      notificacionesSinAcpetar = false;
      notificacionesSinAcpetarNumero = 0;
      notificationsStore.map(not => {
        if (!not.visto) {
          notificacionesSinAcpetar = true;
          notificacionesSinAcpetarNumero++;
        }
      })
      console.log('notificacionesSinAcpetar: ' + notificacionesSinAcpetar)
      console.log('notificacionesSinAcpetarNumero: ' + notificacionesSinAcpetarNumero)
      const notificaciones = document.getElementById("notificaciones");
      notificaciones.addEventListener("click", () => {
        window.location.href = "https://dosxdos.app.iidos.com/notificaciones.html";
      });
      const sinNotificaciones = document.getElementById("sinNotificaciones");
      sinNotificaciones.addEventListener("click", () => {
        window.location.href = "https://dosxdos.app.iidos.com/notificaciones.html";
      });
      
      if (notificacionesSinAcpetarNumero) {
        notificaciones.classList.remove('displayOff');
        notificaciones.classList.add('displayOn');
        document.getElementById('numNtf').innerHTML = notificacionesSinAcpetarNumero;
      } else {
        sinNotificaciones.classList.remove('displayOff');
        sinNotificaciones.classList.add('displayOn');
      }
      console.warn('Ejecuta hasta ese puto punto');
    } catch (err) {
      console.error(err);
      const mensaje =
        "Error en el sistema de notificaciones: " + error.message;
      alerta(mensaje);
    }
  });
}

function eliminarTokenNotificaciones() {
  return new Promise(resolve => {
    try {
      tokenEliminar = localStorage.getItem('tokenNotificaciones');
      if (tokenEliminar != null) {
        urlTokenEliminar = 'https://dosxdos.app.iidos.com/apirest/rutas_notificaciones.php/notificaciones/token/' + tokenEliminar;
        fetch(urlTokenEliminar, {
          method: "DELETE",
        })
          .then((res) =>
            res.ok
              ? res.json()
              : reject(
                new Error(
                  `Error al eliminar el token de las notificaciones.`
                )
              )
          )
          .then((res) => {
            if (res.success) {
              localStorage.setItem('tokenNotificaciones', null);
              resolve(true);
            } else {
              console.error(res);
              alerta("Error al eliminar el token de las notificaciones: " + res.message)
              resolve(false);
            }

          })
          .catch((err) => {
            mensaje = err.message;
            console.error(err);
            alerta(mensaje);
            reject(new Error(mensaje));
          });
      }
    } catch (err) {
      mensaje = err.message;
      console.error(err);
      alerta(mensaje);
      reject(new Error(mensaje));
    }

  });
}

function notificarWebApp() {
  try {

    dataNativa = localStorage.getItem('dataNotificacionNativa');
    console.log('[Firebase] Mensaje en primer plano:', dataNativa);

    if (dataNativa != null) {
      const data = JSON.parse(dataNativa);

      // Extraer datos con valores por defecto si vienen nulos o indefinidos
      const title = data.title || "Nueva Notificación";
      const body = data.body || "Tienes una nueva notificación, por favor revísala en cuanto puedas.";
      const icon = data.icon || "https://dosxdos.app.iidos.com/img/dosxdoslogoNuevoRojo.png";
      const click_action = data.click_action || "https://dosxdos.app.iidos.com/notificaciones.html";

      // Crear un string bien formado
      const mensaje = "Tienes una nueva notificación: " + title + ": " + body;

      alerta(mensaje);

      const $notificaciones = document.getElementById('notificaciones');
      const $sinNotificaciones = document.getElementById('sinNotificaciones');

      if ($sinNotificaciones.classList.contains('displayOn')) {
        $sinNotificaciones.classList.remove('displayOn');
        $sinNotificaciones.classList.add('displayOff');
        $notificaciones.classList.remove('displayOff');
        $notificaciones.classList.add('displayOn');
      }

      const $numeroDeNotificacionesActuales = document.getElementById('numNtf');
      const numeroDeNotificacionesActuales = $numeroDeNotificacionesActuales.innerHTML;
      const numeroDeNotificacionesActualesInt = parseInt(numeroDeNotificacionesActuales);
      $numeroDeNotificacionesActuales.innerHTML = numeroDeNotificacionesActualesInt + 1;

      scrollToTop();
    } else {
      alerta("No se ha recibido un valor en la variable dataNotificacionNativa del localStorage");
    }
  } catch (err) {
    mensaje = err.message;
    console.error(err);
    alerta(mensaje);
  }
}
