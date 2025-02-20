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
          request.onsuccess = (event) => dbResolve(event.target.result);
          request.onerror = (event) =>
            dbReject(new Error(`Error al abrir la base de datos`));
        });

        const transaction = db.transaction("notificaciones", "readwrite");
        const store = transaction.objectStore("notificaciones");

        // Clear existing data first
        await store.clear();

        const datos = resJson.message;
        if (Array.isArray(datos)) {
          for (const item of datos) {
            try {
              await store.add(item);
            } catch (e) {
              console.log("Skipping duplicate notification:", item.id);
            }
          }
        } else {
          await store.add(datos);
        }
      }

      resolve(true);
    } catch (err) {
      console.error(err);
      reject(err);
    }
  });
}

async function sincronizarNotificaciones() {
  if (!navigator.onLine) return false;

  try {
    loaderOn();
    await limpiarDatos("dosxdos", "notificaciones");
    const notificacionesActuales = await fetchNotificaciones();

    if (notificacionesActuales) {
      return true;
    } else {
      alerta("Error al cargar las notificaciones del servidor");
      return false;
    }
  } catch (error) {
    const mensaje =
      "No se han sincronizado las notificaciones: " + error.message;
    alerta(mensaje);
    localStorage.setItem("mensaje", mensaje);
    return false;
  } finally {
    loaderOff();
  }
}

function notificar() {
  return new Promise(async (resolve) => {
    try {
      notificationsStore = await leerDatos("notificaciones");
      notificacionesSinAcpetar = false;
      notificationsStore.map((not) => {
        if (!not.visto) {
          notificacionesSinAcpetar = true;
          notificacionesSinAcpetarNumero++;
        }
      });
      console.log("notificacionesSinAcpetar: " + notificacionesSinAcpetar);
      console.log(
        "notificacionesSinAcpetarNumero: " + notificacionesSinAcpetarNumero
      );
      const notificaciones = document.getElementById("notificaciones");
      notificaciones.addEventListener("click", () => {
        window.location.href =
          "http://localhost/dosxdos_app/notificaciones.html";
      });
      const sinNotificaciones = document.getElementById("sinNotificaciones");
      sinNotificaciones.addEventListener("click", () => {
        window.location.href =
          "http://localhost/dosxdos_app/notificaciones.html";
      });
      if (notificacionesSinAcpetarNumero) {
        notificaciones.classList.remove("displayOff");
        notificaciones.classList.add("displayOn");
        document.getElementById("numNtf").innerHTML =
          notificacionesSinAcpetarNumero;
      } else {
        sinNotificaciones.classList.remove("displayOff");
        sinNotificaciones.classList.add("displayOn");
      }
    } catch (err) {
      console.error(err);
      const mensaje = "Error en el sistema de notificaciones: " + error.message;
      alerta(mensaje);
    }
  });
}

function eliminarTokenNotificaciones() {
  return new Promise((resolve) => {
    tokenEliminar = localStorage.getItem("tokenNotificaciones");
    if (tokenEliminar != null) {
      urlTokenEliminar =
        "http://localhost/dosxdos_app/apirest/rutas_notificaciones.php/notificaciones/token/" +
        tokenEliminar;
      fetch(urlTokenEliminar, {
        method: "DELETE",
      })
        .then((res) =>
          res.ok
            ? res.json()
            : reject(
                new Error(`Error al eliminar el token de las notificaciones.`)
              )
        )
        .then((res) => {
          resolve(true);
        })
        .catch((err) => {
          mensaje = err.message;
          console.error(err);
          alerta(mensaje);
          reject(new Error(mensaje));
        });
    }
  });
}
