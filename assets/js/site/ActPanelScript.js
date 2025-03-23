// Objeto global para almacenar los datos
let listaActividades = {};

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

    datos.filas.forEach(function (item) {
        listaActividades[item.Codigo] = item;
    });




    document.getElementById("crearForm").addEventListener("submit", function (event) {
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

    document.getElementById("editarForm").addEventListener("submit", function (event) {
        event.preventDefault(); // Evita el postback

        let formData = new FormData(this); // Captura los datos del formulario

        fetch("../assets/php/UpdAct.php", {
            method: "POST",
            body: formData
        })
            .then(response => response.text()) // Primero obtenemos el texto en bruto
            .then(text => {
                try {
                    console.log(text);
                    let data = JSON.parse(text); // Intentamos convertirlo a JSON
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
    document.getElementById('editdiv').classList.remove('d-none');
    document.getElementById('listpanel').classList.add('d-none');

    // Obtener los datos del item usando el id desde la variable global
    const item = listaActividades[id];

    if (!item) {
        console.error("No se encontró el item con el ID:", id);
        return;
    }

    // Rellenar los campos del formulario con los datos del item
    document.getElementById('codigoE').value = id;
    document.getElementById('nombreE').value = item.Nombre;
    document.getElementById('descripcionE').value = item.Descripcion;
    document.getElementById('fechaE').value = item.Fecha;

    // Cargar imágenes en el div #listImg
    const listImg = document.getElementById('listImg');
    listImg.innerHTML = ''; // Limpiar el contenedor antes de agregar nuevas imágenes


    // Parsear el campo Img (que es un string JSON) a un array de imágenes
    const imagenes = JSON.parse(item.Img);
    console.log(imagenes);
    imagenes.forEach((imagen, index) => {
        const div = document.createElement('div');
        div.className = 'form-check';

        const input = document.createElement('input');
        input.type = 'checkbox';
        input.className = 'form-check-input';
        input.name = 'imgE[]';
        input.value = imagenes[index]; // Usar el índice como valor (o puedes usar un ID único si lo tienes)
        input.id = `imagen${index}`;
        input.checked = true; // Marcar el checkbox por defecto

        const label = document.createElement('label');
        label.className = 'form-check-label';
        label.htmlFor = `imagen${index}`;

        const img = document.createElement('img');
        img.src = `../assets/img/${id.replace(/\D/g, '')}/${imagen}`; // Ajusta la ruta según tu estructura de archivos
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
    console.log('create');
    document.getElementById('creatediv').classList.remove('d-none');
    document.getElementById('listpanel').classList.add('d-none');
}
function closeDiv() {
    console.log('close');
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
