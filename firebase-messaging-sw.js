if (navigator.onLine) {
    importScripts('https://www.gstatic.com/firebasejs/9.6.11/firebase-app-compat.js');
    importScripts('https://www.gstatic.com/firebasejs/9.6.11/firebase-messaging-compat.js');

    const firebaseConfig = {
        apiKey: "AIzaSyCPtGrKaRYMUxDx6SXIQvlewLNxyapnxcM",
        authDomain: "dosxdos-app.firebaseapp.com",
        projectId: "dosxdos-app",
        storageBucket: "dosxdos-app.firebasestorage.app",
        messagingSenderId: "52849872855",
        appId: "1:52849872855:web:3d68fa7ed8dd785fe14592",
        measurementId: "G-ZG5EKJDHL5"
    };

    firebase.initializeApp(firebaseConfig);

    const messaging = firebase.messaging();

    // Escucha mensajes en segundo plano
    messaging.onBackgroundMessage((payload) => {
        console.log('[firebase-messaging-sw.js] Recibió un mensaje en segundo plano: ', payload);
        const notificationTitle = payload.notification.title || 'Notificación';
        const notificationOptions = {
            body: payload.notification.body || '',
            icon: payload.notification.icon || '/icon.png',
            data: {
                url: payload.notification.click_action || 'https://dosxdos.app.iidos.com/notificaciones.html',
            },
        };
        self.registration.showNotification(notificationTitle, notificationOptions);
    });

    // Escucha mensajes en segundo plano
    messaging.onMessage((payload) => {
        console.log('[firebase-messaging-sw.js] Recibió un mensaje en segundo plano: ', payload);
        const notificationTitle = payload.notification.title || 'Notificación';
        const notificationOptions = {
            body: payload.notification.body || '',
            icon: payload.notification.icon || '/icon.png',
            data: {
                url: payload.notification.click_action || 'https://dosxdos.app.iidos.com/notificaciones.html',
            },
        };
        self.registration.showNotification(notificationTitle, notificationOptions);
    });

    // Maneja clics en las notificaciones
    self.addEventListener('notificationclick', (event) => {
        console.log('[firebase-messaging-sw.js] Notificación clickeada: ', event);

        event.notification.close(); // Cierra la notificación al hacer clic

        // Abre o redirige al usuario a la URL especificada en los datos de la notificación
        if (event.notification.data && event.notification.data.url) {
            event.waitUntil(
                clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
                    for (const client of clientList) {
                        // Si ya está abierta una pestaña con la URL, enfócala
                        if (client.url === event.notification.data.url && 'focus' in client) {
                            return client.focus();
                        }
                    }
                    // Si no hay ninguna pestaña abierta, abre una nueva
                    if (clients.openWindow) {
                        return clients.openWindow(event.notification.data.url);
                    }
                })
            );
        }
    });
}