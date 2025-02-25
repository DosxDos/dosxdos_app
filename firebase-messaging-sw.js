if (navigator.onLine) {
  importScripts(
    "https://www.gstatic.com/firebasejs/9.6.11/firebase-app-compat.js"
  );
  importScripts(
    "https://www.gstatic.com/firebasejs/9.6.11/firebase-messaging-compat.js"
  );

  const firebaseConfig = {
    apiKey: "AIzaSyCPtGrKaRYMUxDx6SXIQvlewLNxyapnxcM",
    authDomain: "dosxdos-app.firebaseapp.com",
    projectId: "dosxdos-app",
    storageBucket: "dosxdos-app.firebasestorage.app",
    messagingSenderId: "52849872855",
    appId: "1:52849872855:web:3d68fa7ed8dd785fe14592",
    measurementId: "G-ZG5EKJDHL5",
  };

  firebase.initializeApp(firebaseConfig);

  const messaging = firebase.messaging();

  // Escucha mensajes en segundo plano
  messaging.onBackgroundMessage((payload) => {
    console.log(
      "[firebase-messaging-sw.js] Recibió un mensaje en segundo plano:",
      payload
    );

    const notificationTitle = payload.data.title || "Notificación";
    const notificationOptions = {
      body: payload.data.body || "",
      icon:
        payload.data.icon ||
        "http://localhost/dosxdos_app/img/dosxdoslogoNuevoRojo.png",
      data: {
        url:
          payload.data.click_action ||
          "http://localhost/dosxdos_app/notificaciones.html",
      },
    };
    self.registration.showNotification(notificationTitle, notificationOptions);
  });

  // Maneja clics en las notificaciones
  self.addEventListener("notificationclick", (event) => {
    console.log("[firebase-messaging-sw.js] Notificación clickeada:", event);

    event.notification.close();

    let url =
      event.notification.data?.url ||
      "http://localhost/dosxdos_app/notificaciones.html";

    event.waitUntil(
      clients
        .matchAll({ type: "window", includeUncontrolled: true })
        .then((clientList) => {
          for (const client of clientList) {
            if (client.url === url && "focus" in client) {
              return client.focus();
            }
          }
          return clients.openWindow(url);
        })
    );
  });
}
