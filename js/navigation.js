function updateUserDisplay() {
  if (usuario) {
    // Desktop user
    const nombreUsuarioElement = document.getElementById("nombreUsuario");
    if (nombreUsuarioElement) {
      nombreUsuarioElement.textContent = usuario.nombre;
    }

    const nombreUsuarioDesktopElement = document.getElementById("nombreUsuarioDesktop");
    if (nombreUsuarioDesktopElement) {
      nombreUsuarioDesktopElement.textContent = usuario.nombre;
    }

    const imagenUsuarioElement = document.getElementById("imagenUsuario");
    if (imagenUsuarioElement && usuario.imagen !== "0") {
      imagenUsuarioElement.src = usuario.imagen;
    }

    // Mobile user
    const nombreUsuarioMobileElement = document.getElementById("nombreUsuarioMobile");
    if (nombreUsuarioMobileElement) {
      nombreUsuarioMobileElement.textContent = usuario.nombre;
    }

    const imagenUsuarioMobileElement = document.getElementById("imagenUsuarioMobile");
    if (imagenUsuarioMobileElement && usuario.imagen !== "0") {
      imagenUsuarioMobileElement.src = usuario.imagen;
    }
  }
}

/* USER ROLE MANAGEMENT */
function setupMenu(userRole) {
  // Create desktop navigation dynamically
  createDesktopNavigation(userRole);

  // Populate mobile menu based on user role
  populateMobileMenu(userRole);
}


