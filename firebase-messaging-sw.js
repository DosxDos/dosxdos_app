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
        alert("ha llegado una notificación");
        console.log('[firebase-messaging-sw.js] Recibió un mensaje en segundo plano: ', payload);
        const notificationTitle = payload.notification.title || 'Notificación';
        const notificationOptions = {
            body: payload.notification.body || '',
            icon: payload.notification.icon || '/icon.png',
        };
        self.registration.showNotification(notificationTitle, notificationOptions);
    });
}