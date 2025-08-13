/* FUNCIONES DE INDEXDB */
function db(database, stores) {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(database, 1);
        request.onupgradeneeded = (event) => {
            const db = event.target.result;
            stores.forEach(element => {
                db.createObjectStore(element, {
                    keyPath: 'id',
                    autoIncrement: true
                });
            });
        };
        request.onsuccess = (event) => {
            resolve(true);
        }
        request.onerror = (event) => {
            console.error(`Error en la función db al tratar de abrir o crear la base de datos ${database}: ${event.target.error}`);
            reject(event.target.error);
        }
    })
}

function existDb(dataBase) {
    return new Promise((resolve, reject) => {
        const dbNameToCheck = dataBase;
        indexedDB.databases().then((databases) => {
            const databaseExists = databases.some((db) => db.name === dbNameToCheck);
            if (databaseExists) {
                resolve(true);
            } else {
                resolve(false);
            }
        }).catch((error) => {
            console.error(`Error en la función existDb al verificar la existencia de la base de datos ${dataBase}: ${error.message}`);
            reject(error);
        });
    })
}

function existStore(db, almacen) {
    return new Promise((resolve, reject) => {
        const dbName = db;
        const storeName = almacen;
        const request = indexedDB.open(dbName);
        request.onsuccess = (event) => {
            const db = event.target.result;
            if (db.objectStoreNames.contains(storeName)) {
                resolve(true);
            } else {
                resolve(false);
            }
        };
        request.onerror = (event) => {
            console.error(`Error en la función existStore al abrir la base de datos ${db} para verificar si el almacen ${almacen} existe: ${event.target.error}`);
            reject(event.target.error);
        };

    })
}

function almacenVacio(db, almacen) {
    return new Promise((resolve, reject) => {
        const dbName = db;
        const storeName = almacen;
        const request = indexedDB.open(dbName);
        request.onsuccess = (event) => {
            const db = event.target.result;
            const transaction = db.transaction([storeName], 'readonly');
            const store = transaction.objectStore(storeName);
            const getAllRequest = store.getAll();
            getAllRequest.onsuccess = (event) => {
                const data = event.target.result;
                if (data.length > 0) {
                    resolve(false);
                } else {
                    resolve(true);
                }
            };
            getAllRequest.onerror = (event) => {
                console.error(`Error en la función almacenVacio al verificar si el almacen ${almacen} tiene contenido: ${event.target.error}`);
                reject(event.target.error);
            };
        };
        request.onerror = (event) => {
            console.error(`Error en la función almacenVacio al abrir la base de datos ${db} para verificar si el almacen ${almacen} tiene contenido: ${event.target.error}`);
            reject(event.target.error);
        }
    })
}

function eliminarTokenNotificaciones() {
    return new Promise((resolve) => {
        tokenEliminar = localStorage.getItem("tokenNotificaciones");
        if (tokenEliminar != null) {
            urlTokenEliminar =
                "http://localhost:8080/apirest/rutas_notificaciones.php/notificaciones/token/" +
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

function leerDatos(database, store) {
    return new Promise((resolve, reject) => {
        datos = [];
        const request = indexedDB.open(database);
        request.onsuccess = (event) => {
            const db = event.target.result;
            const transaction = db.transaction(store, 'readonly');
            const almacen = transaction.objectStore(store);
            const cursorRequest = almacen.openCursor();
            cursorRequest.onsuccess = (event) => {
                const cursor = event.target.result;
                if (cursor) {
                    datos.push(cursor.value);
                    cursor.continue();
                } else {
                    resolve(datos);
                }
            };
            cursorRequest.onerror = function (event) {
                console.error(`Error en la función leerDatos al solcitar el cursor para leer el almacen ${store}: ${event.target.error}`);
                reject(event.target.error);
            };
        };
        request.onerror = (event) => {
            console.error(`Error en la función leerDatos al solcitar abrir la base de datos para leer el almacen ${store}: ${event.target.error}`);
            reject(event.target.error);
        }
    })
}

function leerDatos2(database, store) {
    return new Promise((resolve, reject) => {
        datos = [];
        const request = indexedDB.open(database);
        request.onsuccess = (event) => {
            const db = event.target.result;
            const transaction = db.transaction(store, "readonly");
            const almacen = transaction.objectStore(store);
            const cursorRequest = almacen.openCursor();
            cursorRequest.onsuccess = (event) => {
                const cursor = event.target.result;
                if (cursor) {
                    datos.push(cursor.value);
                    cursor.continue();
                } else {
                    resolve(datos);
                }
            };
            cursorRequest.onerror = function (event) {
                console.error(
                    `Error en la función leerDatos al solcitar el cursor para leer el almacen ${store}: ${event.target.error}`
                );
                reject(event.target.error);
            };
        };
        request.onerror = (event) => {
            console.error(
                `Error en la función leerDatos al solcitar abrir la base de datos para leer el almacen ${store}: ${event.target.error}`
            );
            reject(event.target.error);
        };
    });
}

function limpiarDatos(database, store) {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(database);
        request.onsuccess = (event) => {
            const db = event.target.result;
            const transaction = db.transaction(store, 'readwrite');
            const datosStore = transaction.objectStore(store);
            const clearRequest = datosStore.clear();
            clearRequest.onsuccess = (clearEvent) => {
                resolve(true);
            };
            clearRequest.onerror = (event) => {
                console.error(`Error en la función limpiarDatos al solcitar limpiar el almacen ${store}: ${event.target.error}`);
                reject(event.target.error);
            };
        }
        request.onerror = (event) => {
            console.error(`Error en la función limpiarDatos al solcitar abrir la base de datos para limpiar el almacen ${store}: ${event.target.error}`);
            reject(event.target.error);
        }
    })
}

function limpiarDatos2(database, store) {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(database);
        request.onsuccess = (event) => {
            const db = event.target.result;
            const transaction = db.transaction(store, "readwrite");
            const datosStore = transaction.objectStore(store);
            const clearRequest = datosStore.clear();
            clearRequest.onsuccess = (clearEvent) => {
                resolve(true);
            };
            clearRequest.onerror = (event) => {
                console.error(
                    `Error en la función limpiarDatos al solcitar limpiar el almacen ${store}: ${event.target.error}`
                );
                reject(event.target.error);
            };
        };
        request.onerror = (event) => {
            console.error(
                `Error en la función limpiarDatos al solcitar abrir la base de datos para limpiar el almacen ${store}: ${event.target.error}`
            );
            reject(event.target.error);
        };
    });
}

