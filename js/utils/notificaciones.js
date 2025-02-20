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