function populateMobileMenu(userRole) {
  const mobileMenuNav = document.querySelector("#opcionesMenu nav");
  if (!mobileMenuNav) return;

  mobileMenuNav.innerHTML = '';

  // Standardized function to render consistent SVG icons
  const renderIcon = (paths) => `
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" 
       stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
    ${paths}
  </svg>
  `;

  const mobileMenuItems = {
    admon: [
      {
        id: "archivos",
        href: "https://dosxdos.app.iidos.com/ot.html",
        icon: renderIcon(`
        <path d="M12.37 8.87988H17.62" stroke-linecap="round" stroke-linejoin="round"></path>
        <path d="M6.38 8.87988L7.13 9.62988L9.38 7.37988" stroke-linecap="round" stroke-linejoin="round"></path>
        <path d="M12.37 15.8799H17.62" stroke-linecap="round" stroke-linejoin="round"></path>
        <path d="M6.38 15.8799L7.13 16.6299L9.38 14.3799" stroke-linecap="round" stroke-linejoin="round"></path>
        <path d="M9 22H15C20 22 22 20 22 15V9C22 4 20 2 15 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22Z" stroke-linecap="round" stroke-linejoin="round"></path>
      `),
        text: "OT"
      },
      {
        id: "icLineasOt",
        href: "https://dosxdos.app.iidos.com/lineas_ot.html",
        icon: renderIcon(`
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
        <polyline points="14 2 14 8 20 8" />
        <path d="M9 15l2 2 4-4" />
      `),
        text: "Líneas OT"
      },
      {
        id: "rutasIcono",
        href: "https://dosxdos.app.iidos.com/rutas.html",
        icon: renderIcon(`
            <svg viewBox="0 0 100 100" class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
            xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
            role="img" preserveAspectRatio="xMidYMid meet">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
              <path
                d="M21 32C9.459 32 0 41.43 0 52.94c0 4.46 1.424 8.605 3.835 12.012l14.603 25.244c2.045 2.672 3.405 2.165 5.106-.14l16.106-27.41c.325-.59.58-1.216.803-1.856A20.668 20.668 0 0 0 42 52.94C42 41.43 32.544 32 21 32zm0 9.812c6.216 0 11.16 4.931 11.16 11.129c0 6.198-4.944 11.127-11.16 11.127c-6.215 0-11.16-4.93-11.16-11.127c0-6.198 4.945-11.129 11.16-11.129z"
                fill="currentColor" stroke="currentColor"></path>
              <path
                d="M87.75 0C81.018 0 75.5 5.501 75.5 12.216c0 2.601.83 5.019 2.237 7.006l8.519 14.726c1.193 1.558 1.986 1.262 2.978-.082l9.395-15.99c.19-.343.339-.708.468-1.082a12.05 12.05 0 0 0 .903-4.578C100 5.5 94.484 0 87.75 0zm0 5.724c3.626 0 6.51 2.876 6.51 6.492c0 3.615-2.884 6.49-6.51 6.49c-3.625 0-6.51-2.875-6.51-6.49c0-3.616 2.885-6.492 6.51-6.492z"
                fill="currentColor" stroke="currentColor"></path>
              <path
                d="M88.209 37.412c-2.247.05-4.5.145-6.757.312l.348 5.532a126.32 126.32 0 0 1 6.513-.303zm-11.975.82c-3.47.431-6.97 1.045-10.43 2.032l1.303 5.361c3.144-.896 6.402-1.475 9.711-1.886zM60.623 42.12a24.52 24.52 0 0 0-3.004 1.583l-.004.005l-.006.002c-1.375.866-2.824 1.965-4.007 3.562c-.857 1.157-1.558 2.62-1.722 4.35l5.095.565c.038-.406.246-.942.62-1.446h.002v-.002c.603-.816 1.507-1.557 2.582-2.235l.004-.002a19.64 19.64 0 0 1 2.388-1.256zM58 54.655l-3.303 4.235c.783.716 1.604 1.266 2.397 1.726l.01.005l.01.006c2.632 1.497 5.346 2.342 7.862 3.144l1.446-5.318c-2.515-.802-4.886-1.576-6.918-2.73c-.582-.338-1.092-.691-1.504-1.068zm13.335 5.294l-1.412 5.327l.668.208l.82.262c2.714.883 5.314 1.826 7.638 3.131l2.358-4.92c-2.81-1.579-5.727-2.611-8.538-3.525l-.008-.002l-.842-.269zm14.867 7.7l-3.623 3.92c.856.927 1.497 2.042 1.809 3.194l.002.006l.002.009c.372 1.345.373 2.927.082 4.525l5.024 1.072c.41-2.256.476-4.733-.198-7.178c-.587-2.162-1.707-4.04-3.098-5.548zM82.72 82.643a11.84 11.84 0 0 1-1.826 1.572h-.002c-1.8 1.266-3.888 2.22-6.106 3.04l1.654 5.244c2.426-.897 4.917-1.997 7.245-3.635l.006-.005l.003-.002a16.95 16.95 0 0 0 2.639-2.287zm-12.64 6.089c-3.213.864-6.497 1.522-9.821 2.08l.784 5.479c3.421-.575 6.856-1.262 10.27-2.18zm-14.822 2.836c-3.346.457-6.71.83-10.084 1.148l.442 5.522c3.426-.322 6.858-.701 10.285-1.17zm-15.155 1.583c-3.381.268-6.77.486-10.162.67l.256 5.536c3.425-.185 6.853-.406 10.28-.678zm-15.259.92c-2.033.095-4.071.173-6.114.245l.168 5.541a560.1 560.1 0 0 0 6.166-.246z"
                fill="currentColor" stroke="currentColor" fill-rule="evenodd"></path>
            </g>
          </svg>
      `),
        text: "Rutas"
      },
      {
        id: "lineasIcono",
        href: "https://dosxdos.app.iidos.com/lineas.html",
        icon: renderIcon(`
                 <svg class="w-6 h-6 mr-2" fill="currentColor" stroke="none" viewBox="-3 0 32 32" version="1.1"
            xmlns="http://www.w3.org/2000/svg">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
              <path
                d="M23.36 9.32c-1.32 0-2.36 1.080-2.36 2.36 0 0.28 0.040 0.56 0.12 0.8l-4.8 4.080c-0.32-0.2-0.72-0.28-1.16-0.28s-0.88 0.12-1.24 0.36l-2.72-2.2c0.080-0.24 0.12-0.44 0.12-0.72 0-1.32-1.080-2.36-2.36-2.36-1.32 0-2.36 1.080-2.36 2.36 0 0.36 0.080 0.68 0.2 0.96l-3.44 3.44c-0.28-0.12-0.64-0.2-0.96-0.2-1.32 0-2.36 1.080-2.36 2.36 0 1.32 1.080 2.36 2.36 2.36s2.36-1.080 2.36-2.36c0-0.36-0.080-0.68-0.2-0.96l3.44-3.44c0.28 0.12 0.64 0.2 0.96 0.2 0.44 0 0.88-0.12 1.24-0.36l2.76 2.12c-0.080 0.24-0.080 0.44-0.080 0.72 0 1.32 1.080 2.36 2.36 2.36s2.36-1.080 2.36-2.36c0-0.28-0.040-0.56-0.12-0.8l4.8-4.080c0.32 0.2 0.72 0.28 1.16 0.28 1.32 0 2.36-1.080 2.36-2.36-0.040-1.2-1.16-2.28-2.44-2.28zM2.36 21c-0.36 0-0.68-0.32-0.68-0.68 0-0.4 0.32-0.68 0.68-0.68s0.68 0.32 0.68 0.68c0 0.36-0.28 0.68-0.68 0.68zM8.24 13.76c0-0.4 0.32-0.68 0.68-0.68s0.68 0.32 0.68 0.68-0.32 0.68-0.68 0.68c-0.36 0-0.68-0.32-0.68-0.68zM15.2 19.28c-0.4 0-0.68-0.32-0.68-0.68s0.32-0.68 0.68-0.68 0.68 0.32 0.68 0.68c-0.040 0.4-0.28 0.68-0.68 0.68zM23.36 12.36c-0.36 0-0.68-0.32-0.68-0.68 0-0.4 0.32-0.68 0.68-0.68 0.4 0 0.68 0.32 0.68 0.68 0 0.4-0.32 0.68-0.68 0.68z">
              </path>
            </g>
          </svg>
      `),
        text: "Líneas Ruta"
      },
      {
        id: "gastos",
        text: "Gastos",
        icon: renderIcon(`
        <path d="M5 16C5 15.4477 5.44772 15 6 15H8C8.55229 15 9 15.4477 9 16C9 16.5523 8.55229 17 8 17H6C5.44772 17 5 16.5523 5 16Z" />
        <path d="M11 15C10.4477 15 10 15.4477 10 16C10 16.5523 10.4477 17 11 17H12C12.5523 17 13 16.5523 13 16C13 15.4477 12.5523 15 12 15H11Z" />
        <rect x="2" y="4" width="20" height="16" rx="2" />
      `),
        submenu: [
          {
            href: "https://dosxdos.app.iidos.com/gastos_rutas.html",
            text: "Gastos Rutas"
          },
          {
            href: "",
            text: "Reportar Gastos (próximamente)"
          }
        ]
      },
      {
        id: "clientes",
        text: "Clientes",
        icon: renderIcon(`
                  <svg viewBox="0 0 28 28" class="w-6 h-6 mr-2" fill="currentColor" stroke="none"
            xmlns="http://www.w3.org/2000/svg">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
              <path clip-rule="evenodd"
                d="M1.82047 1C1.36734 1 1 1.35728 1 1.79801V2.39948C1 2.84021 1.36734 3.19749 1.82047 3.19749H3.72716C4.03867 3.19749 4.3233 3.36906 4.46192 3.64038L5.4947 5.93251C5.53326 6.00798 5.56364 6.09443 5.62081 6.15194L10.057 16.4429C10.0129 16.4634 9.97056 16.4883 9.93075 16.5176C8.70163 17.4226 7.87009 18.5878 7.87001 19.7604C7.86996 20.4429 8.16289 21.0807 8.75002 21.5212C9.30752 21.9394 10.0364 22.1118 10.8189 22.1118H10.8446C10.336 22.6308 10.0238 23.3336 10.0238 24.1072C10.0238 25.7049 11.3554 27 12.998 27C14.6406 27 15.9722 25.7049 15.9722 24.1072C15.9722 23.3336 15.66 22.6308 15.1513 22.1118H19.0494C18.5408 22.6308 18.2285 23.3336 18.2285 24.1072C18.2285 25.7049 19.5601 27 21.2027 27C22.8454 27 24.177 25.7049 24.177 24.1072C24.177 23.3336 23.8647 22.6308 23.3561 22.1118H23.9718C24.425 22.1118 24.7923 21.7545 24.7923 21.3138V20.9148C24.7923 20.474 24.425 20.1167 23.9718 20.1167H10.8189C10.3192 20.1167 10.0864 20.0041 10.0028 19.9414C9.94878 19.9009 9.92119 19.8618 9.9212 19.7606C9.92122 19.4917 10.1711 18.8708 11.069 18.1827C11.1084 18.1524 11.1453 18.1194 11.1792 18.084C11.2692 18.1089 11.3635 18.1221 11.4601 18.1221H23.9235C24.4248 18.1221 24.8527 17.7696 24.9351 17.2885L26.9858 5.31837C27.09 4.71036 26.6079 4.1569 25.9742 4.1569H7.35431C7.1981 4.1569 7.05618 4.06597 6.9909 3.92405L5.84968 1.44289C5.71106 1.17157 5.42642 1 5.11492 1H1.82047ZM8.47667 6.15194C8.18952 6.15194 7.99591 6.44552 8.10899 6.70946L12.04 15.8846C12.103 16.0317 12.2476 16.1271 12.4076 16.1271H22.7173C22.9122 16.1271 23.0787 15.9867 23.1116 15.7946L24.6834 6.61948C24.7253 6.37513 24.5371 6.15194 24.2892 6.15194H8.47667ZM11.8698 24.1072C11.8698 23.5012 12.3749 23.0099 12.998 23.0099C13.621 23.0099 14.1261 23.5012 14.1261 24.1072C14.1261 24.7132 13.621 25.2045 12.998 25.2045C12.3749 25.2045 11.8698 24.7132 11.8698 24.1072ZM21.2027 23.0099C20.5797 23.0099 20.0746 23.5012 20.0746 24.1072C20.0746 24.7132 20.5797 25.2045 21.2027 25.2045C21.8258 25.2045 22.3309 24.7132 22.3309 24.1072C22.3309 23.5012 21.8258 23.0099 21.2027 23.0099Z"
                fill-rule="evenodd"></path>
            </g>
          </svg>
      `),
        submenu: [
          {
            href: "https://dosxdos.app.iidos.com/ots_clientes.html",
            text: "Restricciones Tipos de OT"
          },
          {
            href: "https://dosxdos.app.iidos.com/firmas_clientes.html",
            text: "Restricciones Firmas"
          }
        ]
      },
      {
        id: "usuarios",
        href: "https://dosxdos.app.iidos.com/dosxdos.php?modulo=usuarios",
        icon: renderIcon(`
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
        <circle cx="9" cy="7" r="4" />
        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
      `),
        text: "Usuarios"
      },
      {
        id: "sincronizacion",
        text: "Sincronización",
        icon: renderIcon(`
        <path d="M21 2v6h-6" />
        <path d="M3 12a9 9 0 0 1 15-6.7L21 8" />
        <path d="M3 22v-6h6" />
        <path d="M21 12a9 9 0 0 1-15 6.7L3 16" />
      `),
        // submenu: [
        //   {
        //     href: "#",
        //     text: "Sinc. OT Navision"
        //   },
        //   {
        //     href: "https://dosxdos.app.iidos.com/apirest/sincronizador2.php",
        //     text: "Sinc. Total OT Navision"
        //   }
        // ]
      }
    ],
    oficina: [
      {
        id: "archivos",
        href: "https://dosxdos.app.iidos.com/ot.html",
        icon: renderIcon(`
          <path d="M12.37 8.87988H17.62" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M6.38 8.87988L7.13 9.62988L9.38 7.37988" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M12.37 15.8799H17.62" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M6.38 15.8799L7.13 16.6299L9.38 14.3799" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M9 22H15C20 22 22 20 22 15V9C22 4 20 2 15 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22Z" stroke-linecap="round" stroke-linejoin="round"></path>
        `), text: "OT"
      },
      {
        id: "icLineasOt",
        href: "https://dosxdos.app.iidos.com/lineas_ot.html",
        icon: renderIcon(`
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
            <polyline points="14 2 14 8 20 8" />
            <path d="M9 15l2 2 4-4" />
          `),
        text: "Líneas OT"
      },
      {
        id: "rutasIcono",
        href: "https://dosxdos.app.iidos.com/rutas.html",
        icon: renderIcon(`
          <svg viewBox="0 0 100 100" class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
          xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
          role="img" preserveAspectRatio="xMidYMid meet">
          <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
          <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
          <g id="SVGRepo_iconCarrier">
            <path
              d="M21 32C9.459 32 0 41.43 0 52.94c0 4.46 1.424 8.605 3.835 12.012l14.603 25.244c2.045 2.672 3.405 2.165 5.106-.14l16.106-27.41c.325-.59.58-1.216.803-1.856A20.668 20.668 0 0 0 42 52.94C42 41.43 32.544 32 21 32zm0 9.812c6.216 0 11.16 4.931 11.16 11.129c0 6.198-4.944 11.127-11.16 11.127c-6.215 0-11.16-4.93-11.16-11.127c0-6.198 4.945-11.129 11.16-11.129z"
              fill="currentColor" stroke="currentColor"></path>
            <path
              d="M87.75 0C81.018 0 75.5 5.501 75.5 12.216c0 2.601.83 5.019 2.237 7.006l8.519 14.726c1.193 1.558 1.986 1.262 2.978-.082l9.395-15.99c.19-.343.339-.708.468-1.082a12.05 12.05 0 0 0 .903-4.578C100 5.5 94.484 0 87.75 0zm0 5.724c3.626 0 6.51 2.876 6.51 6.492c0 3.615-2.884 6.49-6.51 6.49c-3.625 0-6.51-2.875-6.51-6.49c0-3.616 2.885-6.492 6.51-6.492z"
              fill="currentColor" stroke="currentColor"></path>
            <path
              d="M88.209 37.412c-2.247.05-4.5.145-6.757.312l.348 5.532a126.32 126.32 0 0 1 6.513-.303zm-11.975.82c-3.47.431-6.97 1.045-10.43 2.032l1.303 5.361c3.144-.896 6.402-1.475 9.711-1.886zM60.623 42.12a24.52 24.52 0 0 0-3.004 1.583l-.004.005l-.006.002c-1.375.866-2.824 1.965-4.007 3.562c-.857 1.157-1.558 2.62-1.722 4.35l5.095.565c.038-.406.246-.942.62-1.446h.002v-.002c.603-.816 1.507-1.557 2.582-2.235l.004-.002a19.64 19.64 0 0 1 2.388-1.256zM58 54.655l-3.303 4.235c.783.716 1.604 1.266 2.397 1.726l.01.005l.01.006c2.632 1.497 5.346 2.342 7.862 3.144l1.446-5.318c-2.515-.802-4.886-1.576-6.918-2.73c-.582-.338-1.092-.691-1.504-1.068zm13.335 5.294l-1.412 5.327l.668.208l.82.262c2.714.883 5.314 1.826 7.638 3.131l2.358-4.92c-2.81-1.579-5.727-2.611-8.538-3.525l-.008-.002l-.842-.269zm14.867 7.7l-3.623 3.92c.856.927 1.497 2.042 1.809 3.194l.002.006l.002.009c.372 1.345.373 2.927.082 4.525l5.024 1.072c.41-2.256.476-4.733-.198-7.178c-.587-2.162-1.707-4.04-3.098-5.548zM82.72 82.643a11.84 11.84 0 0 1-1.826 1.572h-.002c-1.8 1.266-3.888 2.22-6.106 3.04l1.654 5.244c2.426-.897 4.917-1.997 7.245-3.635l.006-.005l.003-.002a16.95 16.95 0 0 0 2.639-2.287zm-12.64 6.089c-3.213.864-6.497 1.522-9.821 2.08l.784 5.479c3.421-.575 6.856-1.262 10.27-2.18zm-14.822 2.836c-3.346.457-6.71.83-10.084 1.148l.442 5.522c3.426-.322 6.858-.701 10.285-1.17zm-15.155 1.583c-3.381.268-6.77.486-10.162.67l.256 5.536c3.425-.185 6.853-.406 10.28-.678zm-15.259.92c-2.033.095-4.071.173-6.114.245l.168 5.541a560.1 560.1 0 0 0 6.166-.246z"
              fill="currentColor" stroke="currentColor" fill-rule="evenodd"></path>
          </g>
        </svg>
    `), text: "Rutas"
      },
      {
        id: "lineasIcono",
        href: "https://dosxdos.app.iidos.com/lineas.html",
        icon: renderIcon(`<svg class="w-6 h-6 mr-2" fill="currentColor" stroke="none" viewBox="-3 0 32 32" version="1.1"
            xmlns="http://www.w3.org/2000/svg">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
              <path
                d="M23.36 9.32c-1.32 0-2.36 1.080-2.36 2.36 0 0.28 0.040 0.56 0.12 0.8l-4.8 4.080c-0.32-0.2-0.72-0.28-1.16-0.28s-0.88 0.12-1.24 0.36l-2.72-2.2c0.080-0.24 0.12-0.44 0.12-0.72 0-1.32-1.080-2.36-2.36-2.36-1.32 0-2.36 1.080-2.36 2.36 0 0.36 0.080 0.68 0.2 0.96l-3.44 3.44c-0.28-0.12-0.64-0.2-0.96-0.2-1.32 0-2.36 1.080-2.36 2.36 0 1.32 1.080 2.36 2.36 2.36s2.36-1.080 2.36-2.36c0-0.36-0.080-0.68-0.2-0.96l3.44-3.44c0.28 0.12 0.64 0.2 0.96 0.2 0.44 0 0.88-0.12 1.24-0.36l2.76 2.12c-0.080 0.24-0.080 0.44-0.080 0.72 0 1.32 1.080 2.36 2.36 2.36s2.36-1.080 2.36-2.36c0-0.28-0.040-0.56-0.12-0.8l4.8-4.080c0.32 0.2 0.72 0.28 1.16 0.28 1.32 0 2.36-1.080 2.36-2.36-0.040-1.2-1.16-2.28-2.44-2.28zM2.36 21c-0.36 0-0.68-0.32-0.68-0.68 0-0.4 0.32-0.68 0.68-0.68s0.68 0.32 0.68 0.68c0 0.36-0.28 0.68-0.68 0.68zM8.24 13.76c0-0.4 0.32-0.68 0.68-0.68s0.68 0.32 0.68 0.68-0.32 0.68-0.68 0.68c-0.36 0-0.68-0.32-0.68-0.68zM15.2 19.28c-0.4 0-0.68-0.32-0.68-0.68s0.32-0.68 0.68-0.68 0.68 0.32 0.68 0.68c-0.040 0.4-0.28 0.68-0.68 0.68zM23.36 12.36c-0.36 0-0.68-0.32-0.68-0.68 0-0.4 0.32-0.68 0.68-0.68 0.4 0 0.68 0.32 0.68 0.68 0 0.4-0.32 0.68-0.68 0.68z">
              </path>
            </g>
          </svg>`),
        text: "Líneas Ruta"
      },
      {
        id: "gastos",
        text: "Gastos",
        icon: renderIcon(`
          <path d="M5 16C5 15.4477 5.44772 15 6 15H8C8.55229 15 9 15.4477 9 16C9 16.5523 8.55229 17 8 17H6C5.44772 17 5 16.5523 5 16Z" />
          <path d="M11 15C10.4477 15 10 15.4477 10 16C10 16.5523 10.4477 17 11 17H12C12.5523 17 13 16.5523 13 16C13 15.4477 12.5523 15 12 15H11Z" />
          <rect x="2" y="4" width="20" height="16" rx="2" />
        `), submenu: [
          {
            href: "https://dosxdos.app.iidos.com/gastos_rutas.html",
            text: "Gastos Rutas"
          },
          {
            href: "",
            text: "Reportar Gastos (próximamente)"
          }
        ]
      }
    ],
    diseno: [
      {
        id: "archivos",
        href: "https://dosxdos.app.iidos.com/ot.html",
        icon: renderIcon(`
          <path d="M12.37 8.87988H17.62" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M6.38 8.87988L7.13 9.62988L9.38 7.37988" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M12.37 15.8799H17.62" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M6.38 15.8799L7.13 16.6299L9.38 14.3799" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M9 22H15C20 22 22 20 22 15V9C22 4 20 2 15 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22Z" stroke-linecap="round" stroke-linejoin="round"></path>
        `), text: "OT"
      },
      {
        id: "icLineasOt",
        href: "https://dosxdos.app.iidos.com/lineas_ot.html",
        icon: renderIcon(`
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
          <polyline points="14 2 14 8 20 8" />
          <path d="M9 15l2 2 4-4" />
        `), text: "Líneas OT"
      },
      {
        id: "rutasIcono",
        href: "https://dosxdos.app.iidos.com/rutas.html",
        icon: renderIcon(`
          <svg viewBox="0 0 100 100" class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
          xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
          role="img" preserveAspectRatio="xMidYMid meet">
          <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
          <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
          <g id="SVGRepo_iconCarrier">
            <path
              d="M21 32C9.459 32 0 41.43 0 52.94c0 4.46 1.424 8.605 3.835 12.012l14.603 25.244c2.045 2.672 3.405 2.165 5.106-.14l16.106-27.41c.325-.59.58-1.216.803-1.856A20.668 20.668 0 0 0 42 52.94C42 41.43 32.544 32 21 32zm0 9.812c6.216 0 11.16 4.931 11.16 11.129c0 6.198-4.944 11.127-11.16 11.127c-6.215 0-11.16-4.93-11.16-11.127c0-6.198 4.945-11.129 11.16-11.129z"
              fill="currentColor" stroke="currentColor"></path>
            <path
              d="M87.75 0C81.018 0 75.5 5.501 75.5 12.216c0 2.601.83 5.019 2.237 7.006l8.519 14.726c1.193 1.558 1.986 1.262 2.978-.082l9.395-15.99c.19-.343.339-.708.468-1.082a12.05 12.05 0 0 0 .903-4.578C100 5.5 94.484 0 87.75 0zm0 5.724c3.626 0 6.51 2.876 6.51 6.492c0 3.615-2.884 6.49-6.51 6.49c-3.625 0-6.51-2.875-6.51-6.49c0-3.616 2.885-6.492 6.51-6.492z"
              fill="currentColor" stroke="currentColor"></path>
            <path
              d="M88.209 37.412c-2.247.05-4.5.145-6.757.312l.348 5.532a126.32 126.32 0 0 1 6.513-.303zm-11.975.82c-3.47.431-6.97 1.045-10.43 2.032l1.303 5.361c3.144-.896 6.402-1.475 9.711-1.886zM60.623 42.12a24.52 24.52 0 0 0-3.004 1.583l-.004.005l-.006.002c-1.375.866-2.824 1.965-4.007 3.562c-.857 1.157-1.558 2.62-1.722 4.35l5.095.565c.038-.406.246-.942.62-1.446h.002v-.002c.603-.816 1.507-1.557 2.582-2.235l.004-.002a19.64 19.64 0 0 1 2.388-1.256zM58 54.655l-3.303 4.235c.783.716 1.604 1.266 2.397 1.726l.01.005l.01.006c2.632 1.497 5.346 2.342 7.862 3.144l1.446-5.318c-2.515-.802-4.886-1.576-6.918-2.73c-.582-.338-1.092-.691-1.504-1.068zm13.335 5.294l-1.412 5.327l.668.208l.82.262c2.714.883 5.314 1.826 7.638 3.131l2.358-4.92c-2.81-1.579-5.727-2.611-8.538-3.525l-.008-.002l-.842-.269zm14.867 7.7l-3.623 3.92c.856.927 1.497 2.042 1.809 3.194l.002.006l.002.009c.372 1.345.373 2.927.082 4.525l5.024 1.072c.41-2.256.476-4.733-.198-7.178c-.587-2.162-1.707-4.04-3.098-5.548zM82.72 82.643a11.84 11.84 0 0 1-1.826 1.572h-.002c-1.8 1.266-3.888 2.22-6.106 3.04l1.654 5.244c2.426-.897 4.917-1.997 7.245-3.635l.006-.005l.003-.002a16.95 16.95 0 0 0 2.639-2.287zm-12.64 6.089c-3.213.864-6.497 1.522-9.821 2.08l.784 5.479c3.421-.575 6.856-1.262 10.27-2.18zm-14.822 2.836c-3.346.457-6.71.83-10.084 1.148l.442 5.522c3.426-.322 6.858-.701 10.285-1.17zm-15.155 1.583c-3.381.268-6.77.486-10.162.67l.256 5.536c3.425-.185 6.853-.406 10.28-.678zm-15.259.92c-2.033.095-4.071.173-6.114.245l.168 5.541a560.1 560.1 0 0 0 6.166-.246z"
              fill="currentColor" stroke="currentColor" fill-rule="evenodd"></path>
          </g>
        </svg>
    `), text: "Rutas"
      },
      {
        id: "lineasIcono",
        href: "https://dosxdos.app.iidos.com/lineas.html",
        icon: renderIcon(`
          <svg class="w-6 h-6 mr-2" fill="currentColor" stroke="none" viewBox="-3 0 32 32" version="1.1"
     xmlns="http://www.w3.org/2000/svg">
     <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
     <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
     <g id="SVGRepo_iconCarrier">
       <path
         d="M23.36 9.32c-1.32 0-2.36 1.080-2.36 2.36 0 0.28 0.040 0.56 0.12 0.8l-4.8 4.080c-0.32-0.2-0.72-0.28-1.16-0.28s-0.88 0.12-1.24 0.36l-2.72-2.2c0.080-0.24 0.12-0.44 0.12-0.72 0-1.32-1.080-2.36-2.36-2.36-1.32 0-2.36 1.080-2.36 2.36 0 0.36 0.080 0.68 0.2 0.96l-3.44 3.44c-0.28-0.12-0.64-0.2-0.96-0.2-1.32 0-2.36 1.080-2.36 2.36 0 1.32 1.080 2.36 2.36 2.36s2.36-1.080 2.36-2.36c0-0.36-0.080-0.68-0.2-0.96l3.44-3.44c0.28 0.12 0.64 0.2 0.96 0.2 0.44 0 0.88-0.12 1.24-0.36l2.76 2.12c-0.080 0.24-0.080 0.44-0.080 0.72 0 1.32 1.080 2.36 2.36 2.36s2.36-1.080 2.36-2.36c0-0.28-0.040-0.56-0.12-0.8l4.8-4.080c0.32 0.2 0.72 0.28 1.16 0.28 1.32 0 2.36-1.080 2.36-2.36-0.040-1.2-1.16-2.28-2.44-2.28zM2.36 21c-0.36 0-0.68-0.32-0.68-0.68 0-0.4 0.32-0.68 0.68-0.68s0.68 0.32 0.68 0.68c0 0.36-0.28 0.68-0.68 0.68zM8.24 13.76c0-0.4 0.32-0.68 0.68-0.68s0.68 0.32 0.68 0.68-0.32 0.68-0.68 0.68c-0.36 0-0.68-0.32-0.68-0.68zM15.2 19.28c-0.4 0-0.68-0.32-0.68-0.68s0.32-0.68 0.68-0.68 0.68 0.32 0.68 0.68c-0.040 0.4-0.28 0.68-0.68 0.68zM23.36 12.36c-0.36 0-0.68-0.32-0.68-0.68 0-0.4 0.32-0.68 0.68-0.68 0.4 0 0.68 0.32 0.68 0.68 0 0.4-0.32 0.68-0.68 0.68z">
       </path>
     </g>
   </svg>
`), text: "Líneas Ruta"
      },
      {
        id: "gastos",
        text: "Gastos",
        icon: renderIcon(`
          <path d="M5 16C5 15.4477 5.44772 15 6 15H8C8.55229 15 9 15.4477 9 16C9 16.5523 8.55229 17 8 17H6C5.44772 17 5 16.5523 5 16Z" />
          <path d="M11 15C10.4477 15 10 15.4477 10 16C10 16.5523 10.4477 17 11 17H12C12.5523 17 13 16.5523 13 16C13 15.4477 12.5523 15 12 15H11Z" />
          <rect x="2" y="4" width="20" height="16" rx="2" />
        `), submenu: [
          {
            href: "https://dosxdos.app.iidos.com/gastos_rutas.html",
            text: "Gastos Rutas"
          },
          {
            href: "",
            text: "Reportar Gastos (próximamente)"
          }
        ]
      }
    ],
    estudio: [
      {
        id: "archivos",
        href: "https://dosxdos.app.iidos.com/ot.html",
        icon: renderIcon(`
          <path d="M12.37 8.87988H17.62" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M6.38 8.87988L7.13 9.62988L9.38 7.37988" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M12.37 15.8799H17.62" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M6.38 15.8799L7.13 16.6299L9.38 14.3799" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M9 22H15C20 22 22 20 22 15V9C22 4 20 2 15 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22Z" stroke-linecap="round" stroke-linejoin="round"></path>
        `), text: "OT"
      },
      {
        id: "icLineasOt",
        href: "https://dosxdos.app.iidos.com/lineas_ot.html",
        icon: renderIcon(`
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
          <polyline points="14 2 14 8 20 8" />
          <path d="M9 15l2 2 4-4" />
        `), text: "Líneas OT"
      },
      {
        id: "rutasIcono",
        href: "https://dosxdos.app.iidos.com/rutas.html",
        icon: renderIcon(`
          <svg viewBox="0 0 100 100" class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
          xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
          role="img" preserveAspectRatio="xMidYMid meet">
          <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
          <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
          <g id="SVGRepo_iconCarrier">
            <path
              d="M21 32C9.459 32 0 41.43 0 52.94c0 4.46 1.424 8.605 3.835 12.012l14.603 25.244c2.045 2.672 3.405 2.165 5.106-.14l16.106-27.41c.325-.59.58-1.216.803-1.856A20.668 20.668 0 0 0 42 52.94C42 41.43 32.544 32 21 32zm0 9.812c6.216 0 11.16 4.931 11.16 11.129c0 6.198-4.944 11.127-11.16 11.127c-6.215 0-11.16-4.93-11.16-11.127c0-6.198 4.945-11.129 11.16-11.129z"
              fill="currentColor" stroke="currentColor"></path>
            <path
              d="M87.75 0C81.018 0 75.5 5.501 75.5 12.216c0 2.601.83 5.019 2.237 7.006l8.519 14.726c1.193 1.558 1.986 1.262 2.978-.082l9.395-15.99c.19-.343.339-.708.468-1.082a12.05 12.05 0 0 0 .903-4.578C100 5.5 94.484 0 87.75 0zm0 5.724c3.626 0 6.51 2.876 6.51 6.492c0 3.615-2.884 6.49-6.51 6.49c-3.625 0-6.51-2.875-6.51-6.49c0-3.616 2.885-6.492 6.51-6.492z"
              fill="currentColor" stroke="currentColor"></path>
            <path
              d="M88.209 37.412c-2.247.05-4.5.145-6.757.312l.348 5.532a126.32 126.32 0 0 1 6.513-.303zm-11.975.82c-3.47.431-6.97 1.045-10.43 2.032l1.303 5.361c3.144-.896 6.402-1.475 9.711-1.886zM60.623 42.12a24.52 24.52 0 0 0-3.004 1.583l-.004.005l-.006.002c-1.375.866-2.824 1.965-4.007 3.562c-.857 1.157-1.558 2.62-1.722 4.35l5.095.565c.038-.406.246-.942.62-1.446h.002v-.002c.603-.816 1.507-1.557 2.582-2.235l.004-.002a19.64 19.64 0 0 1 2.388-1.256zM58 54.655l-3.303 4.235c.783.716 1.604 1.266 2.397 1.726l.01.005l.01.006c2.632 1.497 5.346 2.342 7.862 3.144l1.446-5.318c-2.515-.802-4.886-1.576-6.918-2.73c-.582-.338-1.092-.691-1.504-1.068zm13.335 5.294l-1.412 5.327l.668.208l.82.262c2.714.883 5.314 1.826 7.638 3.131l2.358-4.92c-2.81-1.579-5.727-2.611-8.538-3.525l-.008-.002l-.842-.269zm14.867 7.7l-3.623 3.92c.856.927 1.497 2.042 1.809 3.194l.002.006l.002.009c.372 1.345.373 2.927.082 4.525l5.024 1.072c.41-2.256.476-4.733-.198-7.178c-.587-2.162-1.707-4.04-3.098-5.548zM82.72 82.643a11.84 11.84 0 0 1-1.826 1.572h-.002c-1.8 1.266-3.888 2.22-6.106 3.04l1.654 5.244c2.426-.897 4.917-1.997 7.245-3.635l.006-.005l.003-.002a16.95 16.95 0 0 0 2.639-2.287zm-12.64 6.089c-3.213.864-6.497 1.522-9.821 2.08l.784 5.479c3.421-.575 6.856-1.262 10.27-2.18zm-14.822 2.836c-3.346.457-6.71.83-10.084 1.148l.442 5.522c3.426-.322 6.858-.701 10.285-1.17zm-15.155 1.583c-3.381.268-6.77.486-10.162.67l.256 5.536c3.425-.185 6.853-.406 10.28-.678zm-15.259.92c-2.033.095-4.071.173-6.114.245l.168 5.541a560.1 560.1 0 0 0 6.166-.246z"
              fill="currentColor" stroke="currentColor" fill-rule="evenodd"></path>
          </g>
        </svg>
    `), text: "Rutas"
      },
      {
        id: "lineasIcono",
        href: "https://dosxdos.app.iidos.com/lineas.html",
        icon: renderIcon(`
          <svg class="w-6 h-6 mr-2" fill="currentColor" stroke="none" viewBox="-3 0 32 32" version="1.1"
     xmlns="http://www.w3.org/2000/svg">
     <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
     <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
     <g id="SVGRepo_iconCarrier">
       <path
         d="M23.36 9.32c-1.32 0-2.36 1.080-2.36 2.36 0 0.28 0.040 0.56 0.12 0.8l-4.8 4.080c-0.32-0.2-0.72-0.28-1.16-0.28s-0.88 0.12-1.24 0.36l-2.72-2.2c0.080-0.24 0.12-0.44 0.12-0.72 0-1.32-1.080-2.36-2.36-2.36-1.32 0-2.36 1.080-2.36 2.36 0 0.36 0.080 0.68 0.2 0.96l-3.44 3.44c-0.28-0.12-0.64-0.2-0.96-0.2-1.32 0-2.36 1.080-2.36 2.36 0 1.32 1.080 2.36 2.36 2.36s2.36-1.080 2.36-2.36c0-0.36-0.080-0.68-0.2-0.96l3.44-3.44c0.28 0.12 0.64 0.2 0.96 0.2 0.44 0 0.88-0.12 1.24-0.36l2.76 2.12c-0.080 0.24-0.080 0.44-0.080 0.72 0 1.32 1.080 2.36 2.36 2.36s2.36-1.080 2.36-2.36c0-0.28-0.040-0.56-0.12-0.8l4.8-4.080c0.32 0.2 0.72 0.28 1.16 0.28 1.32 0 2.36-1.080 2.36-2.36-0.040-1.2-1.16-2.28-2.44-2.28zM2.36 21c-0.36 0-0.68-0.32-0.68-0.68 0-0.4 0.32-0.68 0.68-0.68s0.68 0.32 0.68 0.68c0 0.36-0.28 0.68-0.68 0.68zM8.24 13.76c0-0.4 0.32-0.68 0.68-0.68s0.68 0.32 0.68 0.68-0.32 0.68-0.68 0.68c-0.36 0-0.68-0.32-0.68-0.68zM15.2 19.28c-0.4 0-0.68-0.32-0.68-0.68s0.32-0.68 0.68-0.68 0.68 0.32 0.68 0.68c-0.040 0.4-0.28 0.68-0.68 0.68zM23.36 12.36c-0.36 0-0.68-0.32-0.68-0.68 0-0.4 0.32-0.68 0.68-0.68 0.4 0 0.68 0.32 0.68 0.68 0 0.4-0.32 0.68-0.68 0.68z">
       </path>
     </g>
   </svg>
`), text: "Líneas Ruta"
      },
      {
        id: "gastos",
        text: "Gastos",
        icon: renderIcon(`
          <path d="M5 16C5 15.4477 5.44772 15 6 15H8C8.55229 15 9 15.4477 9 16C9 16.5523 8.55229 17 8 17H6C5.44772 17 5 16.5523 5 16Z" />
          <path d="M11 15C10.4477 15 10 15.4477 10 16C10 16.5523 10.4477 17 11 17H12C12.5523 17 13 16.5523 13 16C13 15.4477 12.5523 15 12 15H11Z" />
          <rect x="2" y="4" width="20" height="16" rx="2" />
        `), submenu: [
          {
            href: "https://dosxdos.app.iidos.com/gastos_rutas.html",
            text: "Gastos Rutas"
          },
          {
            href: "",
            text: "Reportar Gastos (próximamente)"
          }
        ]
      }
    ],
    montador: [
      {
        id: "rutasMontador",
        href: "https://dosxdos.app.iidos.com/rutas_montador.html",
        icon: renderIcon(`
          <svg viewBox="0 0 100 100" class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
          xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
          role="img" preserveAspectRatio="xMidYMid meet">
          <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
          <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
          <g id="SVGRepo_iconCarrier">
            <path
              d="M21 32C9.459 32 0 41.43 0 52.94c0 4.46 1.424 8.605 3.835 12.012l14.603 25.244c2.045 2.672 3.405 2.165 5.106-.14l16.106-27.41c.325-.59.58-1.216.803-1.856A20.668 20.668 0 0 0 42 52.94C42 41.43 32.544 32 21 32zm0 9.812c6.216 0 11.16 4.931 11.16 11.129c0 6.198-4.944 11.127-11.16 11.127c-6.215 0-11.16-4.93-11.16-11.127c0-6.198 4.945-11.129 11.16-11.129z"
              fill="currentColor" stroke="currentColor"></path>
            <path
              d="M87.75 0C81.018 0 75.5 5.501 75.5 12.216c0 2.601.83 5.019 2.237 7.006l8.519 14.726c1.193 1.558 1.986 1.262 2.978-.082l9.395-15.99c.19-.343.339-.708.468-1.082a12.05 12.05 0 0 0 .903-4.578C100 5.5 94.484 0 87.75 0zm0 5.724c3.626 0 6.51 2.876 6.51 6.492c0 3.615-2.884 6.49-6.51 6.49c-3.625 0-6.51-2.875-6.51-6.49c0-3.616 2.885-6.492 6.51-6.492z"
              fill="currentColor" stroke="currentColor"></path>
            <path
              d="M88.209 37.412c-2.247.05-4.5.145-6.757.312l.348 5.532a126.32 126.32 0 0 1 6.513-.303zm-11.975.82c-3.47.431-6.97 1.045-10.43 2.032l1.303 5.361c3.144-.896 6.402-1.475 9.711-1.886zM60.623 42.12a24.52 24.52 0 0 0-3.004 1.583l-.004.005l-.006.002c-1.375.866-2.824 1.965-4.007 3.562c-.857 1.157-1.558 2.62-1.722 4.35l5.095.565c.038-.406.246-.942.62-1.446h.002v-.002c.603-.816 1.507-1.557 2.582-2.235l.004-.002a19.64 19.64 0 0 1 2.388-1.256zM58 54.655l-3.303 4.235c.783.716 1.604 1.266 2.397 1.726l.01.005l.01.006c2.632 1.497 5.346 2.342 7.862 3.144l1.446-5.318c-2.515-.802-4.886-1.576-6.918-2.73c-.582-.338-1.092-.691-1.504-1.068zm13.335 5.294l-1.412 5.327l.668.208l.82.262c2.714.883 5.314 1.826 7.638 3.131l2.358-4.92c-2.81-1.579-5.727-2.611-8.538-3.525l-.008-.002l-.842-.269zm14.867 7.7l-3.623 3.92c.856.927 1.497 2.042 1.809 3.194l.002.006l.002.009c.372 1.345.373 2.927.082 4.525l5.024 1.072c.41-2.256.476-4.733-.198-7.178c-.587-2.162-1.707-4.04-3.098-5.548zM82.72 82.643a11.84 11.84 0 0 1-1.826 1.572h-.002c-1.8 1.266-3.888 2.22-6.106 3.04l1.654 5.244c2.426-.897 4.917-1.997 7.245-3.635l.006-.005l.003-.002a16.95 16.95 0 0 0 2.639-2.287zm-12.64 6.089c-3.213.864-6.497 1.522-9.821 2.08l.784 5.479c3.421-.575 6.856-1.262 10.27-2.18zm-14.822 2.836c-3.346.457-6.71.83-10.084 1.148l.442 5.522c3.426-.322 6.858-.701 10.285-1.17zm-15.155 1.583c-3.381.268-6.77.486-10.162.67l.256 5.536c3.425-.185 6.853-.406 10.28-.678zm-15.259.92c-2.033.095-4.071.173-6.114.245l.168 5.541a560.1 560.1 0 0 0 6.166-.246z"
              fill="currentColor" stroke="currentColor" fill-rule="evenodd"></path>
          </g>
        </svg>
    `), text: "Rutas"
      },
      {
        id: "lineasMontador",
        href: "https://dosxdos.app.iidos.com/ruta_montador.html",
        icon: renderIcon(`
          <svg class="w-6 h-6 mr-2" fill="currentColor" stroke="none" viewBox="-3 0 32 32" version="1.1"
     xmlns="http://www.w3.org/2000/svg">
     <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
     <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
     <g id="SVGRepo_iconCarrier">
       <path
         d="M23.36 9.32c-1.32 0-2.36 1.080-2.36 2.36 0 0.28 0.040 0.56 0.12 0.8l-4.8 4.080c-0.32-0.2-0.72-0.28-1.16-0.28s-0.88 0.12-1.24 0.36l-2.72-2.2c0.080-0.24 0.12-0.44 0.12-0.72 0-1.32-1.080-2.36-2.36-2.36-1.32 0-2.36 1.080-2.36 2.36 0 0.36 0.080 0.68 0.2 0.96l-3.44 3.44c-0.28-0.12-0.64-0.2-0.96-0.2-1.32 0-2.36 1.080-2.36 2.36 0 1.32 1.080 2.36 2.36 2.36s2.36-1.080 2.36-2.36c0-0.36-0.080-0.68-0.2-0.96l3.44-3.44c0.28 0.12 0.64 0.2 0.96 0.2 0.44 0 0.88-0.12 1.24-0.36l2.76 2.12c-0.080 0.24-0.080 0.44-0.080 0.72 0 1.32 1.080 2.36 2.36 2.36s2.36-1.080 2.36-2.36c0-0.28-0.040-0.56-0.12-0.8l4.8-4.080c0.32 0.2 0.72 0.28 1.16 0.28 1.32 0 2.36-1.080 2.36-2.36-0.040-1.2-1.16-2.28-2.44-2.28zM2.36 21c-0.36 0-0.68-0.32-0.68-0.68 0-0.4 0.32-0.68 0.68-0.68s0.68 0.32 0.68 0.68c0 0.36-0.28 0.68-0.68 0.68zM8.24 13.76c0-0.4 0.32-0.68 0.68-0.68s0.68 0.32 0.68 0.68-0.32 0.68-0.68 0.68c-0.36 0-0.68-0.32-0.68-0.68zM15.2 19.28c-0.4 0-0.68-0.32-0.68-0.68s0.32-0.68 0.68-0.68 0.68 0.32 0.68 0.68c-0.040 0.4-0.28 0.68-0.68 0.68zM23.36 12.36c-0.36 0-0.68-0.32-0.68-0.68 0-0.4 0.32-0.68 0.68-0.68 0.4 0 0.68 0.32 0.68 0.68 0 0.4-0.32 0.68-0.68 0.68z">
       </path>
     </g>
   </svg>
`), text: "Líneas"
      },
      {
        id: "historial",
        href: "https://dosxdos.app.iidos.com/historial_montador.html",
        icon: renderIcon(`<path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>`),
        text: "Historial"
      }
    ]
  };

  function createMenuItem(item) {
    const menuItemElement = document.createElement('div');
    menuItemElement.className = 'mobile-menu-item relative z-10 mb-4';

    if (item.submenu) {
      menuItemElement.innerHTML = `
      <div>
        <div class="z-10 flex items-center px-6 py-4 text-white bg-red-600 bg-opacity-60 hover:bg-red-500/60 rounded-xl transition-all duration-300 shadow-xl mobile-submenu-header cursor-pointer">
          <div class="flex items-center justify-center w-12 h-12 bg-white/20 rounded-xl mr-4 transition-all">
            ${item.icon}
          </div>
          <span class="text-lg font-semibold tracking-wide flex-1">${item.text}</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white transition-transform duration-300 submenu-toggle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </div>
        <div class="hidden mobile-submenu-content bg-white/10 rounded-lg mt-2 overflow-hidden">
          ${item.submenu.map(subitem => `
            <a href="${subitem.href}" class="block px-8 py-3 text-white hover:bg-white/20 transition-colors">
              ${subitem.text}
            </a>
          `).join('')}
        </div>
      </div>
    `;

      const submenuHeader = menuItemElement.querySelector('.mobile-submenu-header');
      const submenuContent = menuItemElement.querySelector('.mobile-submenu-content');
      const submenuToggle = submenuHeader.querySelector('.submenu-toggle');

      submenuHeader.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        submenuContent.classList.toggle('hidden');
        submenuToggle.classList.toggle('rotate-180');

        document.querySelectorAll('.mobile-submenu-content').forEach(content => {
          if (content !== submenuContent) {
            content.classList.add('hidden');
            content.previousElementSibling.querySelector('.submenu-toggle').classList.remove('rotate-180');
          }
        });
      });

    } else {
      menuItemElement.innerHTML = `
      <a href="${item.href}" class="w-full flex items-center px-6 py-4 text-white bg-red-600 bg-opacity-60 hover:bg-white/20 rounded-xl transition-all duration-300 shadow-xl group backdrop-blur-lg">
        <div class="flex items-center justify-center w-12 h-12 bg-white/20 rounded-xl mr-4 group-hover:bg-white/30 transition-all">
          ${item.icon}
        </div>
        <span class="text-lg font-semibold tracking-wide">${item.text}</span>
      </a>
    `;
    }

    return menuItemElement;
  }

  // Create notification link with the same styling as menu items
  const notificationLink = document.createElement('a');
  notificationLink.href = "https://dosxdos.app.iidos.com/notificaciones.html";
  notificationLink.className = "z-10 flex items-center px-6 py-4 text-white bg-red-600 bg-opacity-60 hover:bg-white/20 rounded-xl transition-all duration-300 shadow-xl group backdrop-blur-lg";
  notificationLink.innerHTML = `
  <div class="flex items-center justify-center w-12 h-12 bg-white/20 rounded-xl mr-4 group-hover:bg-white/30 transition-all">
    ${renderIcon(`
      <path d="M18 8a6 6 0 0 0-12 0v5a6 6 0 0 1-2 4h16a6 6 0 0 1-2-4V8"></path>
      <line x1="12" y1="22" x2="12" y2="22"></line>
    `)}
  </div>
  <span class="text-lg font-semibold tracking-wide">Notificaciones</span>
  `;

  const menuItems = mobileMenuItems[userRole] || [];

  mobileMenuNav.appendChild(notificationLink);

  menuItems.forEach(item => {
    const menuItemElement = createMenuItem(item);
    mobileMenuNav.appendChild(menuItemElement);
  });
}

