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
        throw new Error(`Error fetching notifications for ${usuario.nombre}`);
      }

      const resJson = await res.json();

      if (!resJson.message) {
        throw new Error(`Empty or invalid notifications for ${usuario.nombre}`);
      }

      const db = await new Promise((dbResolve, dbReject) => {
        const request = indexedDB.open("dosxdos");
        request.onsuccess = (event) => dbResolve(event.target.result);
        request.onerror = (event) =>
          dbReject(new Error(`Error opening DB: ${event.target.error}`));
      });

      const transaction = db.transaction("notificaciones", "readwrite");
      const store = transaction.objectStore("notificaciones");
      const data = Array.isArray(resJson.message)
        ? resJson.message
        : [resJson.message];

      // Wait for all puts to finish
      await Promise.all(
        data.map((item) => {
          return new Promise((res, rej) => {
            const request = store.put(item);
            request.onsuccess = () => res(true);
            request.onerror = () => {
              console.error("Failed to put item", item, request.error);
              rej(request.error);
            };
          });
        })
      );

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
      // Reset counter variables
      sinNotificaciones = false;
      notificacionesSinAcpetar = false;
      notificacionesSinAcpetarNumero = 0;

      const dedupNotifications = (notifications) => {
        const uniqueNotifications = new Map();

        notifications.forEach(notification => {
          // Create a unique key that considers the most important attributes
          const key = notification.id ||
            `${notification.usuario_id}_${notification.titulo}_${notification.fecha_envio}`;

          // Only keep the first occurrence
          if (!uniqueNotifications.has(key)) {
            uniqueNotifications.set(key, notification);
          }
        });

        return Array.from(uniqueNotifications.values());
      };

      // Get notifications from IndexedDB
      notificationsStore = await leerDatos("dosxdos", "notificaciones");

      // Deduplicate notifications
      notificationsStore = dedupNotifications(notificationsStore);

      // Check if we have notifications
      sinNotificaciones = notificationsStore.length === 0;

      if (sinNotificaciones) {
        renderizarSinNotificaciones();
      } else {
        // Count ONLY unread notifications
        notificacionesSinAcpetarNumero = notificationsStore.filter(not => !not.visto).length;
        notificacionesSinAcpetar = notificacionesSinAcpetarNumero > 0;

        if (notificacionesSinAcpetar) {
          renderizarConNotificaciones(notificacionesSinAcpetarNumero);
        } else {
          renderizarSinNotificaciones();
        }
      }

      // Store the count in a global variable for the event listener to access
      window.correctNotificationCount = notificacionesSinAcpetarNumero;

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
    $bellDesktop.classList.replace("w-12", "w-7"); ç
  } else {
    $bellDesktop.classList.add("w-7");
  }
  if (!$desktopNotificationCount.classList.contains("hidden")) {
    $desktopNotificationCount.classList.add("hidden");
  }
  console.log('Se ha renderizado la campana sin notificaciones');
}

function renderizarConNotificaciones(numeroDeNotificacionesActuales) {
  // Force the correct number to be used (prevent incorrect values from being passed in)
  if (typeof window.correctNotificationCount === "number") {
    numeroDeNotificacionesActuales = window.correctNotificationCount;
  }

  $mobileNotificationCount.textContent = "";
  $desktopNotificationCount.textContent = "";

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
    $desktopNotificationCount.textContent = numeroDeNotificacionesActuales;
  } else {
    $mobileNotificationCount.textContent = numeroDeNotificacionesActuales;
    $desktopNotificationCount.textContent = numeroDeNotificacionesActuales;
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
    $mobileNotificationCount.textContent = numeroDeNotificacionesActuales;
  } else {
    $desktopNotificationCount.textContent = numeroDeNotificacionesActuales;
    $mobileNotificationCount.textContent = numeroDeNotificacionesActuales;
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

// Add this to your DOMContentLoaded function
document.addEventListener('DOMContentLoaded', function () {
  // Wait a short moment to let the notification code finish
  setTimeout(function () {
    const mobileCount = document.getElementById("mobileNotificationCount");
    const desktopCount = document.getElementById("desktopNotificationCount");
    const bellMobile = document.getElementById("bellMobile");
    const bellDesktop = document.getElementById("bellDesktop");

    // If mobile count is visible, make desktop count visible too
    if (mobileCount && !mobileCount.classList.contains("hidden") && desktopCount) {
      desktopCount.classList.remove("hidden");
      desktopCount.textContent = mobileCount.textContent;

      // Also force the bell images to be consistent
      if (bellMobile && bellDesktop && bellMobile.src.includes("bell.gif")) {
        console.log("Syncing desktop bell image with mobile");
        bellDesktop.src = "https://dosxdos.app.iidos.com/img/bell.gif";
        bellDesktop.classList.remove("w-7");
        bellDesktop.classList.add("w-12");
      }
    }

    // Log the bell image sources to debug
    console.log("Bell Mobile src:", bellMobile ? bellMobile.src : "not found");
    console.log("Bell Desktop src:", bellDesktop ? bellDesktop.src : "not found");
  }, 1000);
});