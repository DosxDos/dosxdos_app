// Espera a que el DOM esté completamente cargado antes de ejecutar el código
document.addEventListener("DOMContentLoaded", () => {

    // Selecciona todos los checkboxes cuyo id comience con 'revisado_'
    document.querySelectorAll("input[type=checkbox][id^='revisado_']").forEach(checkbox => {

        // Añade un evento que se dispara cada vez que se cambia el estado del checkbox (marcar o desmarcar)
        checkbox.addEventListener("change", () => {

            // Obtiene el ID de la línea asociada desde el atributo data-id del checkbox
            const idLinea = checkbox.getAttribute("data-id");

            // Obtiene el estado actual del checkbox: true (marcado) o false (desmarcado)
            const revisado = checkbox.checked;

            // Envía una solicitud POST a boton_revisada.php con los datos en formato JSON
            fetch('boton_revisada.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json' // Especifica que el cuerpo es JSON
                },
                body: JSON.stringify({ id: idLinea, revisado: revisado }) // Convierte el objeto JS en JSON
            })

            // Cuando se recibe una respuesta, se convierte de JSON a objeto JS
            .then(response => response.json())

            // Procesa la respuesta recibida del servidor
            .then(data => {
                if (data.estado) {
                    // Si el servidor responde con estado = true, todo fue bien
                    console.log("✅ Línea actualizada correctamente");
                } else {
                    // Si hay un error, lo muestra en consola y lanza una alerta
                    console.error("❌ Error al actualizar línea:", data.mensajeError);
                    alert("Error al actualizar estado.");
                }
            });

        });
    });
});