/* MENU INTERACTION FUNCTIONS */
function setupMenuInteractions() {
  // Set up desktop dropdown toggles
  const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

  dropdownToggles.forEach(toggle => {
    toggle.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();

      const dropdownId = this.getAttribute('data-dropdown');
      const dropdown = document.getElementById(dropdownId);

      // Close all other dropdowns
      document.querySelectorAll('.desktop-dropdown').forEach(dd => {
        if (dd.id !== dropdownId) {
          dd.classList.remove('desktop-dropdown-visible');
        }
      });

      // Toggle arrow rotation
      document.querySelectorAll('.dropdown-toggle svg:last-child').forEach(arrow => {
        if (arrow.parentElement === this) {
          arrow.classList.toggle('rotate-180');
        } else {
          arrow.classList.remove('rotate-180');
        }
      });

      // Toggle current dropdown
      dropdown.classList.toggle('desktop-dropdown-visible');
    });
  });

  // Mobile Menu Toggle
  const menuButton = document.getElementById("menuButton");
  const mobileMenu = document.getElementById("opcionesMenu");
  const hamburgerTop = document.getElementById("hamburgerTop");
  const hamburgerMiddle = document.getElementById("hamburgerMiddle");
  const hamburgerBottom = document.getElementById("hamburgerBottom");

  if (menuButton && mobileMenu) {
    menuButton.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      mobileMenu.classList.toggle("translate-x-full");
      document.body.classList.toggle("overflow-hidden");

      // Toggle hamburger to X animation
      const isOpen = !mobileMenu.classList.contains("translate-x-full");

      if (isOpen) {
        hamburgerTop.style.transform = "rotate(45deg) translate(10px, 7px)";
        hamburgerMiddle.style.opacity = "0";
        hamburgerBottom.style.transform = "rotate(-45deg) translate(10px, -7px)";
        hamburgerTop.classList.replace("bg-gray-900", "bg-white");
        hamburgerMiddle.classList.replace("bg-gray-900", "bg-white");
        hamburgerBottom.classList.replace("bg-gray-900", "bg-white");
      } else {
        hamburgerTop.style.transform = "";
        hamburgerMiddle.style.opacity = "1";
        hamburgerBottom.style.transform = "";
        hamburgerTop.classList.replace("bg-white", "bg-gray-900");
        hamburgerMiddle.classList.replace("bg-white", "bg-gray-900");
        hamburgerBottom.classList.replace("bg-white", "bg-gray-900");
      }
    });
  }

  // Desktop User Menu
  const userMenuButton = document.getElementById("userMenuButton");
  const userDropdownMenu = document.getElementById("opcionesUsuario");

  if (userMenuButton && userDropdownMenu) {
    userMenuButton.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      userDropdownMenu.classList.toggle("hidden");
      const isExpanded = !userDropdownMenu.classList.contains("hidden");
      userMenuButton.setAttribute("aria-expanded", isExpanded.toString());
    });
  }
}

