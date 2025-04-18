// Objeto global para almacenar los datos
let listaActividades = {};
let datosGlobal;

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
    fetch("../assets/php/ActPanel.php?buscar="+buscar, {
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
        datosGlobal = datos;
        // Si la sesión es válida, mostrar el contenido HTML devuelto en el JSON
        document.getElementById('navbaritems').innerHTML = datos.navbar;
        document.getElementById('panel').innerHTML = datos.panel;
        const urlParams = new URLSearchParams(window.location.search);
        const buscar = urlParams.get('buscar');
        if (buscar) {
            document.getElementById('buscar').value = buscar;
        }
        datos.filas.forEach(function (item) {
            listaActividades[item.Codigo] = item;
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

        document.getElementById(datos.name2).addEventListener("submit", function (event) {
            event.preventDefault(); // Evita el postback

            let formData = new FormData(this); // Captura los datos del formulario

            fetch(datos.url2, {
                method: "POST",
                body: formData
            })
                .then(response => response.text())
                .then(text => {
                    try {
                        let data = JSON.parse(text);
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

        document.getElementById(datos.name3).addEventListener("submit", function (event) {
            event.preventDefault(); // Evita el postback

            let formData = new FormData(this); // Captura los datos del formulario

            fetch(datos.url3, {
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
                                document.getElementById("respuestaE").innerHTML = data.ex;
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

    }
   
 
}
function edit(id) {
    console.log(id);
    document.getElementById('editdiv').classList.remove('d-none');
    document.getElementById('listpanel').classList.add('d-none');

    // Obtener los datos del item usando el id desde la variable global
    const item = listaActividades[id];

    if (!item) {
        console.error("No se encontró el item con el ID:", id);
        return;
    }

    // Rellenar los campos del formulario con los datos del item
    document.getElementById(datosGlobal.name4).value = id;
    document.getElementById(datosGlobal.name5).value = item.Nombre;
    document.getElementById(datosGlobal.name6).value = item.Descripcion;
    document.getElementById(datosGlobal.name7).value = item.Fecha;
    if (item.Visible === 1) {
        document.getElementById(datosGlobal.name8).checked = true;
    } else {
        document.getElementById(datosGlobal.name8).checked = false;
    }


    const listImg = document.getElementById(datosGlobal.name9);
    listImg.innerHTML = '';

    const imagenes = JSON.parse(item.Img);
    console.log(imagenes);
    imagenes.forEach((imagen, index) => {
        const div = document.createElement('div');
        div.className = 'form-check';

        const input = document.createElement('input');
        input.type = 'checkbox';
        input.className = 'form-check-input';
        input.name = 'imgE[]';
        input.value = imagenes[index];
        input.id = `imagen${index}`;
        input.checked = true;

        const label = document.createElement('label');
        label.className = 'form-check-label';
        label.htmlFor = `imagen${index}`;

        const img = document.createElement('img');
        img.src = `../assets/img/${id.replace(/\D/g, '')}/${imagen}`;
        img.alt = `Imagen ${index}`;
        img.className = 'img-thumbnail';
        img.style.width = '100px';

        label.appendChild(img);
        div.appendChild(input);
        div.appendChild(label);
        listImg.appendChild(div);
    });
}
function create() {
    document.getElementById('creatediv').classList.remove('d-none');
    document.getElementById('listpanel').classList.add('d-none');
}
function closeDiv() {
    document.getElementById('creatediv').classList.add('d-none');
    document.getElementById('editdiv').classList.add('d-none');
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
function del(id, nombre) {

    $.confirm({
        title: 'Eliminar Actividad?',
        content: 'Actividad: ' + nombre,
        buttons: {
            confirmar: function () {
                fetch(datosGlobal.url4, {
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
