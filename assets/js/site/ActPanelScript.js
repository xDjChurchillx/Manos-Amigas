document.addEventListener("DOMContentLoaded", function () {
    // Función para verificar la sesión
    function startSession() {
        // Realizar la solicitud AJAX
        fetch('../assets/php/ActPanel.php', {
            method: 'GET',
            credentials: 'same-origin',  // Mantener la sesión activa si es posible
        })
            .then(response => {
                // Verificamos que la respuesta sea exitosa antes de convertirla en JSON
                if (!response.ok) {
                    // En caso de error (fallo en la solicitud o procesamiento), mostrar el error
                    console.log('Error en la solicitud');
                    // Redirigir al login en caso de un fallo
                    window.location.href = 'ingreso.html';
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'error') {
                    // Si la sesión no es válida, redirigir al usuario
                    if ("ex" in data) {
                        alert(data.ex);
                    }
                    if ("ex" in redirect) {
                        window.location.href = data.redirect;
                    }
                } else if (data.status === 'success') {                 
                    // Llamar al contador después de que el HTML se haya cargado
                    startPanel(data);
                }
            })
            .catch(error => {
                // En caso de error (fallo en la solicitud o procesamiento), mostrar el error
                console.error('Error al verificar la sesión:', error);
                // Redirigir al login en caso de un fallo
                window.location.href = 'ingreso.html?error=3';
            });
    }

    // Llamar a la función de verificación de sesión al cargar la página
    startSession();
});

// Función para inicializar el contador después de cargar el HTML
function startPanel(datos) {

    console.log(datos);  
    // Si la sesión es válida, mostrar el contenido HTML devuelto en el JSON
    document.getElementById('navbaritems').innerHTML = datos.navbar;
    document.getElementById('panel').innerHTML = datos.panel;
   
 
}

async function actualizarDatos(val) {
 
    try {
        // Enviar los datos al PHP usando fetch
        const response = await fetch('../assets/php/panel.php', {
            method: 'POST', // Usar POST para enviar los datos
            headers: {
                'Content-Type': 'application/json' // Indicar que el cuerpo es JSON
            },
            body: JSON.stringify(datos) // Convertir el objeto a JSON
        });

        // Verificar si la respuesta es exitosa
        if (!response.ok) {
            throw new Error('Error en la solicitud');
        }

        // Procesar la respuesta JSON
        const respuesta = await response.json(); // 👈 Cambiado a "respuesta"
        console.log('Respuesta del servidor:', respuesta);

        // Aquí puedes manejar la respuesta del servidor
        if (respuesta.status === 'success') {
          //  alert('Datos correctamente');
        } else {
            if ("ex" in data) {
                alert(data.ex);
            }
            if ("ex" in redirect) {
                window.location.href = data.redirect;
            }
        }
    } catch (error) {
        console.error('Error al cargar los datos:', error);
        alert('Hubo un error al cargar los datos');
    }
}