function setupUserActions() {
  // Edit User Buttons
  const editarUsuario = document.getElementById("editarUsuario");
  const editarUsuarioMobile = document.getElementById("editarUsuarioMobile");

  if (editarUsuario) {
    editarUsuario.addEventListener("click", e => {
      if (navigator.onLine) {
        window.location.href = "https://dosxdos.app.iidos.com/dosxdos.php?modulo=editarUsuario&id=" + usuario.id;
      } else {
        alerta('No es posible acceder a las opciones de edición de usuario sin conexión a internet');
        scrollToTop();
      }
    });
  }

  if (editarUsuarioMobile) {
    editarUsuarioMobile.addEventListener("click", e => {
      if (navigator.onLine) {
        window.location.href = "https://dosxdos.app.iidos.com/dosxdos.php?modulo=editarUsuario&id=" + usuario.id;
      } else {
        alerta('No es posible acceder a las opciones de edición de usuario sin conexión a internet');
        scrollToTop();
      }
    });
  }

  // Logout Buttons
  const cerrarSesionBtn = document.getElementById("cerrarSesion");
  const cerrarSesionMobileBtn = document.getElementById("cerrarSesionMobile");

  if (cerrarSesionBtn) {
    cerrarSesionBtn.addEventListener("click", cerrarSesion);
  }

  if (cerrarSesionMobileBtn) {
    cerrarSesionMobileBtn.addEventListener("click", cerrarSesion);
  }
}

function setupGlobalMenuClosing() {
  document.addEventListener("click", (e) => {
    // Close desktop dropdowns if clicking outside
    if (!e.target.closest('.dropdown-toggle') && !e.target.closest('.desktop-dropdown')) {
      document.querySelectorAll('.desktop-dropdown').forEach(dropdown => {
        dropdown.classList.remove('desktop-dropdown-visible');
      });

      // Reset rotation of all dropdown arrows
      document.querySelectorAll('.dropdown-toggle svg:last-child').forEach(arrow => {
        arrow.classList.remove('rotate-180');
      });
    }

    // Close user menu if clicking outside
    const userMenuButton = document.getElementById("userMenuButton");
    const userDropdownMenu = document.getElementById("opcionesUsuario");

    if (userDropdownMenu && userMenuButton &&
      !userMenuButton.contains(e.target) &&
      !userDropdownMenu.contains(e.target) &&
      !userDropdownMenu.classList.contains("hidden")) {
      userDropdownMenu.classList.add("hidden");
      userMenuButton.setAttribute("aria-expanded", "false");
    }

    // Close mobile menu if clicking outside
    const menuButton = document.getElementById("menuButton");
    const mobileMenu = document.getElementById("opcionesMenu");

    if (mobileMenu && menuButton &&
      !mobileMenu.contains(e.target) &&
      !menuButton.contains(e.target) &&
      !mobileMenu.classList.contains("translate-x-full")) {
      menuButton.click();
    }
  });

  // Handle escape key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      // Close all desktop dropdowns
      document.querySelectorAll('.desktop-dropdown').forEach(dropdown => {
        dropdown.classList.remove('desktop-dropdown-visible');
      });

      // Reset rotation of all dropdown arrows
      document.querySelectorAll('.dropdown-toggle svg:last-child').forEach(arrow => {
        arrow.classList.remove('rotate-180');
      });

      // Close user dropdown
      const userDropdownMenu = document.getElementById("opcionesUsuario");
      if (userDropdownMenu && !userDropdownMenu.classList.contains("hidden")) {
        userDropdownMenu.classList.add("hidden");
        document.getElementById("userMenuButton")?.setAttribute("aria-expanded", "false");
      }

      // Close mobile menu
      const mobileMenu = document.getElementById("opcionesMenu");
      if (mobileMenu && !mobileMenu.classList.contains("translate-x-full")) {
        document.getElementById("menuButton").click();
      }
    }
  });
}

function cerrarSesion() {
  loaderOn();
  try {
    // Clear localStorage
    localStorage.removeItem('login');
    localStorage.removeItem('usuario');
    localStorage.removeItem('loginMontador');

    // Clear cookies
    document.cookie = "login=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.cookie = "usuario=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";

    // Redirect to login page
    window.location.href = "https://dosxdos.app.iidos.com/index.html";
  } catch (error) {
    console.error("Error during logout:", error);
    loaderOff();
    alerta("Error al cerrar sesión: " + error.message);
  }
}

