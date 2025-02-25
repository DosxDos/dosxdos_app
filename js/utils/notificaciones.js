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
      notificacionesSinAcpetarNumero = 0;
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

      // Desktop Notification Elements
      const notificaciones = document.getElementById("notificaciones");
      const numNtf = document.getElementById("numNtf");
      const sinNotificaciones = document.getElementById("sinNotificaciones");

      // Mobile Notification Elements
      const mobileNotificationCount = document.getElementById(
        "mobileNotificationCount"
      );
      const mobileBellImg = document.getElementById("bellMobile");

      // Add click events for desktop
      if (notificaciones) {
        notificaciones.addEventListener("click", () => {
          window.location.href =
            "https://dosxdos.app.iidos.com/notificaciones.html";
        });
      }

      if (sinNotificaciones) {
        sinNotificaciones.addEventListener("click", () => {
          window.location.href =
            "https://dosxdos.app.iidos.com/notificaciones.html";
        });
      }

      // Handle notification display
      if (notificacionesSinAcpetarNumero) {
        // Desktop Notifications
        if (notificaciones) {
          notificaciones.classList.remove("displayOff");
          notificaciones.classList.add("displayOn");
        }
        if (numNtf) {
          numNtf.innerHTML = notificacionesSinAcpetarNumero;
        }
        if (sinNotificaciones) {
          sinNotificaciones.classList.remove("displayOn");
          sinNotificaciones.classList.add("displayOff");
        }

        // Mobile Notifications
        if (mobileNotificationCount) {
          mobileNotificationCount.textContent = notificacionesSinAcpetarNumero;
          mobileNotificationCount.classList.remove("hidden");
        }
        if (mobileBellImg) {
          mobileBellImg.src = "https://dosxdos.app.iidos.com/img/bell.gif";
          mobileBellImg.classList.add("bell-animate");
        }
      } else {
        // Desktop Notifications
        if (notificaciones) {
          notificaciones.classList.remove("displayOn");
          notificaciones.classList.add("displayOff");
        }
        if (sinNotificaciones) {
          sinNotificaciones.classList.remove("displayOff");
          sinNotificaciones.classList.add("displayOn");
        }

        // Mobile Notifications
        if (mobileNotificationCount) {
          mobileNotificationCount.textContent = "0";
          mobileNotificationCount.classList.add("hidden");
        }
        if (mobileBellImg) {
          mobileBellImg.src = "https://dosxdos.app.iidos.com/img/bell2.png";
          mobileBellImg.classList.remove("bell-animate");
        }
      }

      resolve(true);
    } catch (err) {
      console.error(err);
      const mensaje = "Error en el sistema de notificaciones: " + err.message;
      alerta(mensaje);
      resolve(false);
    }
  });
}

function eliminarTokenNotificaciones() {
  return new Promise((resolve) => {
    tokenEliminar = localStorage.getItem("tokenNotificaciones");
    if (tokenEliminar != null) {
      urlTokenEliminar =
        "https://dosxdos.app.iidos.com/apirest/rutas_notificaciones.php/notificaciones/token/" +
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
