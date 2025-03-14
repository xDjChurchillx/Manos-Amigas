document.addEventListener("DOMContentLoaded", function () {
    // Función para verificar la sesión
    function checkSession() {
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
                    // Si la sesión no es válida, manejar el error y redirigir al login
                    handleError(data.message);
                }
            })
            .catch(error => {
                // En caso de error (fallo en la solicitud o procesamiento), mostrar el error
                console.error('Error al verificar la sesión:', error);
                handleError(error.message || 'Error inesperado. Por favor intenta más tarde.');
            });
    }

    // Función para manejar errores y mostrar mensajes
    function handleError(errorMessage) {
        // Mostrar mensaje de error en consola (para debug)
        console.log("Error:", errorMessage);

        // Puedes personalizar este mensaje como un modal o un elemento en el DOM
        alert(errorMessage);

        // Redirigir al login
        window.location.href = 'ingreso.html';
    }

    // Llamar a la función de verificación de sesión al cargar la página
    checkSession();
});
