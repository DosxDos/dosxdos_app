if ('serviceWorker' in navigator) {
    navigator.serviceWorker
      .register('/firebase-messaging-sw.js') // Ajusta la ruta según dónde se halle el archivo
      .then((registration) => {
        console.log('SW registrado con éxito el SW de Firebase:', registration);
      })
      .catch((err) => {
        console.error('Fallo en el registro del SW de Firebase:', err);
      });
  }
  