document.addEventListener("DOMContentLoaded", function () {
    // Funci�n para verificar la sesi�n
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
                    // Si la sesi�n es v�lida, mostrar el nombre del usuario en el panel
                    document.getElementById('panel').innerHTML = `<h1>Bienvenido, ${data.user}</h1>`;
                } else {
                    console.error('Error iniciando la sesi�n:', data.message);
                    window.location.href = 'ingreso.html';
                }
            })
            .catch(error => {
                // En caso de error (fallo en la solicitud o procesamiento), mostrar el error
                console.error('Error al verificar la sesi�n:', error);
                // Redirigir al login
                window.location.href = 'ingreso.html';
            });
    }

   

    // Llamar a la funci�n de verificaci�n de sesi�n al cargar la p�gina
    startSession();
});
