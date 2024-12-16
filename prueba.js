// ConnectionManager.js

class ConnectionManager {
    constructor() {
        // Mapa para almacenar conexiones por nombre de base de datos
        this.connections = new Map();
    }

    /**
     * Abre una conexión a la base de datos y la rastrea.
     * @param {string} database - Nombre de la base de datos.
     * @param {number} [version] - Versión de la base de datos.
     * @returns {Promise<IDBDatabase>} - Promesa que resuelve con la instancia de la base de datos.
     */

    open(database, version) {
        const request = indexedDB.open(database, version);
        const promise = new Promise((resolve, reject) => {
            request.onsuccess = (event) => {
                const db = event.target.result;

                // Si la base de datos ya tiene conexiones rastreadas, obtenemos el conjunto existente
                if (!this.connections.has(database)) {
                    this.connections.set(database, new Set());
                }

                const dbConnections = this.connections.get(database);
                dbConnections.add(db);

                // Escuchar el evento de cierre para eliminar la conexión del conjunto
                db.onclose = () => {
                    dbConnections.delete(db);
                    if (dbConnections.size === 0) {
                        this.connections.delete(database);
                    }
                };

                resolve(db);
            };
            request.onerror = (event) => {
                reject(event.target.error);
            };
            request.onblocked = () => {
                console.warn(`La apertura de la base de datos "${database}" está bloqueada.`);
            };
        });
        return promise;
    }

    /**
     * Cierra todas las conexiones abiertas a una base de datos específica.
     * @param {string} database - Nombre de la base de datos.
     */
    closeAllConnections(database) {
        const dbConnections = this.connections.get(database);
        if (dbConnections) {
            dbConnections.forEach((db) => {
                db.close();
                dbConnections.delete(db);
            });
            this.connections.delete(database);
            console.log(`Todas las conexiones a la base de datos "${database}" han sido cerradas.`);
        } else {
            console.log(`No hay conexiones abiertas a la base de datos "${database}".`);
        }
    }

}

// Exportar una instancia única del ConnectionManager
const connectionManager = new ConnectionManager();
connectionManager.open('dosxdos', 1,)

function cerrarConexiones(databaseName) {
    connectionManager.closeAllConnections(databaseName);
}

// Ejemplo de uso
cerrarConexiones('miBaseDeDatos');