const menuConfigs = {
  admon: [
    {
      id: "archivos",
      href: "https://dosxdos.app.iidos.com/ot.html",
      icon: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-6 h-6 mr-2"
        xmlns="http://www.w3.org/2000/svg">
        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
        <g id="SVGRepo_iconCarrier">
          <path d="M12.37 8.87988H17.62" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M6.38 8.87988L7.13 9.62988L9.38 7.37988" stroke-width="1.5" stroke-linecap="round"
            stroke-linejoin="round"></path>
          <path d="M12.37 15.8799H17.62" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M6.38 15.8799L7.13 16.6299L9.38 14.3799" stroke-width="1.5" stroke-linecap="round"
            stroke-linejoin="round"></path>
          <path d="M9 22H15C20 22 22 20 22 15V9C22 4 20 2 15 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22Z"
            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        </g>
      </svg>`,
      text: "OT"
    },
    {
      id: "icLineasOt",
      href: "https://dosxdos.app.iidos.com/lineas_ot.html",
      icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
        <polyline points="14 2 14 8 20 8" />
        <path d="M9 15l2 2 4-4" />
      </svg>`,
      text: "Líneas OT"
    },
    {
      id: "rutasIcono",
      href: "https://dosxdos.app.iidos.com/rutas.html",
      icon: `<svg viewBox="0 0 100 100" class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
        role="img" preserveAspectRatio="xMidYMid meet">
        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
        <g id="SVGRepo_iconCarrier">
          <path
            d="M21 32C9.459 32 0 41.43 0 52.94c0 4.46 1.424 8.605 3.835 12.012l14.603 25.244c2.045 2.672 3.405 2.165 5.106-.14l16.106-27.41c.325-.59.58-1.216.803-1.856A20.668 20.668 0 0 0 42 52.94C42 41.43 32.544 32 21 32zm0 9.812c6.216 0 11.16 4.931 11.16 11.129c0 6.198-4.944 11.127-11.16 11.127c-6.215 0-11.16-4.93-11.16-11.127c0-6.198 4.945-11.129 11.16-11.129z"
            fill="currentColor" stroke="currentColor"></path>
          <path
            d="M87.75 0C81.018 0 75.5 5.501 75.5 12.216c0 2.601.83 5.019 2.237 7.006l8.519 14.726c1.193 1.558 1.986 1.262 2.978-.082l9.395-15.99c.19-.343.339-.708.468-1.082a12.05 12.05 0 0 0 .903-4.578C100 5.5 94.484 0 87.75 0zm0 5.724c3.626 0 6.51 2.876 6.51 6.492c0 3.615-2.884 6.49-6.51 6.49c-3.625 0-6.51-2.875-6.51-6.49c0-3.616 2.885-6.492 6.51-6.492z"
            fill="currentColor" stroke="currentColor"></path>
          <path
            d="M88.209 37.412c-2.247.05-4.5.145-6.757.312l.348 5.532a126.32 126.32 0 0 1 6.513-.303zm-11.975.82c-3.47.431-6.97 1.045-10.43 2.032l1.303 5.361c3.144-.896 6.402-1.475 9.711-1.886zM60.623 42.12a24.52 24.52 0 0 0-3.004 1.583l-.004.005l-.006.002c-1.375.866-2.824 1.965-4.007 3.562c-.857 1.157-1.558 2.62-1.722 4.35l5.095.565c.038-.406.246-.942.62-1.446h.002v-.002c.603-.816 1.507-1.557 2.582-2.235l.004-.002a19.64 19.64 0 0 1 2.388-1.256zM58 54.655l-3.303 4.235c.783.716 1.604 1.266 2.397 1.726l.01.005l.01.006c2.632 1.497 5.346 2.342 7.862 3.144l1.446-5.318c-2.515-.802-4.886-1.576-6.918-2.73c-.582-.338-1.092-.691-1.504-1.068zm13.335 5.294l-1.412 5.327l.668.208l.82.262c2.714.883 5.314 1.826 7.638 3.131l2.358-4.92c-2.81-1.579-5.727-2.611-8.538-3.525l-.008-.002l-.842-.269zm14.867 7.7l-3.623 3.92c.856.927 1.497 2.042 1.809 3.194l.002.006l.002.009c.372 1.345.373 2.927.082 4.525l5.024 1.072c.41-2.256.476-4.733-.198-7.178c-.587-2.162-1.707-4.04-3.098-5.548zM82.72 82.643a11.84 11.84 0 0 1-1.826 1.572h-.002c-1.8 1.266-3.888 2.22-6.106 3.04l1.654 5.244c2.426-.897 4.917-1.997 7.245-3.635l.006-.005l.003-.002a16.95 16.95 0 0 0 2.639-2.287zm-12.64 6.089c-3.213.864-6.497 1.522-9.821 2.08l.784 5.479c3.421-.575 6.856-1.262 10.27-2.18zm-14.822 2.836c-3.346.457-6.71.83-10.084 1.148l.442 5.522c3.426-.322 6.858-.701 10.285-1.17zm-15.155 1.583c-3.381.268-6.77.486-10.162.67l.256 5.536c3.425-.185 6.853-.406 10.28-.678zm-15.259.92c-2.033.095-4.071.173-6.114.245l.168 5.541a560.1 560.1 0 0 0 6.166-.246z"
            fill="currentColor" stroke="currentColor" fill-rule="evenodd"></path>
        </g>
      </svg>`,
      text: "Rutas"
    },
    {
      id: "lineasIcono",
      href: "https://dosxdos.app.iidos.com/lineas.html",
      icon: `<svg class="w-6 h-6 mr-2" fill="currentColor" stroke="none" viewBox="-3 0 32 32" version="1.1"
        xmlns="http://www.w3.org/2000/svg">
        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
        <g id="SVGRepo_iconCarrier">
          <path
            d="M23.36 9.32c-1.32 0-2.36 1.080-2.36 2.36 0 0.28 0.040 0.56 0.12 0.8l-4.8 4.080c-0.32-0.2-0.72-0.28-1.16-0.28s-0.88 0.12-1.24 0.36l-2.72-2.2c0.080-0.24 0.12-0.44 0.12-0.72 0-1.32-1.080-2.36-2.36-2.36-1.32 0-2.36 1.080-2.36 2.36 0 0.36 0.080 0.68 0.2 0.96l-3.44 3.44c-0.28-0.12-0.64-0.2-0.96-0.2-1.32 0-2.36 1.080-2.36 2.36 0 1.32 1.080 2.36 2.36 2.36s2.36-1.080 2.36-2.36c0-0.36-0.080-0.68-0.2-0.96l3.44-3.44c0.28 0.12 0.64 0.2 0.96 0.2 0.44 0 0.88-0.12 1.24-0.36l2.76 2.12c-0.080 0.24-0.080 0.44-0.080 0.72 0 1.32 1.080 2.36 2.36 2.36s2.36-1.080 2.36-2.36c0-0.28-0.040-0.56-0.12-0.8l4.8-4.080c0.32 0.2 0.72 0.28 1.16 0.28 1.32 0 2.36-1.080 2.36-2.36-0.040-1.2-1.16-2.28-2.44-2.28zM2.36 21c-0.36 0-0.68-0.32-0.68-0.68 0-0.4 0.32-0.68 0.68-0.68s0.68 0.32 0.68 0.68c0 0.36-0.28 0.68-0.68 0.68zM8.24 13.76c0-0.4 0.32-0.68 0.68-0.68s0.68 0.32 0.68 0.68-0.32 0.68-0.68 0.68c-0.36 0-0.68-0.32-0.68-0.68zM15.2 19.28c-0.4 0-0.68-0.32-0.68-0.68s0.32-0.68 0.68-0.68 0.68 0.32 0.68 0.68c-0.040 0.4-0.28 0.68-0.68 0.68zM23.36 12.36c-0.36 0-0.68-0.32-0.68-0.68 0-0.4 0.32-0.68 0.68-0.68 0.4 0 0.68 0.32 0.68 0.68 0 0.4-0.32 0.68-0.68 0.68z">
          </path>
        </g>
      </svg>`,
      text: "Líneas Ruta"
    },
    {
      id: "gastos",
      icon: `<svg viewBox="0 0 24 24" class="w-6 h-6 mr-2" fill="currentColor" stroke="none"
        xmlns="http://www.w3.org/2000/svg">
        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
        <g id="SVGRepo_iconCarrier">
          <path
            d="M5 16C5 15.4477 5.44772 15 6 15H8C8.55229 15 9 15.4477 9 16C9 16.5523 8.55229 17 8 17H6C5.44772 17 5 16.5523 5 16Z">
          </path>
          <path
            d="M11 15C10.4477 15 10 15.4477 10 16C10 16.5523 10.4477 17 11 17H12C12.5523 17 13 16.5523 13 16C13 15.4477 12.5523 15 12 15H11Z">
          </path>
          <path fill-rule="evenodd" clip-rule="evenodd"
            d="M6.788 3C5.96948 2.99999 5.29393 2.99998 4.74393 3.04565C4.17258 3.0931 3.64774 3.19496 3.1561 3.45035C2.42553 3.82985 1.82985 4.42553 1.45035 5.1561C1.19496 5.64774 1.0931 6.17258 1.04565 6.74393C0.999977 7.29393 0.999988 7.96946 1 8.78798V15.212C0.999988 16.0305 0.999977 16.7061 1.04565 17.2561C1.0931 17.8274 1.19496 18.3523 1.45035 18.8439C1.82985 19.5745 2.42553 20.1702 3.1561 20.5497C3.64774 20.805 4.17258 20.9069 4.74393 20.9544C5.29394 21 5.96949 21 6.78803 21H17.212C18.0305 21 18.7061 21 19.2561 20.9544C19.8274 20.9069 20.3523 20.805 20.8439 20.5497C21.5745 20.1702 22.1702 19.5745 22.5497 18.8439C22.805 18.3523 22.9069 17.8274 22.9544 17.2561C23 16.7061 23 16.0305 23 15.212V8.78802C23 7.96949 23 7.29394 22.9544 6.74393C22.9069 6.17258 22.805 5.64774 22.5497 5.1561C22.1702 4.42553 21.5745 3.82985 20.8439 3.45035C20.3523 3.19496 19.8274 3.0931 19.2561 3.04565C18.7061 2.99998 18.0305 2.99999 17.212 3H6.788ZM4.07805 5.22517C4.23663 5.1428 4.46402 5.07578 4.90945 5.03879C5.36686 5.00081 5.95898 5 6.83 5H17.17C18.041 5 18.6331 5.00081 19.0906 5.03879C19.536 5.07578 19.7634 5.1428 19.922 5.22517C20.2872 5.41493 20.5851 5.71277 20.7748 6.07805C20.8572 6.23663 20.9242 6.46402 20.9612 6.90945C20.9857 7.20418 20.9947 7.55484 20.9981 8H3.00194C3.00528 7.55484 3.01431 7.20418 3.03879 6.90945C3.07578 6.46402 3.1428 6.23663 3.22517 6.07805C3.41493 5.71277 3.71277 5.41493 4.07805 5.22517ZM3 10V15.17C3 16.041 3.00081 16.6331 3.03879 17.0906C3.07578 17.536 3.1428 17.7634 3.22517 17.922C3.41493 18.2872 3.71277 18.5851 4.07805 18.7748C4.23663 18.8572 4.46402 18.9242 4.90945 18.9612C5.36686 18.9992 5.95898 19 6.83 19H17.17C18.041 19 18.6331 18.9992 19.0906 18.9612C19.536 18.9242 19.7634 18.8572 19.922 18.7748C20.2872 18.5851 20.5851 18.2872 20.7748 17.922C20.8572 17.7634 20.9242 17.536 20.9612 17.0906C20.9992 16.6331 21 16.041 21 15.17V10H3Z">
          </path>
        </g>
      </svg>`,
      text: "Gastos",
      submenu: [
        {
          href: "https://dosxdos.app.iidos.com/gastos_rutas.html",
          text: "Gastos Rutas"
        },
        {
          href: "javascript:void(0)",
          class: "text-gray-200 cursor-not-allowed hover:text-gray-200",
          text: "Reportar Gastos (próximamente)"
        }
      ]
    },
    {
      id: "clientes",
      icon: `<svg viewBox="0 0 28 28" class="w-6 h-6 mr-2" fill="currentColor" stroke="none"
        xmlns="http://www.w3.org/2000/svg">
        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
        <g id="SVGRepo_iconCarrier">
          <path clip-rule="evenodd"
            d="M1.82047 1C1.36734 1 1 1.35728 1 1.79801V2.39948C1 2.84021 1.36734 3.19749 1.82047 3.19749H3.72716C4.03867 3.19749 4.3233 3.36906 4.46192 3.64038L5.4947 5.93251C5.53326 6.00798 5.56364 6.09443 5.62081 6.15194L10.057 16.4429C10.0129 16.4634 9.97056 16.4883 9.93075 16.5176C8.70163 17.4226 7.87009 18.5878 7.87001 19.7604C7.86996 20.4429 8.16289 21.0807 8.75002 21.5212C9.30752 21.9394 10.0364 22.1118 10.8189 22.1118H10.8446C10.336 22.6308 10.0238 23.3336 10.0238 24.1072C10.0238 25.7049 11.3554 27 12.998 27C14.6406 27 15.9722 25.7049 15.9722 24.1072C15.9722 23.3336 15.66 22.6308 15.1513 22.1118H19.0494C18.5408 22.6308 18.2285 23.3336 18.2285 24.1072C18.2285 25.7049 19.5601 27 21.2027 27C22.8454 27 24.177 25.7049 24.177 24.1072C24.177 23.3336 23.8647 22.6308 23.3561 22.1118H23.9718C24.425 22.1118 24.7923 21.7545 24.7923 21.3138V20.9148C24.7923 20.474 24.425 20.1167 23.9718 20.1167H10.8189C10.3192 20.1167 10.0864 20.0041 10.0028 19.9414C9.94878 19.9009 9.92119 19.8618 9.9212 19.7606C9.92122 19.4917 10.1711 18.8708 11.069 18.1827C11.1084 18.1524 11.1453 18.1194 11.1792 18.084C11.2692 18.1089 11.3635 18.1221 11.4601 18.1221H23.9235C24.4248 18.1221 24.8527 17.7696 24.9351 17.2885L26.9858 5.31837C27.09 4.71036 26.6079 4.1569 25.9742 4.1569H7.35431C7.1981 4.1569 7.05618 4.06597 6.9909 3.92405L5.84968 1.44289C5.71106 1.17157 5.42642 1 5.11492 1H1.82047ZM8.47667 6.15194C8.18952 6.15194 7.99591 6.44552 8.10899 6.70946L12.04 15.8846C12.103 16.0317 12.2476 16.1271 12.4076 16.1271H22.7173C22.9122 16.1271 23.0787 15.9867 23.1116 15.7946L24.6834 6.61948C24.7253 6.37513 24.5371 6.15194 24.2892 6.15194H8.47667ZM11.8698 24.1072C11.8698 23.5012 12.3749 23.0099 12.998 23.0099C13.621 23.0099 14.1261 23.5012 14.1261 24.1072C14.1261 24.7132 13.621 25.2045 12.998 25.2045C12.3749 25.2045 11.8698 24.7132 11.8698 24.1072ZM21.2027 23.0099C20.5797 23.0099 20.0746 23.5012 20.0746 24.1072C20.0746 24.7132 20.5797 25.2045 21.2027 25.2045C21.8258 25.2045 22.3309 24.7132 22.3309 24.1072C22.3309 23.5012 21.8258 23.0099 21.2027 23.0099Z"
            fill-rule="evenodd"></path>
        </g>
      </svg>`,
      text: "Clientes",
      submenu: [
        {
          href: "https://dosxdos.app.iidos.com/ots_clientes.html",
          text: "Restricciones Tipos de OT"
        },
        {
          href: "https://dosxdos.app.iidos.com/firmas_clientes.html",
          text: "Restricciones Firmas",
        }
      ]
    },
    {
      id: "usuarios",
      href: "https://dosxdos.app.iidos.com/dosxdos.php?modulo=usuarios",
      icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
        <circle cx="9" cy="7" r="4"></circle>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
      </svg>`,
      text: "Usuarios"
    },
    {
      id: "sincronizacion",
      icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2 text-gray-400" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 2v6h-6"></path>
        <path d="M3 12a9 9 0 0 1 15-6.7L21 8"></path>
        <path d="M3 22v-6h6"></path>
        <path d="M21 12a9 9 0 0 1-15 6.7L3 16"></path>
      </svg>`,
      text: "Sincronización",
      href: "javascript:void(0)",
      class: "text-gray-200 cursor-not-allowed hover:text-gray-200"
      //   submenu: [
      //     {
      //       href: "#",
      //       text: "Sinc. OT Navision"
      //     },
      //     {
      //       href: "https://dosxdos.app.iidos.com/apirest/sincronizador2.php",
      //       text: "Sinc. Total OT Navision"
      //     }
      //   ]
    }
  ],
  oficina: [
    {
      id: "archivos",
      href: "https://dosxdos.app.iidos.com/ot.html",
      icon: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-6 h-6 mr-2"
        xmlns="http://www.w3.org/2000/svg">
        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
        <g id="SVGRepo_iconCarrier">
          <path d="M12.37 8.87988H17.62" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M6.38 8.87988L7.13 9.62988L9.38 7.37988" stroke-width="1.5" stroke-linecap="round"
            stroke-linejoin="round"></path>
          <path d="M12.37 15.8799H17.62" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M6.38 15.8799L7.13 16.6299L9.38 14.3799" stroke-width="1.5" stroke-linecap="round"
            stroke-linejoin="round"></path>
          <path d="M9 22H15C20 22 22 20 22 15V9C22 4 20 2 15 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22Z"
            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        </g>
      </svg>`,
      text: "OT"
    },
    {
      id: "icLineasOt",
      href: "https://dosxdos.app.iidos.com/lineas_ot.html",
      icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" viewBox="0 0 24 24" fill="none"
      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
      <polyline points="14 2 14 8 20 8" />
      <path d="M9 15l2 2 4-4" />
    </svg>`,
      text: "Líneas OT"
    },
    {
      id: "rutasIcono",
      href: "https://dosxdos.app.iidos.com/rutas.html",
      icon: `<svg viewBox="0 0 100 100" class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
      xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
      role="img" preserveAspectRatio="xMidYMid meet">
      <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
      <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
      <g id="SVGRepo_iconCarrier">
        <path
          d="M21 32C9.459 32 0 41.43 0 52.94c0 4.46 1.424 8.605 3.835 12.012l14.603 25.244c2.045 2.672 3.405 2.165 5.106-.14l16.106-27.41c.325-.59.58-1.216.803-1.856A20.668 20.668 0 0 0 42 52.94C42 41.43 32.544 32 21 32zm0 9.812c6.216 0 11.16 4.931 11.16 11.129c0 6.198-4.944 11.127-11.16 11.127c-6.215 0-11.16-4.93-11.16-11.127c0-6.198 4.945-11.129 11.16-11.129z"
          fill="currentColor" stroke="currentColor"></path>
        <path
          d="M87.75 0C81.018 0 75.5 5.501 75.5 12.216c0 2.601.83 5.019 2.237 7.006l8.519 14.726c1.193 1.558 1.986 1.262 2.978-.082l9.395-15.99c.19-.343.339-.708.468-1.082a12.05 12.05 0 0 0 .903-4.578C100 5.5 94.484 0 87.75 0zm0 5.724c3.626 0 6.51 2.876 6.51 6.492c0 3.615-2.884 6.49-6.51 6.49c-3.625 0-6.51-2.875-6.51-6.49c0-3.616 2.885-6.492 6.51-6.492z"
          fill="currentColor" stroke="currentColor"></path>
        <path
          d="M88.209 37.412c-2.247.05-4.5.145-6.757.312l.348 5.532a126.32 126.32 0 0 1 6.513-.303zm-11.975.82c-3.47.431-6.97 1.045-10.43 2.032l1.303 5.361c3.144-.896 6.402-1.475 9.711-1.886zM60.623 42.12a24.52 24.52 0 0 0-3.004 1.583l-.004.005l-.006.002c-1.375.866-2.824 1.965-4.007 3.562c-.857 1.157-1.558 2.62-1.722 4.35l5.095.565c.038-.406.246-.942.62-1.446h.002v-.002c.603-.816 1.507-1.557 2.582-2.235l.004-.002a19.64 19.64 0 0 1 2.388-1.256zM58 54.655l-3.303 4.235c.783.716 1.604 1.266 2.397 1.726l.01.005l.01.006c2.632 1.497 5.346 2.342 7.862 3.144l1.446-5.318c-2.515-.802-4.886-1.576-6.918-2.73c-.582-.338-1.092-.691-1.504-1.068zm13.335 5.294l-1.412 5.327l.668.208l.82.262c2.714.883 5.314 1.826 7.638 3.131l2.358-4.92c-2.81-1.579-5.727-2.611-8.538-3.525l-.008-.002l-.842-.269zm14.867 7.7l-3.623 3.92c.856.927 1.497 2.042 1.809 3.194l.002.006l.002.009c.372 1.345.373 2.927.082 4.525l5.024 1.072c.41-2.256.476-4.733-.198-7.178c-.587-2.162-1.707-4.04-3.098-5.548zM82.72 82.643a11.84 11.84 0 0 1-1.826 1.572h-.002c-1.8 1.266-3.888 2.22-6.106 3.04l1.654 5.244c2.426-.897 4.917-1.997 7.245-3.635l.006-.005l.003-.002a16.95 16.95 0 0 0 2.639-2.287zm-12.64 6.089c-3.213.864-6.497 1.522-9.821 2.08l.784 5.479c3.421-.575 6.856-1.262 10.27-2.18zm-14.822 2.836c-3.346.457-6.71.83-10.084 1.148l.442 5.522c3.426-.322 6.858-.701 10.285-1.17zm-15.155 1.583c-3.381.268-6.77.486-10.162.67l.256 5.536c3.425-.185 6.853-.406 10.28-.678zm-15.259.92c-2.033.095-4.071.173-6.114.245l.168 5.541a560.1 560.1 0 0 0 6.166-.246z"
          fill="currentColor" stroke="currentColor" fill-rule="evenodd"></path>
      </g>
    </svg>`,
      text: "Rutas"
    },
    {
      id: "lineasIcono",
      href: "https://dosxdos.app.iidos.com/lineas.html",
      icon: `<svg class="w-6 h-6 mr-2" fill="currentColor" stroke="none" viewBox="-3 0 32 32" version="1.1"
      xmlns="http://www.w3.org/2000/svg">
      <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
      <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
      <g id="SVGRepo_iconCarrier">
        <path
          d="M23.36 9.32c-1.32 0-2.36 1.080-2.36 2.36 0 0.28 0.040 0.56 0.12 0.8l-4.8 4.080c-0.32-0.2-0.72-0.28-1.16-0.28s-0.88 0.12-1.24 0.36l-2.72-2.2c0.080-0.24 0.12-0.44 0.12-0.72 0-1.32-1.080-2.36-2.36-2.36-1.32 0-2.36 1.080-2.36 2.36 0 0.36 0.080 0.68 0.2 0.96l-3.44 3.44c-0.28-0.12-0.64-0.2-0.96-0.2-1.32 0-2.36 1.080-2.36 2.36 0 1.32 1.080 2.36 2.36 2.36s2.36-1.080 2.36-2.36c0-0.36-0.080-0.68-0.2-0.96l3.44-3.44c0.28 0.12 0.64 0.2 0.96 0.2 0.44 0 0.88-0.12 1.24-0.36l2.76 2.12c-0.080 0.24-0.080 0.44-0.080 0.72 0 1.32 1.080 2.36 2.36 2.36s2.36-1.080 2.36-2.36c0-0.28-0.040-0.56-0.12-0.8l4.8-4.080c0.32 0.2 0.72 0.28 1.16 0.28 1.32 0 2.36-1.080 2.36-2.36-0.040-1.2-1.16-2.28-2.44-2.28zM2.36 21c-0.36 0-0.68-0.32-0.68-0.68 0-0.4 0.32-0.68 0.68-0.68s0.68 0.32 0.68 0.68c0 0.36-0.28 0.68-0.68 0.68zM8.24 13.76c0-0.4 0.32-0.68 0.68-0.68s0.68 0.32 0.68 0.68-0.32 0.68-0.68 0.68c-0.36 0-0.68-0.32-0.68-0.68zM15.2 19.28c-0.4 0-0.68-0.32-0.68-0.68s0.32-0.68 0.68-0.68 0.68 0.32 0.68 0.68c-0.040 0.4-0.28 0.68-0.68 0.68zM23.36 12.36c-0.36 0-0.68-0.32-0.68-0.68 0-0.4 0.32-0.68 0.68-0.68 0.4 0 0.68 0.32 0.68 0.68 0 0.4-0.32 0.68-0.68 0.68z">
        </path>
      </g>
    </svg>`,
      text: "Líneas Ruta"
    },
    {
      id: "gastos",
      icon: `<svg viewBox="0 0 24 24" class="w-6 h-6 mr-2" fill="currentColor" stroke="none"
      xmlns="http://www.w3.org/2000/svg">
      <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
      <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
      <g id="SVGRepo_iconCarrier">
        <path
          d="M5 16C5 15.4477 5.44772 15 6 15H8C8.55229 15 9 15.4477 9 16C9 16.5523 8.55229 17 8 17H6C5.44772 17 5 16.5523 5 16Z">
        </path>
        <path
          d="M11 15C10.4477 15 10 15.4477 10 16C10 16.5523 10.4477 17 11 17H12C12.5523 17 13 16.5523 13 16C13 15.4477 12.5523 15 12 15H11Z">
        </path>
        <path fill-rule="evenodd" clip-rule="evenodd"
          d="M6.788 3C5.96948 2.99999 5.29393 2.99998 4.74393 3.04565C4.17258 3.0931 3.64774 3.19496 3.1561 3.45035C2.42553 3.82985 1.82985 4.42553 1.45035 5.1561C1.19496 5.64774 1.0931 6.17258 1.04565 6.74393C0.999977 7.29393 0.999988 7.96946 1 8.78798V15.212C0.999988 16.0305 0.999977 16.7061 1.04565 17.2561C1.0931 17.8274 1.19496 18.3523 1.45035 18.8439C1.82985 19.5745 2.42553 20.1702 3.1561 20.5497C3.64774 20.805 4.17258 20.9069 4.74393 20.9544C5.29394 21 5.96949 21 6.78803 21H17.212C18.0305 21 18.7061 21 19.2561 20.9544C19.8274 20.9069 20.3523 20.805 20.8439 20.5497C21.5745 20.1702 22.1702 19.5745 22.5497 18.8439C22.805 18.3523 22.9069 17.8274 22.9544 17.2561C23 16.7061 23 16.0305 23 15.212V8.78802C23 7.96949 23 7.29394 22.9544 6.74393C22.9069 6.17258 22.805 5.64774 22.5497 5.1561C22.1702 4.42553 21.5745 3.82985 20.8439 3.45035C20.3523 3.19496 19.8274 3.0931 19.2561 3.04565C18.7061 2.99998 18.0305 2.99999 17.212 3H6.788ZM4.07805 5.22517C4.23663 5.1428 4.46402 5.07578 4.90945 5.03879C5.36686 5.00081 5.95898 5 6.83 5H17.17C18.041 5 18.6331 5.00081 19.0906 5.03879C19.536 5.07578 19.7634 5.1428 19.922 5.22517C20.2872 5.41493 20.5851 5.71277 20.7748 6.07805C20.8572 6.23663 20.9242 6.46402 20.9612 6.90945C20.9857 7.20418 20.9947 7.55484 20.9981 8H3.00194C3.00528 7.55484 3.01431 7.20418 3.03879 6.90945C3.07578 6.46402 3.1428 6.23663 3.22517 6.07805C3.41493 5.71277 3.71277 5.41493 4.07805 5.22517ZM3 10V15.17C3 16.041 3.00081 16.6331 3.03879 17.0906C3.07578 17.536 3.1428 17.7634 3.22517 17.922C3.41493 18.2872 3.71277 18.5851 4.07805 18.7748C4.23663 18.8572 4.46402 18.9242 4.90945 18.9612C5.36686 18.9992 5.95898 19 6.83 19H17.17C18.041 19 18.6331 18.9992 19.0906 18.9612C19.536 18.9242 19.7634 18.8572 19.922 18.7748C20.2872 18.5851 20.5851 18.2872 20.7748 17.922C20.8572 17.7634 20.9242 17.536 20.9612 17.0906C20.9992 16.6331 21 16.041 21 15.17V10H3Z">
        </path>
      </g>
    </svg>`,
      text: "Gastos",
      submenu: [
        {
          href: "https://dosxdos.app.iidos.com/gastos_rutas.html",
          text: "Gastos Rutas"
        },
        {
          href: "",
          text: "Reportar Gastos (próximamente)"
        }
      ]
    }
  ],
  diseno: [
    {
      id: "archivos",
      href: "https://dosxdos.app.iidos.com/ot.html",
      icon: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-6 h-6 mr-2"
      xmlns="http://www.w3.org/2000/svg">
      <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
      <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
      <g id="SVGRepo_iconCarrier">
        <path d="M12.37 8.87988H17.62" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        <path d="M6.38 8.87988L7.13 9.62988L9.38 7.37988" stroke-width="1.5" stroke-linecap="round"
          stroke-linejoin="round"></path>
        <path d="M12.37 15.8799H17.62" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        <path d="M6.38 15.8799L7.13 16.6299L9.38 14.3799" stroke-width="1.5" stroke-linecap="round"
          stroke-linejoin="round"></path>
        <path d="M9 22H15C20 22 22 20 22 15V9C22 4 20 2 15 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22Z"
          stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
      </g>
    </svg>`,
      text: "OT"
    },
    {
      id: "icLineasOt",
      href: "https://dosxdos.app.iidos.com/lineas_ot.html",
      icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" viewBox="0 0 24 24" fill="none"
      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
      <polyline points="14 2 14 8 20 8" />
      <path d="M9 15l2 2 4-4" />
    </svg>`,
      text: "Líneas OT"
    },
    {
      id: "rutasIcono",
      href: "https://dosxdos.app.iidos.com/rutas.html",
      icon: `<svg viewBox="0 0 100 100" class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
      xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
      role="img" preserveAspectRatio="xMidYMid meet">
      <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
      <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
      <g id="SVGRepo_iconCarrier">
        <path
          d="M21 32C9.459 32 0 41.43 0 52.94c0 4.46 1.424 8.605 3.835 12.012l14.603 25.244c2.045 2.672 3.405 2.165 5.106-.14l16.106-27.41c.325-.59.58-1.216.803-1.856A20.668 20.668 0 0 0 42 52.94C42 41.43 32.544 32 21 32zm0 9.812c6.216 0 11.16 4.931 11.16 11.129c0 6.198-4.944 11.127-11.16 11.127c-6.215 0-11.16-4.93-11.16-11.127c0-6.198 4.945-11.129 11.16-11.129z"
          fill="currentColor" stroke="currentColor"></path>
        <path
          d="M87.75 0C81.018 0 75.5 5.501 75.5 12.216c0 2.601.83 5.019 2.237 7.006l8.519 14.726c1.193 1.558 1.986 1.262 2.978-.082l9.395-15.99c.19-.343.339-.708.468-1.082a12.05 12.05 0 0 0 .903-4.578C100 5.5 94.484 0 87.75 0zm0 5.724c3.626 0 6.51 2.876 6.51 6.492c0 3.615-2.884 6.49-6.51 6.49c-3.625 0-6.51-2.875-6.51-6.49c0-3.616 2.885-6.492 6.51-6.492z"
          fill="currentColor" stroke="currentColor"></path>
        <path
          d="M88.209 37.412c-2.247.05-4.5.145-6.757.312l.348 5.532a126.32 126.32 0 0 1 6.513-.303zm-11.975.82c-3.47.431-6.97 1.045-10.43 2.032l1.303 5.361c3.144-.896 6.402-1.475 9.711-1.886zM60.623 42.12a24.52 24.52 0 0 0-3.004 1.583l-.004.005l-.006.002c-1.375.866-2.824 1.965-4.007 3.562c-.857 1.157-1.558 2.62-1.722 4.35l5.095.565c.038-.406.246-.942.62-1.446h.002v-.002c.603-.816 1.507-1.557 2.582-2.235l.004-.002a19.64 19.64 0 0 1 2.388-1.256zM58 54.655l-3.303 4.235c.783.716 1.604 1.266 2.397 1.726l.01.005l.01.006c2.632 1.497 5.346 2.342 7.862 3.144l1.446-5.318c-2.515-.802-4.886-1.576-6.918-2.73c-.582-.338-1.092-.691-1.504-1.068zm13.335 5.294l-1.412 5.327l.668.208l.82.262c2.714.883 5.314 1.826 7.638 3.131l2.358-4.92c-2.81-1.579-5.727-2.611-8.538-3.525l-.008-.002l-.842-.269zm14.867 7.7l-3.623 3.92c.856.927 1.497 2.042 1.809 3.194l.002.006l.002.009c.372 1.345.373 2.927.082 4.525l5.024 1.072c.41-2.256.476-4.733-.198-7.178c-.587-2.162-1.707-4.04-3.098-5.548zM82.72 82.643a11.84 11.84 0 0 1-1.826 1.572h-.002c-1.8 1.266-3.888 2.22-6.106 3.04l1.654 5.244c2.426-.897 4.917-1.997 7.245-3.635l.006-.005l.003-.002a16.95 16.95 0 0 0 2.639-2.287zm-12.64 6.089c-3.213.864-6.497 1.522-9.821 2.08l.784 5.479c3.421-.575 6.856-1.262 10.27-2.18zm-14.822 2.836c-3.346.457-6.71.83-10.084 1.148l.442 5.522c3.426-.322 6.858-.701 10.285-1.17zm-15.155 1.583c-3.381.268-6.77.486-10.162.67l.256 5.536c3.425-.185 6.853-.406 10.28-.678zm-15.259.92c-2.033.095-4.071.173-6.114.245l.168 5.541a560.1 560.1 0 0 0 6.166-.246z"
          fill="currentColor" stroke="currentColor" fill-rule="evenodd"></path>
      </g>
    </svg>`,
      text: "Rutas"
    },
    {
      id: "lineasIcono",
      href: "https://dosxdos.app.iidos.com/lineas.html",
      icon: `<svg class="w-6 h-6 mr-2" fill="currentColor" stroke="none" viewBox="-3 0 32 32" version="1.1"
      xmlns="http://www.w3.org/2000/svg">
      <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
      <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
      <g id="SVGRepo_iconCarrier">
        <path
          d="M23.36 9.32c-1.32 0-2.36 1.080-2.36 2.36 0 0.28 0.040 0.56 0.12 0.8l-4.8 4.080c-0.32-0.2-0.72-0.28-1.16-0.28s-0.88 0.12-1.24 0.36l-2.72-2.2c0.080-0.24 0.12-0.44 0.12-0.72 0-1.32-1.080-2.36-2.36-2.36-1.32 0-2.36 1.080-2.36 2.36 0 0.36 0.080 0.68 0.2 0.96l-3.44 3.44c-0.28-0.12-0.64-0.2-0.96-0.2-1.32 0-2.36 1.080-2.36 2.36 0 1.32 1.080 2.36 2.36 2.36s2.36-1.080 2.36-2.36c0-0.36-0.080-0.68-0.2-0.96l3.44-3.44c0.28 0.12 0.64 0.2 0.96 0.2 0.44 0 0.88-0.12 1.24-0.36l2.76 2.12c-0.080 0.24-0.080 0.44-0.080 0.72 0 1.32 1.080 2.36 2.36 2.36s2.36-1.080 2.36-2.36c0-0.28-0.040-0.56-0.12-0.8l4.8-4.080c0.32 0.2 0.72 0.28 1.16 0.28 1.32 0 2.36-1.080 2.36-2.36-0.040-1.2-1.16-2.28-2.44-2.28zM2.36 21c-0.36 0-0.68-0.32-0.68-0.68 0-0.4 0.32-0.68 0.68-0.68s0.68 0.32 0.68 0.68c0 0.36-0.28 0.68-0.68 0.68zM8.24 13.76c0-0.4 0.32-0.68 0.68-0.68s0.68 0.32 0.68 0.68-0.32 0.68-0.68 0.68c-0.36 0-0.68-0.32-0.68-0.68zM15.2 19.28c-0.4 0-0.68-0.32-0.68-0.68s0.32-0.68 0.68-0.68 0.68 0.32 0.68 0.68c-0.040 0.4-0.28 0.68-0.68 0.68zM23.36 12.36c-0.36 0-0.68-0.32-0.68-0.68 0-0.4 0.32-0.68 0.68-0.68 0.4 0 0.68 0.32 0.68 0.68 0 0.4-0.32 0.68-0.68 0.68z">
          </path>
        </g>
      </svg>`,
      text: "Líneas Ruta"
    },
    {
      id: "gastos",
      icon: `<svg viewBox="0 0 24 24" class="w-6 h-6 mr-2" fill="currentColor" stroke="none"
        xmlns="http://www.w3.org/2000/svg">
        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
        <g id="SVGRepo_iconCarrier">
          <path
            d="M5 16C5 15.4477 5.44772 15 6 15H8C8.55229 15 9 15.4477 9 16C9 16.5523 8.55229 17 8 17H6C5.44772 17 5 16.5523 5 16Z">
          </path>
          <path
            d="M11 15C10.4477 15 10 15.4477 10 16C10 16.5523 10.4477 17 11 17H12C12.5523 17 13 16.5523 13 16C13 15.4477 12.5523 15 12 15H11Z">
          </path>
          <path fill-rule="evenodd" clip-rule="evenodd"
            d="M6.788 3C5.96948 2.99999 5.29393 2.99998 4.74393 3.04565C4.17258 3.0931 3.64774 3.19496 3.1561 3.45035C2.42553 3.82985 1.82985 4.42553 1.45035 5.1561C1.19496 5.64774 1.0931 6.17258 1.04565 6.74393C0.999977 7.29393 0.999988 7.96946 1 8.78798V15.212C0.999988 16.0305 0.999977 16.7061 1.04565 17.2561C1.0931 17.8274 1.19496 18.3523 1.45035 18.8439C1.82985 19.5745 2.42553 20.1702 3.1561 20.5497C3.64774 20.805 4.17258 20.9069 4.74393 20.9544C5.29394 21 5.96949 21 6.78803 21H17.212C18.0305 21 18.7061 21 19.2561 20.9544C19.8274 20.9069 20.3523 20.805 20.8439 20.5497C21.5745 20.1702 22.1702 19.5745 22.5497 18.8439C22.805 18.3523 22.9069 17.8274 22.9544 17.2561C23 16.7061 23 16.0305 23 15.212V8.78802C23 7.96949 23 7.29394 22.9544 6.74393C22.9069 6.17258 22.805 5.64774 22.5497 5.1561C22.1702 4.42553 21.5745 3.82985 20.8439 3.45035C20.3523 3.19496 19.8274 3.0931 19.2561 3.04565C18.7061 2.99998 18.0305 2.99999 17.212 3H6.788ZM4.07805 5.22517C4.23663 5.1428 4.46402 5.07578 4.90945 5.03879C5.36686 5.00081 5.95898 5 6.83 5H17.17C18.041 5 18.6331 5.00081 19.0906 5.03879C19.536 5.07578 19.7634 5.1428 19.922 5.22517C20.2872 5.41493 20.5851 5.71277 20.7748 6.07805C20.8572 6.23663 20.9242 6.46402 20.9612 6.90945C20.9857 7.20418 20.9947 7.55484 20.9981 8H3.00194C3.00528 7.55484 3.01431 7.20418 3.03879 6.90945C3.07578 6.46402 3.1428 6.23663 3.22517 6.07805C3.41493 5.71277 3.71277 5.41493 4.07805 5.22517ZM3 10V15.17C3 16.041 3.00081 16.6331 3.03879 17.0906C3.07578 17.536 3.1428 17.7634 3.22517 17.922C3.41493 18.2872 3.71277 18.5851 4.07805 18.7748C4.23663 18.8572 4.46402 18.9242 4.90945 18.9612C5.36686 18.9992 5.95898 19 6.83 19H17.17C18.041 19 18.6331 18.9992 19.0906 18.9612C19.536 18.9242 19.7634 18.8572 19.922 18.7748C20.2872 18.5851 20.5851 18.2872 20.7748 17.922C20.8572 17.7634 20.9242 17.536 20.9612 17.0906C20.9992 16.6331 21 16.041 21 15.17V10H3Z">
          </path>
        </g>
      </svg>`,
      text: "Gastos",
      submenu: [
        {
          href: "https://dosxdos.app.iidos.com/gastos_rutas.html",
          text: "Gastos Rutas"
        },
        {
          href: "",
          text: "Reportar Gastos (próximamente)"
        }
      ]
    }
  ],
  estudio: [
    {
      id: "archivos",
      href: "https://dosxdos.app.iidos.com/ot.html",
      icon: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-6 h-6 mr-2"
        xmlns="http://www.w3.org/2000/svg">
        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
        <g id="SVGRepo_iconCarrier">
          <path d="M12.37 8.87988H17.62" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M6.38 8.87988L7.13 9.62988L9.38 7.37988" stroke-width="1.5" stroke-linecap="round"
            stroke-linejoin="round"></path>
          <path d="M12.37 15.8799H17.62" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M6.38 15.8799L7.13 16.6299L9.38 14.3799" stroke-width="1.5" stroke-linecap="round"
            stroke-linejoin="round"></path>
          <path d="M9 22H15C20 22 22 20 22 15V9C22 4 20 2 15 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22Z"
            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        </g>
      </svg>`,
      text: "OT"
    },
    {
      id: "icLineasOt",
      href: "https://dosxdos.app.iidos.com/lineas_ot.html",
      icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
        <polyline points="14 2 14 8 20 8" />
        <path d="M9 15l2 2 4-4" />
      </svg>`,
      text: "Líneas OT"
    },
    {
      id: "rutasIcono",
      href: "https://dosxdos.app.iidos.com/rutas.html",
      icon: `<svg viewBox="0 0 100 100" class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
        role="img" preserveAspectRatio="xMidYMid meet">
        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
        <g id="SVGRepo_iconCarrier">
          <path
            d="M21 32C9.459 32 0 41.43 0 52.94c0 4.46 1.424 8.605 3.835 12.012l14.603 25.244c2.045 2.672 3.405 2.165 5.106-.14l16.106-27.41c.325-.59.58-1.216.803-1.856A20.668 20.668 0 0 0 42 52.94C42 41.43 32.544 32 21 32zm0 9.812c6.216 0 11.16 4.931 11.16 11.129c0 6.198-4.944 11.127-11.16 11.127c-6.215 0-11.16-4.93-11.16-11.127c0-6.198 4.945-11.129 11.16-11.129z"
            fill="currentColor" stroke="currentColor"></path>
          <path
            d="M87.75 0C81.018 0 75.5 5.501 75.5 12.216c0 2.601.83 5.019 2.237 7.006l8.519 14.726c1.193 1.558 1.986 1.262 2.978-.082l9.395-15.99c.19-.343.339-.708.468-1.082a12.05 12.05 0 0 0 .903-4.578C100 5.5 94.484 0 87.75 0zm0 5.724c3.626 0 6.51 2.876 6.51 6.492c0 3.615-2.884 6.49-6.51 6.49c-3.625 0-6.51-2.875-6.51-6.49c0-3.616 2.885-6.492 6.51-6.492z"
            fill="currentColor" stroke="currentColor"></path>
          <path
            d="M88.209 37.412c-2.247.05-4.5.145-6.757.312l.348 5.532a126.32 126.32 0 0 1 6.513-.303zm-11.975.82c-3.47.431-6.97 1.045-10.43 2.032l1.303 5.361c3.144-.896 6.402-1.475 9.711-1.886zM60.623 42.12a24.52 24.52 0 0 0-3.004 1.583l-.004.005l-.006.002c-1.375.866-2.824 1.965-4.007 3.562c-.857 1.157-1.558 2.62-1.722 4.35l5.095.565c.038-.406.246-.942.62-1.446h.002v-.002c.603-.816 1.507-1.557 2.582-2.235l.004-.002a19.64 19.64 0 0 1 2.388-1.256zM58 54.655l-3.303 4.235c.783.716 1.604 1.266 2.397 1.726l.01.005l.01.006c2.632 1.497 5.346 2.342 7.862 3.144l1.446-5.318c-2.515-.802-4.886-1.576-6.918-2.73c-.582-.338-1.092-.691-1.504-1.068zm13.335 5.294l-1.412 5.327l.668.208l.82.262c2.714.883 5.314 1.826 7.638 3.131l2.358-4.92c-2.81-1.579-5.727-2.611-8.538-3.525l-.008-.002l-.842-.269zm14.867 7.7l-3.623 3.92c.856.927 1.497 2.042 1.809 3.194l.002.006l.002.009c.372 1.345.373 2.927.082 4.525l5.024 1.072c.41-2.256.476-4.733-.198-7.178c-.587-2.162-1.707-4.04-3.098-5.548zM82.72 82.643a11.84 11.84 0 0 1-1.826 1.572h-.002c-1.8 1.266-3.888 2.22-6.106 3.04l1.654 5.244c2.426-.897 4.917-1.997 7.245-3.635l.006-.005l.003-.002a16.95 16.95 0 0 0 2.639-2.287zm-12.64 6.089c-3.213.864-6.497 1.522-9.821 2.08l.784 5.479c3.421-.575 6.856-1.262 10.27-2.18zm-14.822 2.836c-3.346.457-6.71.83-10.084 1.148l.442 5.522c3.426-.322 6.858-.701 10.285-1.17zm-15.155 1.583c-3.381.268-6.77.486-10.162.67l.256 5.536c3.425-.185 6.853-.406 10.28-.678zm-15.259.92c-2.033.095-4.071.173-6.114.245l.168 5.541a560.1 560.1 0 0 0 6.166-.246z"
            fill="currentColor" stroke="currentColor" fill-rule="evenodd"></path>
        </g>
      </svg>`,
      text: "Rutas"
    },
    {
      id: "lineasIcono",
      href: "https://dosxdos.app.iidos.com/lineas.html",
      icon: `<svg class="w-6 h-6 mr-2" fill="currentColor" stroke="none" viewBox="-3 0 32 32" version="1.1"
        xmlns="http://www.w3.org/2000/svg">
        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
        <g id="SVGRepo_iconCarrier">
          <path
            d="M23.36 9.32c-1.32 0-2.36 1.080-2.36 2.36 0 0.28 0.040 0.56 0.12 0.8l-4.8 4.080c-0.32-0.2-0.72-0.28-1.16-0.28s-0.88 0.12-1.24 0.36l-2.72-2.2c0.080-0.24 0.12-0.44 0.12-0.72 0-1.32-1.080-2.36-2.36-2.36-1.32 0-2.36 1.080-2.36 2.36 0 0.36 0.080 0.68 0.2 0.96l-3.44 3.44c-0.28-0.12-0.64-0.2-0.96-0.2-1.32 0-2.36 1.080-2.36 2.36 0 1.32 1.080 2.36 2.36 2.36s2.36-1.080 2.36-2.36c0-0.36-0.080-0.68-0.2-0.96l3.44-3.44c0.28 0.12 0.64 0.2 0.96 0.2 0.44 0 0.88-0.12 1.24-0.36l2.76 2.12c-0.080 0.24-0.080 0.44-0.080 0.72 0 1.32 1.080 2.36 2.36 2.36s2.36-1.080 2.36-2.36c0-0.28-0.040-0.56-0.12-0.8l4.8-4.080c0.32 0.2 0.72 0.28 1.16 0.28 1.32 0 2.36-1.080 2.36-2.36-0.040-1.2-1.16-2.28-2.44-2.28zM2.36 21c-0.36 0-0.68-0.32-0.68-0.68 0-0.4 0.32-0.68 0.68-0.68s0.68 0.32 0.68 0.68c0 0.36-0.28 0.68-0.68 0.68zM8.24 13.76c0-0.4 0.32-0.68 0.68-0.68s0.68 0.32 0.68 0.68-0.32 0.68-0.68 0.68c-0.36 0-0.68-0.32-0.68-0.68zM15.2 19.28c-0.4 0-0.68-0.32-0.68-0.68s0.32-0.68 0.68-0.68 0.68 0.32 0.68 0.68c-0.040 0.4-0.28 0.68-0.68 0.68zM23.36 12.36c-0.36 0-0.68-0.32-0.68-0.68 0-0.4 0.32-0.68 0.68-0.68 0.4 0 0.68 0.32 0.68 0.68 0 0.4-0.32 0.68-0.68 0.68z">
          </path>
        </g>
      </svg>`,
      text: "Líneas Ruta"
    },
    {
      id: "gastos",
      icon: `<svg viewBox="0 0 24 24" class="w-6 h-6 mr-2" fill="currentColor" stroke="none"
        xmlns="http://www.w3.org/2000/svg">
        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
        <g id="SVGRepo_iconCarrier">
          <path
            d="M5 16C5 15.4477 5.44772 15 6 15H8C8.55229 15 9 15.4477 9 16C9 16.5523 8.55229 17 8 17H6C5.44772 17 5 16.5523 5 16Z">
          </path>
          <path
            d="M11 15C10.4477 15 10 15.4477 10 16C10 16.5523 10.4477 17 11 17H12C12.5523 17 13 16.5523 13 16C13 15.4477 12.5523 15 12 15H11Z">
          </path>
          <path fill-rule="evenodd" clip-rule="evenodd"
            d="M6.788 3C5.96948 2.99999 5.29393 2.99998 4.74393 3.04565C4.17258 3.0931 3.64774 3.19496 3.1561 3.45035C2.42553 3.82985 1.82985 4.42553 1.45035 5.1561C1.19496 5.64774 1.0931 6.17258 1.04565 6.74393C0.999977 7.29393 0.999988 7.96946 1 8.78798V15.212C0.999988 16.0305 0.999977 16.7061 1.04565 17.2561C1.0931 17.8274 1.19496 18.3523 1.45035 18.8439C1.82985 19.5745 2.42553 20.1702 3.1561 20.5497C3.64774 20.805 4.17258 20.9069 4.74393 20.9544C5.29394 21 5.96949 21 6.78803 21H17.212C18.0305 21 18.7061 21 19.2561 20.9544C19.8274 20.9069 20.3523 20.805 20.8439 20.5497C21.5745 20.1702 22.1702 19.5745 22.5497 18.8439C22.805 18.3523 22.9069 17.8274 22.9544 17.2561C23 16.7061 23 16.0305 23 15.212V8.78802C23 7.96949 23 7.29394 22.9544 6.74393C22.9069 6.17258 22.805 5.64774 22.5497 5.1561C22.1702 4.42553 21.5745 3.82985 20.8439 3.45035C20.3523 3.19496 19.8274 3.0931 19.2561 3.04565C18.7061 2.99998 18.0305 2.99999 17.212 3H6.788ZM4.07805 5.22517C4.23663 5.1428 4.46402 5.07578 4.90945 5.03879C5.36686 5.00081 5.95898 5 6.83 5H17.17C18.041 5 18.6331 5.00081 19.0906 5.03879C19.536 5.07578 19.7634 5.1428 19.922 5.22517C20.2872 5.41493 20.5851 5.71277 20.7748 6.07805C20.8572 6.23663 20.9242 6.46402 20.9612 6.90945C20.9857 7.20418 20.9947 7.55484 20.9981 8H3.00194C3.00528 7.55484 3.01431 7.20418 3.03879 6.90945C3.07578 6.46402 3.1428 6.23663 3.22517 6.07805C3.41493 5.71277 3.71277 5.41493 4.07805 5.22517ZM3 10V15.17C3 16.041 3.00081 16.6331 3.03879 17.0906C3.07578 17.536 3.1428 17.7634 3.22517 17.922C3.41493 18.2872 3.71277 18.5851 4.07805 18.7748C4.23663 18.8572 4.46402 18.9242 4.90945 18.9612C5.36686 18.9992 5.95898 19 6.83 19H17.17C18.041 19 18.6331 18.9992 19.0906 18.9612C19.536 18.9242 19.7634 18.8572 19.922 18.7748C20.2872 18.5851 20.5851 18.2872 20.7748 17.922C20.8572 17.7634 20.9242 17.536 20.9612 17.0906C20.9992 16.6331 21 16.041 21 15.17V10H3Z">
          </path>
        </g>
      </svg>`,
      text: "Gastos",
      submenu: [
        {
          href: "https://dosxdos.app.iidos.com/gastos_rutas.html",
          text: "Gastos Rutas"
        },
        {
          href: "",
          text: "Reportar Gastos (próximamente)"
        }
      ]
    }
  ],
  montador: [
    {
      id: "rutasMontador",
      href: "https://dosxdos.app.iidos.com/rutas_montador.html",
      icon: `<svg viewBox="0 0 100 100" class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
        role="img" preserveAspectRatio="xMidYMid meet">
        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
        <g id="SVGRepo_iconCarrier">
          <path
            d="M21 32C9.459 32 0 41.43 0 52.94c0 4.46 1.424 8.605 3.835 12.012l14.603 25.244c2.045 2.672 3.405 2.165 5.106-.14l16.106-27.41c.325-.59.58-1.216.803-1.856A20.668 20.668 0 0 0 42 52.94C42 41.43 32.544 32 21 32zm0 9.812c6.216 0 11.16 4.931 11.16 11.129c0 6.198-4.944 11.127-11.16 11.127c-6.215 0-11.16-4.93-11.16-11.127c0-6.198 4.945-11.129 11.16-11.129z"
            fill="currentColor" stroke="currentColor"></path>
          <path
            d="M87.75 0C81.018 0 75.5 5.501 75.5 12.216c0 2.601.83 5.019 2.237 7.006l8.519 14.726c1.193 1.558 1.986 1.262 2.978-.082l9.395-15.99c.19-.343.339-.708.468-1.082a12.05 12.05 0 0 0 .903-4.578C100 5.5 94.484 0 87.75 0zm0 5.724c3.626 0 6.51 2.876 6.51 6.492c0 3.615-2.884 6.49-6.51 6.49c-3.625 0-6.51-2.875-6.51-6.49c0-3.616 2.885-6.492 6.51-6.492z"
            fill="currentColor" stroke="currentColor"></path>
          <path
            d="M88.209 37.412c-2.247.05-4.5.145-6.757.312l.348 5.532a126.32 126.32 0 0 1 6.513-.303zm-11.975.82c-3.47.431-6.97 1.045-10.43 2.032l1.303 5.361c3.144-.896 6.402-1.475 9.711-1.886zM60.623 42.12a24.52 24.52 0 0 0-3.004 1.583l-.004.005l-.006.002c-1.375.866-2.824 1.965-4.007 3.562c-.857 1.157-1.558 2.62-1.722 4.35l5.095.565c.038-.406.246-.942.62-1.446h.002v-.002c.603-.816 1.507-1.557 2.582-2.235l.004-.002a19.64 19.64 0 0 1 2.388-1.256zM58 54.655l-3.303 4.235c.783.716 1.604 1.266 2.397 1.726l.01.005l.01.006c2.632 1.497 5.346 2.342 7.862 3.144l1.446-5.318c-2.515-.802-4.886-1.576-6.918-2.73c-.582-.338-1.092-.691-1.504-1.068zm13.335 5.294l-1.412 5.327l.668.208l.82.262c2.714.883 5.314 1.826 7.638 3.131l2.358-4.92c-2.81-1.579-5.727-2.611-8.538-3.525l-.008-.002l-.842-.269zm14.867 7.7l-3.623 3.92c.856.927 1.497 2.042 1.809 3.194l.002.006l.002.009c.372 1.345.373 2.927.082 4.525l5.024 1.072c.41-2.256.476-4.733-.198-7.178c-.587-2.162-1.707-4.04-3.098-5.548zM82.72 82.643a11.84 11.84 0 0 1-1.826 1.572h-.002c-1.8 1.266-3.888 2.22-6.106 3.04l1.654 5.244c2.426-.897 4.917-1.997 7.245-3.635l.006-.005l.003-.002a16.95 16.95 0 0 0 2.639-2.287zm-12.64 6.089c-3.213.864-6.497 1.522-9.821 2.08l.784 5.479c3.421-.575 6.856-1.262 10.27-2.18zm-14.822 2.836c-3.346.457-6.71.83-10.084 1.148l.442 5.522c3.426-.322 6.858-.701 10.285-1.17zm-15.155 1.583c-3.381.268-6.77.486-10.162.67l.256 5.536c3.425-.185 6.853-.406 10.28-.678zm-15.259.92c-2.033.095-4.071.173-6.114.245l.168 5.541a560.1 560.1 0 0 0 6.166-.246z"
            fill="currentColor" stroke="currentColor" fill-rule="evenodd"></path>
        </g>
      </svg>
  `, text: "Rutas"
    },
    {
      id: "lineasMontador",
      href: "https://dosxdos.app.iidos.com/ruta_montador.html",
      icon: `
        <svg class="w-6 h-6 mr-2" fill="currentColor" stroke="none" viewBox="-3 0 32 32" version="1.1"
   xmlns="http://www.w3.org/2000/svg">
   <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
   <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
   <g id="SVGRepo_iconCarrier">
     <path
       d="M23.36 9.32c-1.32 0-2.36 1.080-2.36 2.36 0 0.28 0.040 0.56 0.12 0.8l-4.8 4.080c-0.32-0.2-0.72-0.28-1.16-0.28s-0.88 0.12-1.24 0.36l-2.72-2.2c0.080-0.24 0.12-0.44 0.12-0.72 0-1.32-1.080-2.36-2.36-2.36-1.32 0-2.36 1.080-2.36 2.36 0 0.36 0.080 0.68 0.2 0.96l-3.44 3.44c-0.28-0.12-0.64-0.2-0.96-0.2-1.32 0-2.36 1.080-2.36 2.36 0 1.32 1.080 2.36 2.36 2.36s2.36-1.080 2.36-2.36c0-0.36-0.080-0.68-0.2-0.96l3.44-3.44c0.28 0.12 0.64 0.2 0.96 0.2 0.44 0 0.88-0.12 1.24-0.36l2.76 2.12c-0.080 0.24-0.080 0.44-0.080 0.72 0 1.32 1.080 2.36 2.36 2.36s2.36-1.080 2.36-2.36c0-0.28-0.040-0.56-0.12-0.8l4.8-4.080c0.32 0.2 0.72 0.28 1.16 0.28 1.32 0 2.36-1.080 2.36-2.36-0.040-1.2-1.16-2.28-2.44-2.28zM2.36 21c-0.36 0-0.68-0.32-0.68-0.68 0-0.4 0.32-0.68 0.68-0.68s0.68 0.32 0.68 0.68c0 0.36-0.28 0.68-0.68 0.68zM8.24 13.76c0-0.4 0.32-0.68 0.68-0.68s0.68 0.32 0.68 0.68-0.32 0.68-0.68 0.68c-0.36 0-0.68-0.32-0.68-0.68zM15.2 19.28c-0.4 0-0.68-0.32-0.68-0.68s0.32-0.68 0.68-0.68 0.68 0.32 0.68 0.68c-0.040 0.4-0.28 0.68-0.68 0.68zM23.36 12.36c-0.36 0-0.68-0.32-0.68-0.68 0-0.4 0.32-0.68 0.68-0.68 0.4 0 0.68 0.32 0.68 0.68 0 0.4-0.32 0.68-0.68 0.68z">
     </path>
   </g>
 </svg>
`, text: "Líneas"
    },
    {
      id: "historial",
      href: "https://dosxdos.app.iidos.com/historial_montador.html",
      icon: `<path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>`,
      text: "Historial"
    }
  ]
};

