//asignar un nombre y versión al cache
const CACHE_NAME = 'dosxdos114',
  urlsToCache = [
    'http://localhost:8080/index.html?utm_source=web_app_manifest',
    'http://localhost:8080/index.html',
    'http://localhost:8080/manifest.json',
    'http://localhost:8080/serviceworker.js',
    'http://localhost:8080/sw.js',
    'http://localhost:8080/rutas_montador.html',
    'http://localhost:8080/ruta_montador.html',
    'http://localhost:8080/linea_montador.html',
    'http://localhost:8080/fotos_y_firmas.html',
    'http://localhost:8080/ot_completa.html',
    'http://localhost:8080/ot.html',
    'http://localhost:8080/css/fuentes/Roboto/Roboto-Light.ttf',
    'http://localhost:8080/css/fuentes/Merriweather/Merriweather-Light.ttf',
    'http://localhost:8080/css/fuentes/Merriweather/Merriweather-Bold.ttf',
    'http://localhost:8080/css/fuentes/Lora/Lora-Regular.ttf',
    'http://localhost:8080/css/fuentes/Lora/Lora-Medium.ttf',
    'http://localhost:8080/css/fuentes/Lora/Lora-Bold.ttf',
    'http://localhost:8080/img/candado.png',
    'http://localhost:8080/img/casa.png',
    'http://localhost:8080/img/casaWhite.png',
    'http://localhost:8080/img/dosxdos.png',
    'http://localhost:8080/img/email.png',
    'http://localhost:8080/img/flechaAbajo.png',
    'http://localhost:8080/img/fondo.jpg',
    'http://localhost:8080/img/logoPwa1024.png',
    'http://localhost:8080/img/logoPwa512.png',
    'http://localhost:8080/img/logoPwa384.png',
    'http://localhost:8080/img/logoPwa256.png',
    'http://localhost:8080/img/logoPwa192.png',
    'http://localhost:8080/img/logoPwa128.png',
    'http://localhost:8080/img/logoPwa96.png',
    'http://localhost:8080/img/logoPwa64.png',
    'http://localhost:8080/img/logoPwa32.png',
    'http://localhost:8080/img/logoPwa16.png',
    'http://localhost:8080/img/logo300.png',
    'http://localhost:8080/img/lupa.png',
    'http://localhost:8080/img/reloj.png',
    'http://localhost:8080/img/relojWhite.png',
    'http://localhost:8080/img/usuario.png',
    'http://localhost:8080/img/rutasWhite.png',
    'http://localhost:8080/img/cerrar.png',
    'http://localhost:8080/img/usuarios.png',
    'http://localhost:8080/img/trash.png',
    'http://localhost:8080/img/folder.png',
    'http://localhost:8080/img/comprimido.png',
    'http://localhost:8080/img/task.png',
    'http://localhost:8080/img/work.png',
    'http://localhost:8080/css/cdn_data_tables.css',
    'http://localhost:8080/js/jquery.js',
    'http://localhost:8080/js/data_tables.js',
    'http://localhost:8080/js/cdn_data_tables.js',
    'http://localhost:8080/js/index_db.js',
    'http://localhost:8080/img/tienda.png',
    'http://localhost:8080/img/clientes.png',
    'http://localhost:8080/img/editar.png',
    'http://localhost:8080/img/archivar.png',
    'http://localhost:8080/img/back.png',
    'http://localhost:8080/img/visible.png',
    'http://localhost:8080/img/no_visible.png',
    'http://localhost:8080/img/crear.png',
    'http://localhost:8080/img/logo_clientes.png',
    'http://localhost:8080/img/logo2930.png',
    'http://localhost:8080/img/logo_clientes.png',
    'http://localhost:8080/img/instalar.png',
    'http://localhost:8080/espanol.json',
    'http://localhost:8080/english.json',
    'http://localhost:8080/pv.html',
    'http://localhost:8080/crear_pv.html',
    'http://localhost:8080/editar_pv.html',
    'http://localhost:8080/editar_pv.html',
    'http://localhost:8080/js/fixed_header.js',
    'http://localhost:8080/css/fuentes/Futura/Futura_Bold.otf',
    'http://localhost:8080/css/fuentes/Futura/Futura_Light.otf',
    'http://localhost:8080/css/fuentes/Futura/Futura_Medium.otf',
    'http://localhost:8080/img/alerta.png',
    'http://localhost:8080/img/saludo.png',
    'http://localhost:8080/img/dm.png',
    'http://localhost:8080/img/papelera.png',
    'http://localhost:8080/horarios.html',
    'http://localhost:8080/lineas_ot.html',
    'http://localhost:8080/lineas.html',
    'http://localhost:8080/usuarios_oficina.html',
    'http://localhost:8080/dm.html',
    'http://localhost:8080/reciclar.html',
    'http://localhost:8080/rutas_inactivas.html',
    'http://localhost:8080/historial_montador.html',
    'http://localhost:8080/linea_historial_montador.html',
    'http://localhost:8080/img/trabajos.png',
    'http://localhost:8080/img/clientes2.png',
    'http://localhost:8080/img/sincronizar.png',
    'http://localhost:8080/img/historial.png',
    'http://localhost:8080/css/tailwindmain.css',
    'http://localhost:8080/firebase-messaging-sw.js',
    'http://localhost:8080/img/dosxdoslogoNuevoRojo.png',
    'http://localhost:8080/js/notificaciones.js',
    'http://localhost:8080/js/navigation.js',
    'http://localhost:8080/js/loadFirebase.js',
    'http://localhost:8080/img/bell.gif',
    'http://localhost:8080/img/bell2.png',
    'http://localhost:8080/img/Isotipo-38.png',
    'http://localhost:8080/notificaciones.html',
    'http://localhost:8080/img/texture-red.svg',
    'http://localhost:8080/img/texture-white.svg',
    'http://localhost:8080/css/index.css',
    'http://localhost:8080/gsap.min.js',
    'http://localhost:8080/js/informe_ot.js',
    'http://localhost:8080/css/informe_ot.css',
    'http://localhost:8080/js/pdf_informe_ot.js',
    'http://localhost:8080/js/informe_ot_visuales_montajes.js',
    'http://localhost:8080/informe_ot_visuales_montajes.html',
    'http://localhost:8080/css/informe_ot_visuales_montajes.css',
    'http://localhost:8080/js/pdf_informe_ot_visuales_montajes.js',
    'http://localhost:8080/js/bridge-redirect.js'

  ],

  urlsToUpdate = [
    'http://localhost:8080/index.html?utm_source=web_app_manifest',
    'http://localhost:8080/index.html',
    'http://localhost:8080/manifest.json',
    'http://localhost:8080/serviceworker.js',
    'http://localhost:8080/sw.js',
    'http://localhost:8080/rutas_montador.html',
    'http://localhost:8080/ruta_montador.html',
    'http://localhost:8080/linea_montador.html',
    'http://localhost:8080/fotos_y_firmas.html',
    'http://localhost:8080/ot_completa.html',
    'http://localhost:8080/ot.html',
    'http://localhost:8080/espanol.json',
    'http://localhost:8080/english.json',
    'http://localhost:8080/pv.html',
    'http://localhost:8080/crear_pv.html',
    'http://localhost:8080/editar_pv.html',
    'http://localhost:8080/js/index_db.js',
    'http://localhost:8080/img/logoPwa1024.png',
    'http://localhost:8080/img/logoPwa512.png',
    'http://localhost:8080/img/logoPwa384.png',
    'http://localhost:8080/img/logoPwa256.png',
    'http://localhost:8080/img/logoPwa192.png',
    'http://localhost:8080/img/logoPwa128.png',
    'http://localhost:8080/img/logoPwa96.png',
    'http://localhost:8080/img/logoPwa64.png',
    'http://localhost:8080/img/logoPwa32.png',
    'http://localhost:8080/img/logoPwa16.png',
    'http://localhost:8080/img/saludo.png',
    'http://localhost:8080/horarios.html',
    'http://localhost:8080/lineas_ot.html',
    'http://localhost:8080/lineas.html',
    'http://localhost:8080/usuarios_oficina.html',
    'http://localhost:8080/dm.html',
    'http://localhost:8080/reciclar.html',
    'http://localhost:8080/rutas_inactivas.html',
    'http://localhost:8080/historial_montador.html',
    'http://localhost:8080/linea_historial_montador.html',
    'http://localhost:8080/notificaciones.html',
    'http://localhost:8080/js/notificaciones.js',
    'http://localhost:8080/js/loadFirebase.js',
    'http://localhost:8080/css/index.css',
    'http://localhost:8080/js/informe_ot.js',
    'http://localhost:8080/css/informe_ot.css',
    'http://localhost:8080/js/pdf_informe_ot.js',
    'http://localhost:8080/informe_ot_visuales_montajes.html',
    'http://localhost:8080/js/informe_ot_visuales_montajes.js',
    'http://localhost:8080/css/informe_ot_visuales_montajes.css',
    'http://localhost:8080/js/pdf_informe_ot_visuales_montajes.js',
    'http://localhost:8080/js/bridge-redirect.js'
  ];

