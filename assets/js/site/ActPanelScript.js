document.addEventListener("DOMContentLoaded", function () {
   // Llamar a la función de verificación de sesión al cargar la página
    startSession();
});
// Función para verificar la sesión
function startSession() {
    // Realizar la solicitud AJAX
    fetch("../assets/php/ActPanel.php", {
        method: 'GET',
        credentials: 'same-origin'
    })
        .then(response => response.text()) // Primero obtenemos el texto en bruto
        .then(text => {
            try {
                let data = JSON.parse(text); // Intentamos convertirlo a JSON
                if (data.status === "success") {
                    startPanel(data);
                } else {
                    if ("ex" in data) {
                        Alerta(data.ex);
                    } else {
                        Alerta("Error");
                    }
                    if ("redirect" in data) {
                       // window.location.href = data.redirect;
                    }
                }
            } catch (error) {
                console.error("La respuesta no es JSON:", text); // Imprime el texto antes de que falle
                Alerta("Error inesperado: " + text); // Opcional: mostrar el error en un alert
            }
        })
        .catch(error => {
            // En caso de error (fallo en la solicitud o procesamiento), mostrar el error
            console.error('Error al verificar la sesión:', error);
            // Redirigir al login en caso de un fallo
            // window.location.href = 'ingreso.html?error=1';
        });
}
// Función para inicializar el contador después de cargar el HTML
function startPanel(datos) {

    console.log(datos);  
    // Si la sesión es válida, mostrar el contenido HTML devuelto en el JSON
    document.getElementById('navbaritems').innerHTML = datos.navbar;
    document.getElementById('panel').innerHTML = datos.panel;

    document.getElementById("actividadForm").addEventListener("submit", function (event) {
        event.preventDefault(); // Evita el postback

        let formData = new FormData(this); // Captura los datos del formulario

        fetch("../assets/php/AddAct.php", {
            method: "POST",
            body: formData
        })
            .then(response => response.text()) // Primero obtenemos el texto en bruto
            .then(text => {
                try {
                    let data = JSON.parse(text); // Intentamos convertirlo a JSON
                    if (data.status === "success") {
                        location.reload();
                    } else {
                        if ("ex" in data) {
                            document.getElementById("respuesta").innerHTML = data.ex;
                        } else {
                            Alerta("Error al agregar la actividad.");
                        }
                        if ("redirect" in data) {
                            window.location.href = data.redirect;
                        }
                    }
                } catch (error) {
                    console.error("La respuesta no es JSON:", text); // Imprime el texto antes de que falle
                    Alerta("Error inesperado: " + text); // Opcional: mostrar el error en un alert
                }
            })
            .catch(error => console.error("Error en la solicitud:", error));
    });
 
}
function Alerta(mensaje) {
    const alertaDiv = document.getElementById('alerta');
    alertaDiv.textContent = mensaje;
    alertaDiv.classList.remove('d-none');
    setTimeout(() => {
        alertaDiv.classList.add('d-none');
    }, 5000);
}
function edit(id) {
	console.log(id);
}
function create() {
    console.log('create');
    document.getElementById('creatediv').classList.remove('d-none');
    document.getElementById('listpanel').classList.add('d-none');
}
function closeDiv() {
    console.log('create');
    document.getElementById('creatediv').classList.add('d-none');
    document.getElementById('listpanel').classList.remove('d-none');
}
function search() {
    console.log('create');
}
function del(id,nombre) {

    $.confirm({
        title: 'Eliminar Actividad?',
        content: 'Actividad: '+nombre,
        buttons: {
            confirmar: function () {
                fetch('../assets/php/DelAct.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        codigo: id
                    })
                })
                    .then(response => response.text()) // Primero obtenemos el texto en bruto
                    .then(text => {
                        try {
                            let data = JSON.parse(text); // Intentamos convertir el texto a JSON
                            if (data.status === 'success') {
                                //    Alerta(data.mensaje); // Actividad eliminada exitosamente
                                location.reload();
                            } else {
                                if ("ex" in data) {
                                    Alerta("Error: " + data.ex);
                                } else {
                                    Alerta("Error al eliminar");
                                }
                                if ("redirect" in data) {
                                    window.location.href = data.redirect;
                                }
                            }
                        } catch (error) {
                            console.error('La respuesta no es JSON:', text); // Imprime el texto antes de que falle
                            Alerta('Error inesperado: ' + text); // Mostrar el error en un alert
                        }
                    })
                    .catch(error => {
                        console.error('Error en la solicitud:', error);
                        Alerta('Error: Ocurrió un problema al procesar la solicitud');
                    });
            },
            cancelar: function () {
               
            }
        }
    });
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
                Alerta(data.ex);
            }
            if ("redirect" in data) {
                window.location.href = data.redirect;
            }
        }
    } catch (error) {
        console.error('Error al cargar los datos:', error);
        Alerta('Hubo un error al cargar los datos');
    }
}
