async function loadFirebase() {
  try {
    const userAgent = navigator.userAgent;
    const isAndroid = /android/i.test(userAgent);
    const isiOS = /(iphone|ipad|ipod)/i.test(userAgent);

    if (isAndroid || isiOS) {
      console.warn(
        "Dispositivo Android o Ios, Firebase web push notifications no será cargado."
      );
      return;
    }

    if (!navigator.onLine) {
      console.warn(
        "Sin conexión, Firebase web push notifications no será cargado."
      );
      return;
    }

    console.warn(
      "Conexión activa. Cargando Firebase web push notifications..."
    );

    // Importa Firebase dinámicamente solo si hay conexión
    const { initializeApp } = await import(
      "https://www.gstatic.com/firebasejs/9.6.11/firebase-app.js"
    );
    const { getMessaging, onMessage } = await import(
      "https://www.gstatic.com/firebasejs/9.6.11/firebase-messaging.js"
    );

    const firebaseConfig = {
      apiKey: "AIzaSyCPtGrKaRYMUxDx6SXIQvlewLNxyapnxcM",
      authDomain: "dosxdos-app.firebaseapp.com",
      projectId: "dosxdos-app",
      storageBucket: "dosxdos.app.firebasestorage.app",
      messagingSenderId: "52849872855",
      appId: "1:52849872855:web:3d68fa7ed8dd785fe14592",
      measurementId: "G-ZG5EKJDHL5",
    };

    const app = initializeApp(firebaseConfig);
    const messaging = getMessaging(app);

    // Manejar mensajes en primer plano
    onMessage(messaging, (payload) => {
      console.log(
        "[Firebase web push notifications] Mensaje en primer plano:",
        payload
      );

      // Extraer datos con valores por defecto si vienen nulos o indefinidos
      const title = payload.data.title || "Nueva Notificación";
      const body =
        payload.data.body ||
        "Tienes una nueva notificación, por favor revísala en cuanto puedas.";
      const icon =
        payload.data.icon ||
        "http://localhost/dosxdos_app/img/dosxdoslogoNuevoRojo.png";
      const click_action = "http://localhost/dosxdos_app/notificaciones.html";

      // Crear un string bien formado
      const mensaje = "Tienes una nueva notificación: " + title + ": " + body;

      alerta(mensaje);

      //Verificar si la página tiene campana o no, y actuar en consecuencia
      if (
        document.getElementById("desktopBellContainer") &&
        document.getElementById("mobileBellContainer")
      ) {
        notificacionesSinAcpetarNumero = notificacionesSinAcpetarNumero + 1;
        console.warn(
          "notificacionesSinAcpetarNumero: " + notificacionesSinAcpetarNumero
        );
        renderizarConNotificaciones(notificacionesSinAcpetarNumero);
        scrollToTop();
        // Mostrar notificación en la UI
        // Crear la notificación nativa del navegador
        const notification = new Notification(title, {
          body: body,
          icon: icon,
        });
        // Manejar el clic en la notificación
        notification.onclick = () => {
          console.log("Notificación clickeada, abriendo URL:", click_action);
          window.open(click_action, "_blank");
        };
      } else {
        scrollToTop();
        // Mostrar notificación en la UI
        // Crear la notificación nativa del navegador
        const notification = new Notification(title, {
          body: body,
          icon: icon,
        });
        // Manejar el clic en la notificación
        notification.onclick = () => {
          console.log("Notificación clickeada, abriendo URL:", click_action);
          window.open(click_action, "_blank");
        };
        setTimeout(() => {
          //Recargar de nuevo la página
          localStorage.setItem("mensaje", mensaje);
          window.location.reload();
        }, 3000);
      }
    });

    console.warn("Firebase Web push notifications cargado exitosamente");
  } catch (error) {
    console.error(
      "No es posible cargar Firebase web push notifications en este dispositivo"
    );
    console.error(error);
  }
}

// Cargar Firebase al inicio si hay conexión
if (navigator.onLine) loadFirebase();

// Manejar mensajes en primer plano en notificaciones nativas de Android e Ios
function notificarWebApp() {
  try {
    dataNativa = localStorage.getItem("dataNotificacionNativa");
    console.log("[Firebase] Mensaje en primer plano:", dataNativa);

    if (dataNativa != null) {
      const data = JSON.parse(dataNativa);

      // Extraer datos con valores por defecto si vienen nulos o indefinidos
      const title = data.title || "Nueva Notificación";
      const body =
        data.body ||
        "Tienes una nueva notificación, por favor revísala en cuanto puedas.";
      const icon =
        data.icon ||
        "http://localhost/dosxdos_app/img/dosxdoslogoNuevoRojo.png";
      const click_action =
        data.click_action || "http://localhost/dosxdos_app/notificaciones.html";

      // Crear un string bien formado
      const mensaje = "Tienes una nueva notificación: " + title + ": " + body;

      alerta(mensaje);

      //Verificar si la página tiene campana o no, y actuar en consecuencia
      if (
        document.getElementById("desktopBellContainer") &&
        document.getElementById("mobileBellContainer")
      ) {
        notificacionesSinAcpetarNumero = notificacionesSinAcpetarNumero + 1;
        console.warn(
          "notificacionesSinAcpetarNumero: " + notificacionesSinAcpetarNumero
        );
        renderizarConNotificaciones(notificacionesSinAcpetarNumero);
        scrollToTop();
      } else {
        scrollToTop();
        setTimeout(() => {
          //Recargar de nuevo la página
          localStorage.setItem("mensaje", mensaje);
          window.location.reload();
        }, 3000);
      }
    } else {
      alerta(
        "No se ha recibido un valor en la variable dataNotificacionNativa del localStorage"
      );
    }
  } catch (err) {
    mensaje = err.message;
    console.error(err);
    alerta(mensaje);
  }
}
