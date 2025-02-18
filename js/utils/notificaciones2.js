function fetchNotificaciones() {
  return new Promise(async (resolve, reject) => {
    try {
      const url =
        "http://localhost/dosxdos_app/apirest/rutas_notificaciones.php/notificaciones/" +
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
          const mensaje = "Se han sincronizado las notificaciones exitosamente";
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
      notificationsStore = await leerDatos("notificaciones");
      notificacionesSinAcpetar = false;
      console.log(notificationsStore);
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
        window.location.href = "http://localhost/dosxdos_app/notificaciones.html";
      });
      const sinNotificaciones = document.getElementById("sinNotificaciones");
      sinNotificaciones.addEventListener("click", () => {
        window.location.href = "http://localhost/dosxdos_app/notificaciones.html";
      });
      if (notificacionesSinAcpetarNumero) {
        notificaciones.classList.remove('displayOff');
        notificaciones.classList.add('displayOn');
        document.getElementById('numNtf').innerHTML = notificacionesSinAcpetarNumero;
      } else {
        sinNotificaciones.classList.remove('displayOff');
        sinNotificaciones.classList.add('displayOn');
      }
    } catch (err) {
      console.error(err);
      const mensaje =
        "Error en el sistema de notificaciones: " + error.message;
      alerta(mensaje);
    }
  });
}