//durante la fase de instalación, generalmente se almacena en caché los activos estáticos
self.addEventListener('install', e => {
  e.waitUntil(
    caches.keys()
      .then(cacheNames => {
        return Promise.all(
          cacheNames.map(cacheName => {
            // Eliminamos toda la cache antigua
            caches.delete(cacheName, { force: true });
          })
        );
      })
      .then(() => caches.open(CACHE_NAME))
      .then(cache => {
        return Promise.all(
          urlsToCache.map(url => {
            return fetch(url, { cache: 'no-store' })
              .then(response => {
                if (!response.ok) {
                  console.error(`Error al intentar agregar un elemento a la caché: Failed to fetch ${url}`);
                }
                cache.put(url, response);
              });
          })
        );
      })
      .then(() => self.skipWaiting())
      .catch(err => console.log('Falló registro de caché', err))
  );
});

//una vez que se instala el SW, se activa y busca los recursos para hacer que funcione sin conexión
self.addEventListener('activate', e => {
  e.waitUntil(
    caches.keys()
      .then(cacheNames => {
        return Promise.all(
          cacheNames.map(cacheName => {
            // Eliminamos lo que ya no se necesita en caché
            if (CACHE_NAME != cacheName) {
              caches.delete(cacheName, { force: true });
            }
          })
        );
      })
      // Le indica al SW activar el cache actual
      .then(() => self.clients.claim())
      .then(() => self.skipWaiting())
      .catch(err => console.log('Falló la activación de caché', err))
  )
})


self.addEventListener('fetch', event => {
  event.respondWith(
    (async function () {
      if (urlsToCache.includes(event.request.url)) {
        if (navigator.onLine) {
          try {
            const response = await fetch(event.request, { cache: 'no-store' });
            if (urlsToUpdate.includes(event.request.url)) {
              const cache = await caches.open(CACHE_NAME);
              cache.put(event.request, response.clone());
              console.log('Respuesta del servidor y se actualizó en caché: ' + event.request.url);
              return response;
            } else {
              console.log('Respuesta del servidor: ' + event.request.url);
              return response;
            }
          } catch (error) {
            console.error('Error en la solicitud: ' + event.request.url + '-' + error);
          }
        } else {
          const response = await caches.match(event.request);
          console.log('Respuesta de caché: ' + event.request.url);
          return response;
        }
      } else {
        console.log('Respuesta del servidor: ' + event.request.url);
        return fetch(event.request, { cache: 'no-store' });
      }
    })()
  );
});