function limpiarAlmacen(database, store) {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(database);
        request.onsuccess = (event) => {
            const db = event.target.result;
            const transaction = db.transaction(store, "readwrite");
            const datosStore = transaction.objectStore(store);
            const clearRequest = datosStore.clear();
            clearRequest.onsuccess = (clearEvent) => {
                resolve(true);
            };
            clearRequest.onerror = (event) => {
                console.error(
                    `Error en la función limpiarDatos al solcitar limpiar el almacen ${store}: ${event.target.error}`
                );
                reject(event.target.error);
            };
        };
        request.onerror = (event) => {
            console.error(
                `Error en la función limpiarDatos al solcitar abrir la base de datos para limpiar el almacen ${store}: ${event.target.error}`
            );
            reject(event.target.error);
        };
    });
}


function agregarDato(database, store, data) {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(database);
        request.onsuccess = (event) => {
            const db = event.target.result;
            const transaction = db.transaction(store, 'readwrite');
            const datosStore = transaction.objectStore(store);
            const clearRequest = datosStore.clear();
            clearRequest.onsuccess = (clearEvent) => {
                const requestAgregar = datosStore.add(data);
                requestAgregar.onsuccess = (event) => {
                    resolve(true);
                };
                requestAgregar.onerror = (event) => {
                    console.error(`Error en la función agregarDato al agregar los datos al almacén ${store}: ${event.target.error}`);
                    reject(event.target.error);
                };
            };
            clearRequest.onerror = (event) => {
                console.error(`Error en la función agregarDato al limpiar el almacén ${store} para ingresar los nuevos datos: ${event.target.error}`);
                reject(event.target.error);
            };
        };
        request.onerror = (event) => {
            console.error(`Error en la función agregarDato al abrir la base de datos ${database} para ingresar los nuevos datos: ${event.target.error}`);
            reject(event.target.error);
        }
    })
}

function agregarDato2(database, store, data) {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(database);
        request.onsuccess = (event) => {
            const db = event.target.result;
            const transaction = db.transaction(store, 'readwrite');
            const datosStore = transaction.objectStore(store);
            const requestAgregar = datosStore.add(data);
            requestAgregar.onsuccess = (event) => {
                resolve(true);
            };
            requestAgregar.onerror = (event) => {
                console.error(`Error en la función agregarDato al agregar los datos al almacén ${store}: ${event.target.error}`);
                reject(event.target.error);
            };
        };
        request.onerror = (event) => {
            console.error(`Error en la función agregarDato al abrir la base de datos ${database} para ingresar los nuevos datos: ${event.target.error}`);
            reject(event.target.error);
        }
    })
}

