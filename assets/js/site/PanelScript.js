document.addEventListener("DOMContentLoaded", function () {
    // Función para verificar la sesión
    function startSession() {
        // Realizar la solicitud AJAX
        fetch('../assets/php/panel.php')
            .then(response => {
                // Verificar si la respuesta fue exitosa
                if (!response.ok) {
                    throw new Error('Error al conectar con el servidor');
                }
                return response.json();
            })
            .then(data => {
                // Verificar si la respuesta contiene un estado exitoso
                if (data.status === "success") {
                    // Si la sesión es válida, mostrar el nombre del usuario en el panel
                    document.getElementById('panel').innerHTML = `<h1>Bienvenido, ${data.user}</h1>`;
                } else {
                    console.error('Error iniciando la sesión:', data.message);
                    window.location.href = 'ingreso.html';
                }
            })
            .catch(error => {
                // En caso de error (fallo en la solicitud o procesamiento), mostrar el error
                console.error('Error al verificar la sesión:', error);
                // Redirigir al login
                window.location.href = 'ingreso.html';
            });
    }

   

    // Llamar a la función de verificación de sesión al cargar la página
    startSession();
});
