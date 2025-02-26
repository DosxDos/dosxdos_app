const $bellMobile = document.getElementById("bellMobile"),
  $mobileNotificationCount = document.getElementById("mobileNotificationCount"),
  $bellDesktop = document.getElementById("bellDesktop"),
  $desktopNotificationCount = document.getElementById("desktopNotificationCount");
sinNotificaciones = false;
notificacionesSinAcpetar = false;
notificacionesSinAcpetarNumero = 0;

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
      const limpiarNotificaciones = await limpiarDatos("dosxdos", "notificaciones");
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
      notificationsStore = await leerDatos("dosxdos", "notificaciones");
      notificationsStore.length == 0 ? sinNotificaciones = true : sinNotificaciones = false;
      if (sinNotificaciones) {
        renderizarSinNotificaciones();
      } else {
        notificationsStore.map((not) => {
          if (!not.visto) {
            notificacionesSinAcpetar = true;
            notificacionesSinAcpetarNumero++;
          }
        });
        if (notificacionesSinAcpetar) {
          renderizarConNotificaciones(notificacionesSinAcpetarNumero);
        } else {
          renderizarSinNotificaciones();
        }
      }
      console.warn("notificacionesSinAcpetar: " + notificacionesSinAcpetar);
      console.warn(
        "notificacionesSinAcpetarNumero: " + notificacionesSinAcpetarNumero
      );
      resolve(true);
    } catch (err) {
      console.error(err);
      const mensaje = "Error en el sistema de notificaciones: " + err.message;
      alerta(mensaje);
      resolve(false);
    }
  });
}

function renderizarSinNotificaciones() {
  //Mobiles
  $bellMobile.src = "https://dosxdos.app.iidos.com/img/bell2.png";
  if ($bellMobile.classList.contains("w-14")) {
    $bellMobile.classList.replace("w-14", "w-8");
  } else {
    $bellMobile.classList.add("w-8");
  }
  if (!$mobileNotificationCount.classList.contains("hidden")) {
    $mobileNotificationCount.classList.add("hidden");
  }
  //Desktops
  $bellDesktop.src = "https://dosxdos.app.iidos.com/img/bell2.png";
  if ($bellDesktop.classList.contains("w-12")) {
    $bellDesktop.classList.replace("w-12", "w-7");
  } else {
    $bellDesktop.classList.add("w-7");
  }
  if (!$desktopNotificationCount.classList.contains("hidden")) {
    $desktopNotificationCount.classList.add("hidden");
  }
  console.log('Se ha renderizado la campana sin notificaciones');
}

function renderizarConNotificaciones(numeroDeNotificacionesActuales) {
  //Mobiles
  $bellMobile.src = "https://dosxdos.app.iidos.com/img/bell.gif";
  if ($bellMobile.classList.contains("w-8")) {
    $bellMobile.classList.replace("w-8", "w-14");
  } else {
    $bellMobile.classList.add("w-14");
  }
  if ($mobileNotificationCount.classList.contains("hidden")) {
    $mobileNotificationCount.classList.remove("hidden");
    $mobileNotificationCount.textContent = numeroDeNotificacionesActuales;
  } else {
    $mobileNotificationCount.textContent = numeroDeNotificacionesActuales;
  }
  //Desktops
  $bellDesktop.src = "https://dosxdos.app.iidos.com/img/bell.gif";
  if ($bellDesktop.classList.contains("w-7")) {
    $bellDesktop.classList.replace("w-7", "w-12");
  } else {
    $bellDesktop.classList.add("w-12");
  }
  if ($desktopNotificationCount.classList.contains("hidden")) {
    $desktopNotificationCount.classList.remove("hidden");
    $desktopNotificationCount.textContent = numeroDeNotificacionesActuales;
  } else {
    $desktopNotificationCount.textContent = numeroDeNotificacionesActuales;
  }
  console.log('Se ha renderizado la campana con notificaciones');
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
          if (res.success) {
            localStorage.setItem("tokenNotificaciones", null);
            resolve(true);
          } else {
            console.error(res);
            alerta(
              "Error al eliminar el token de las notificaciones: " + res.message
            );
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
  });
}