function agregarDatos(database, store, data) {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(database);
        request.onsuccess = (event) => {
            const db = event.target.result;
            const transaction = db.transaction(store, 'readwrite');
            const datosStore = transaction.objectStore(store);
            const clearRequest = datosStore.clear();
            clearRequest.onsuccess = (clearEvent) => {
                data.forEach(dato => {
                    const requestAgregar = datosStore.add(dato);
                    requestAgregar.onerror = (event) => {
                        console.error(`Error en la función agregarDato al agregar los datos al almacén ${store}: ${event.target.error}`);
                        reject(event.target.error);
                    };
                })
            };
            clearRequest.onerror = (event) => {
                console.error(`Error en la función agregarDato al limpiar el almacén ${store} para ingresar los nuevos datos: ${event.target.error}`);
                reject(event.target.error);
            };
        };
        request.onerror = (event) => {
            console.error(`Error en la función agregarDato al abrir la base de datos ${database} para ingresar los nuevos datos: ${event.target.error}`);
            reject(event.target.error);
        }
        resolve(true);
    })
}

function deleteDB(database) {
    return new Promise((resolve, reject) => {
        const request = indexedDB.deleteDatabase(database);

        // Evento que se dispara cuando la base de datos se elimina correctamente
        request.onsuccess = () => {
            console.log(`Base de datos "${database}" eliminada exitosamente.`);
            resolve(true);
        };

        // Evento que se dispara si ocurre un error al eliminar la base de datos
        request.onerror = (event) => {
            console.error(`Error al eliminar la base de datos "${database}":`, event.target.error);
            reject(event.target.error);
        };

        // Evento que se dispara si la eliminación de la base de datos está bloqueada
        request.onblocked = () => {
            console.warn(`La eliminación de la base de datos "${database}" está bloqueada. Asegúrate de cerrar todas las conexiones a esta base de datos.`);
            reject(new Error(`La eliminación de la base de datos "${database}" está bloqueada.`));
        };
    });
}

function deleteDB2(database) {
    return new Promise((resolve, reject) => {
        const request = indexedDB.deleteDatabase(database);

        // Evento que se dispara cuando la base de datos se elimina correctamente
        request.onsuccess = () => {
            console.log(`Base de datos "${database}" eliminada exitosamente.`);
            resolve(true);
        };

        // Evento que se dispara si ocurre un error al eliminar la base de datos
        request.onerror = (event) => {
            console.error(
                `Error al eliminar la base de datos "${database}":`,
                event.target.error
            );
            reject(event.target.error);
        };
    });
}

async function cerrarSesion() {
    if (navigator.onLine) {
        try {
            loaderOn();
            const eliminarBaseDeDatosCliente = await deleteDB2("dosxdos");
            if (eliminarBaseDeDatosCliente) {
                // Add null check before calling eliminarTokenNotificaciones
                if (typeof eliminarTokenNotificaciones === 'function') {
                    const eliminarTokenDeLasNotificaciones = await eliminarTokenNotificaciones();
                    if (eliminarTokenDeLasNotificaciones) {
                        localStorage.clear();
                        eliminarCookie("login");
                        eliminarCookie("usuario");
                        window.location.href = "http://localhost:8080/index.html";
                    }
                } else {
                    // If the function doesn't exist, proceed anyway
                    localStorage.clear();
                    eliminarCookie("login");
                    eliminarCookie("usuario");
                    window.location.href = "http://localhost:8080/index.html";
                }
            }
        } catch (error) {
            mensaje = "ERROR: " + error.message;
            console.error(error);
            alerta(mensaje);
        }
    } else {
        mensaje = "No es posible cerrar la sesión sin conexión a internet";
        alerta(mensaje);
    }
}

async function cerrarSesions() {
    if (navigator.onLine) {
        try {
            loaderOn();
            const eliminarBaseDeDatosCliente = await deleteDB2("dosxdos");
            if (eliminarBaseDeDatosCliente) {
                // Add null check before calling eliminarTokenNotificaciones
                if (typeof eliminarTokenNotificaciones === 'function') {
                    const eliminarTokenDeLasNotificaciones = await eliminarTokenNotificaciones();
                    if (eliminarTokenDeLasNotificaciones) {
                        localStorage.clear();
                        eliminarCookie("login");
                        eliminarCookie("usuario");
                        window.location.href = "http://localhost:8080/index.html";
                    }
                } else {
                    // If the function doesn't exist, proceed anyway
                    localStorage.clear();
                    eliminarCookie("login");
                    eliminarCookie("usuario");
                    window.location.href = "http://localhost:8080/index.html";
                }
            }
        } catch (error) {
            mensaje = "ERROR: " + error.message;
            console.error(error);
            alerta(mensaje);
        }
    } else {
        mensaje = "No es posible cerrar la sesión sin conexión a internet";
        alerta(mensaje);
    }
}


function eliminarCookie(nombre) {
    const tiempoExpiracion = 1; // Segundo 1 de la Época Unix
    document.cookie = `${nombre}=; expires=${new Date(tiempoExpiracion * 1000).toUTCString()}; path=/;`;
}