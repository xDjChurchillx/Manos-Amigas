document.addEventListener("DOMContentLoaded", function () {
    // Funci�n para verificar la sesi�n
    function startSession() {
        // Realizar la solicitud AJAX
        fetch('../assets/php/panel.php', {
            method: 'GET',
            credentials: 'same-origin',  // Mantener la sesi�n activa si es posible
        })
            .then(response => {
                // Verificamos que la respuesta sea exitosa antes de convertirla en JSON
                if (!response.ok) {
                    throw new Error('Error en la solicitud');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'error') {
                    // Si la sesi�n no es v�lida, redirigir al usuario
                    window.location.href = data.redirect;
                } else if (data.status === 'success') {
                    // Si la sesi�n es v�lida, mostrar el contenido HTML devuelto en el JSON
                    document.getElementById('panel').innerHTML = data.html;
                }
            })
            .catch(error => {
                // En caso de error (fallo en la solicitud o procesamiento), mostrar el error
                console.error('Error al verificar la sesi�n:', error);
                // Redirigir al login en caso de un fallo
                window.location.href = 'ingreso.html';
            });
    }

    // Llamar a la funci�n de verificaci�n de sesi�n al cargar la p�gina
    startSession();
});
