// Objeto global para almacenar los datos
let listaDonaciones = {};
let urlAux;

document.addEventListener("DOMContentLoaded", function () {
    // Llamar a la función de verificación de sesión al cargar la página
    startSession();
});
// Función para verificar la sesión
function startSession() {
    const urlParams = new URLSearchParams(window.location.search);
    let buscar = urlParams.get('buscar');
    if (!buscar) {
        buscar = '';
    }
    // Realizar la solicitud AJAX
    fetch("../assets/php/DonaPanel.php?buscar=" + buscar, {
        method: 'GET',
        credentials: 'same-origin'
    })
        .then(response => response.text())
        .then(text => {
            try {
                let data = JSON.parse(text);
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
    try {
        console.log(datos);
        urlAux = datos.url2;
        // Si la sesión es válida, mostrar el contenido HTML devuelto en el JSON
        document.getElementById('navbaritems').innerHTML = datos.navbar;
        document.getElementById('panel').innerHTML = datos.panel;
        const urlParams = new URLSearchParams(window.location.search);
        const buscar = urlParams.get('buscar');
        if (buscar) {
            document.getElementById('buscar').value = buscar;
        }
        datos.filas.forEach(function (item) {
            listaDonaciones[item.Codigo] = item;
        });
        document.getElementById(datos.name0).addEventListener("submit", function (event) {
            event.preventDefault(); // Evita el postback
            let formData = new FormData(this); // Captura los datos del formulario
            fetch(datos.url1, {
                method: "POST",
                body: formData
            }) 
                .then(response => response.text())
                .then(text => {
                    try {
                        console.log(text);
                        let data = JSON.parse(text);
                        if (data.status === "success") {
                            location.reload();
                        } else {
                            if ("ex" in data) {
                                document.getElementById(datos.name1).innerHTML = data.ex;
                            } else {
                                Alerta("Error al actualizar la actividad.");
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
       
    } catch (e) {
        console.error("Error iniciando panel:", e); // Imprime el texto antes de que falle
        Alerta("Error inesperado: " + e); // Opcional: mostrar el error en un alert
    }
   

}
function edit(id) {
    console.log(id);
    document.getElementById('detaildiv').classList.remove('d-none');
    document.getElementById('listpanel').classList.add('d-none');

    // Obtener los datos del item usando el id desde la variable global
    const item = listaDonaciones[id];

    if (!item) {
        console.error("No se encontró el item con el ID:", id);
        return;
    }

    // Rellenar los campos del formulario con los datos del item
    document.getElementById('codigoD').value = item.Codigo || "";
    document.getElementById('metodoD').value = item.Metodo || "";
    document.getElementById('destinoD').value = item.Destino || "";
    document.getElementById('nombreD').value = item.Nombre || "";
    document.getElementById('contactoD').value = item.Contacto || "";
    document.getElementById('mensajeD').value = item.Mensaje || "";
    document.getElementById('fechaD').value = item.Fecha || "";
    

}
function closeDiv() {
    console.log('close');
    document.getElementById('detaildiv').classList.add('d-none');
    document.getElementById('listpanel').classList.remove('d-none');
}
function search() {
    console.log('search');
    const input = document.getElementById('buscar').value;

    // Actualizar la URL y recargar la página
    const url = new URL(window.location);
    url.searchParams.set('buscar', input);
    window.location.href = url;
}
function del(id) {

    $.confirm({
        title: 'Eliminar Donacion?',
        content: 'Donacion: ' + id,
        buttons: {
            confirmar: function () {
                fetch(urlAux, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        codigo: id
                    })
                })
                    .then(response => response.text())
                    .then(text => {
                        try {
                            let data = JSON.parse(text);
                            if (data.status === 'success') {
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