function createDesktopNavigation(userRole) {
  // Get desktop navigation container
  const desktopNav = document.querySelector('.xl\\:flex.items-center.space-x-8');
  if (!desktopNav) return;

  // Clear existing items
  desktopNav.innerHTML = '';

  // Get menu items for the current user role
  const menuItems = menuConfigs[userRole] || [];

  // Add CSS for responsive menu behavior
  if (!document.getElementById('responsive-nav-styles')) {
    const styleEl = document.createElement('style');
    styleEl.id = 'responsive-nav-styles';
    styleEl.textContent = `
      /* Monitor container width and adjust dynamically */
      @media (max-width: 1280px) {
        .menu-item-text {
          display: none !important;
        }
        
        .nav-dropdown-arrow {
          display: none !important;
        }
        
        .nav-item svg.mr-2 {
          margin-right: 0 !important;
        }
        
        .nav-item {
          padding: 6px !important;
          display: flex;
          justify-content: center;
        }
        
        .nav-item[data-tooltip]:hover::after {
          content: attr(data-tooltip);
          position: absolute;
          top: 100%;
          left: 50%;
          transform: translateX(-50%);
          background-color: #1F2937;
          color: white;
          font-size: 12px;
          padding: 4px 8px;
          border-radius: 4px;
          z-index: 50;
          white-space: nowrap;
        }
      }
    `;
    document.head.appendChild(styleEl);
  }

  // Create menu items
  menuItems.forEach(item => {
    const element = document.createElement('div');
    element.id = item.id;
    element.className = "nav-item relative";
    element.setAttribute('data-tooltip', item.text);

    if (item.submenu) {
      // Create dropdown menu item
      element.innerHTML = `
        <button
          class="dropdown-toggle flex items-center text-gray-700 hover:text-red-600 transition-colors duration-200 ${item.class || ''}"
          data-dropdown="${item.id}Dropdown">
          ${item.icon}
          <span class="menu-item-text">${item.text}</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1 transition-transform duration-200 nav-dropdown-arrow"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </button>
        <div id="${item.id}Dropdown" class="desktop-dropdown w-auto">
          ${item.submenu.map(subItem => `
            <a href="${subItem.href}" class="text-sm text-gray-700 hover:bg-red-600 hover:text-white ${subItem.class || ''}">
              ${subItem.text}
            </a>
          `).join('')}
        </div>
      `;
    } else {
      // Create regular menu item
      element.innerHTML = `
        <a href="${item.href}" 
          class="flex items-center text-gray-700 hover:text-red-600 transition-colors duration-200 ${item.class || ''}">
          ${item.icon}
          <span class="menu-item-text">${item.text}</span>
        </a>
      `;
    }

    // Add to desktop nav
    desktopNav.appendChild(element);
  });

  // Add notification bell
  const desktopBell = document.createElement('a');
  desktopBell.href = "https://dosxdos.app.iidos.com/notificaciones.html";
  desktopBell.className = "relative z-10";
  desktopBell.id = "desktopBellContainer";
  desktopBell.innerHTML = `
    <img id="bellDesktop" src="https://dosxdos.app.iidos.com/img/bell2.png" class="w-7 text-gray-900 object-contain" />
    <span id="desktopNotificationCount" class="absolute top-0 -right-2 bg-red-600 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[20px] text-center border hidden"></span>
  `;
  desktopNav.appendChild(desktopBell);

  // Add user menu
  const userMenu = document.createElement('div');
  userMenu.className = "relative group drop-shadow";
  userMenu.innerHTML = `
    <button id="userMenuButton"
      class="group flex items-center gap-3 py-1 pl-1.5 pr-4 rounded-full bg-white border border-gray-200 shadow-sm hover:shadow-md hover:border-gray-300 transition-all duration-200"
      aria-expanded="false">
      <div
        class="w-10 h-10 rounded-full overflow-hidden border-2 border-white shadow-sm bg-gray-100 flex items-center justify-center">
        <img id="imagenUsuario" src="https://dosxdos.app.iidos.com/img/usuario.png"
          class="w-full h-full object-cover" alt="Profile"
          onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" />
        <svg class="w-5 h-5 text-gray-400 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor"
          stroke-width="2">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
          <circle cx="12" cy="7" r="4"></circle>
        </svg>
      </div>

      <span id="nombreUsuario" class="text-gray-700 font-medium text-lg"></span>

      <svg xmlns="http://www.w3.org/2000/svg"
        class="w-6 h-6 text-gray-400 group-hover:text-gray-600 transition-transform duration-200 group-hover:rotate-180"
        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polyline points="6 9 12 15 18 9"></polyline>
      </svg>
    </button>

    <!-- Desktop Dropdown Menu -->
    <div id="opcionesUsuario" class="hidden absolute right-0 mt-2 w-max bg-white rounded-lg shadow-lg z-50 py-2">
      <button id="editarUsuario"
        class="flex items-center gap-2 w-full text-left text-lg text-black hover:bg-red-600 hover:text-white transition-colors duration-200 px-4 py-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
          <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
        </svg>
        Editar Usuario
      </button>
      <button id="cerrarSesion"
        class="flex items-center gap-2 w-full text-left text-lg text-red-500 hover:bg-red-600 hover:text-white transition-colors duration-200 px-4 py-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
          <polyline points="16 17 21 12 16 7" />
          <line x1="21" y1="12" x2="9" y2="12" />
        </svg>
        Cerrar Sesión
      </button>
    </div>
  `;
  desktopNav.appendChild(userMenu);
}