async function loadFirebase() {
    try {

        const userAgent = navigator.userAgent;
        const isAndroid = /android/i.test(userAgent);
        const isiOS = /(iphone|ipad|ipod)/i.test(userAgent);

        if (isAndroid || isiOS) {
            console.warn("Dispositivo Android o Ios, Firebase no será cargado.");
            return;
        }

        if (!navigator.onLine) {
            console.warn("Sin conexión, Firebase no será cargado.");
            return;
        }

        console.log("Conexión activa. Cargando Firebase...");

        // Importa Firebase dinámicamente solo si hay conexión
        const { initializeApp } = await import("https://www.gstatic.com/firebasejs/9.6.11/firebase-app.js");
        const { getMessaging, onMessage } = await import("https://www.gstatic.com/firebasejs/9.6.11/firebase-messaging.js");

        const firebaseConfig = {
            apiKey: "AIzaSyCPtGrKaRYMUxDx6SXIQvlewLNxyapnxcM",
            authDomain: "dosxdos-app.firebaseapp.com",
            projectId: "dosxdos-app",
            storageBucket: "dosxdos.app.firebasestorage.app",
            messagingSenderId: "52849872855",
            appId: "1:52849872855:web:3d68fa7ed8dd785fe14592",
            measurementId: "G-ZG5EKJDHL5"
        };

        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);

        // Manejar mensajes en primer plano
        onMessage(messaging, (payload) => {
            console.log('[Firebase] Mensaje en primer plano:', payload);

            // Extraer datos con valores por defecto si vienen nulos o indefinidos
            const title = payload?.notification?.title || "Nueva Notificación";
            const body = payload?.notification?.body || "Tienes una nueva notificación, por favor revísala en cuanto puedas.";
            const icon = payload?.notification?.icon || "https://dosxdos.app.iidos.com/img/dosxdoslogoNuevoRojo.png";
            const click_action = payload?.notification?.click_action || "https://dosxdos.app.iidos.com/notificaciones.html";

            // Crear un string bien formado
            const mensaje = title + ": " + body;

            // Guardar en localStorage
            localStorage.setItem('mensaje', mensaje);
            console.log("Notificación guardada en localStorage:", mensaje);

            // Mostrar notificación en la UI si el usuario tiene permisos activados
            if (Notification.permission === "granted") {
                new Notification(title, {
                    body,
                    icon
                });
            }

            location.reload();

        });

        console.log('Firebase cargado exitosamente')
    } catch (error) {
        console.error('No es posible cargar Firebase en este dispositivo')
        console.error(error);
    }

}

// Escuchar cambios de conexión
window.addEventListener("online", loadFirebase);

// Cargar Firebase al inicio si hay conexión
loadFirebase();

