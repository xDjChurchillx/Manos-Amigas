document.addEventListener("DOMContentLoaded", function () {
    // Funci�n para verificar la sesi�n
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
                    // Si la sesi�n es v�lida, mostrar el nombre del usuario en el panel
                    document.getElementById('panel').innerHTML = `<h1>Bienvenido, ${data.user}</h1>`;
                } else {
                    // Si la sesi�n no es v�lida, manejar el error y redirigir al login
                    handleError(data.message);
                }
            })
            .catch(error => {
                // En caso de error (fallo en la solicitud o procesamiento), mostrar el error
                console.error('Error al verificar la sesi�n:', error);
                handleError(error.message || 'Error inesperado. Por favor intenta m�s tarde.');
            });
    }

    // Funci�n para manejar errores y mostrar mensajes
    function handleError(errorMessage) {
        // Mostrar mensaje de error en consola (para debug)
        console.log("Error:", errorMessage);

        // Puedes personalizar este mensaje como un modal o un elemento en el DOM
        alert(errorMessage);

        // Redirigir al login
        window.location.href = 'ingreso.html';
    }

    // Llamar a la funci�n de verificaci�n de sesi�n al cargar la p�gina
    checkSession();
});
