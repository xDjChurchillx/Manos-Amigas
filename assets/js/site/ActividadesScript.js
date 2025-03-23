// Objeto global para almacenar los datos
let listaActividades = {};

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
    fetch("../assets/php/Actividades.php?buscar=" + buscar, {
        method: 'GET'
    })
        .then(response => response.text()) 
        .then(text => {
            try {
                let data = JSON.parse(text); 
                if (data.status === "success") {
                    startPanel(data);
                } else {
                        Alerta("Error al cargar actividades");                    
                }
            } catch (error) {
                console.error("Respuesta:", text); // Imprime el texto antes de que falle
                Alerta("Error al cargar actividades");  
            }
        })
        .catch(error => {          
            Alerta("Main Error al cargar actividades");  
        });
}
// Función para inicializar el contador después de cargar el HTML
function startPanel(datos) {

    console.log(datos);
    
    const urlParams = new URLSearchParams(window.location.search);
    const buscar = urlParams.get('buscar');
    if (buscar) {
        document.getElementById('buscar').value = buscar;
    }
    datos.filas.forEach(function (item) {
        listaActividades[item.Codigo] = item;
        createActivityCard(item);
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

function search() {
    console.log('search');
    const input = document.getElementById('buscar').value;

    // Actualizar la URL y recargar la página
    const url = new URL(window.location);
    url.searchParams.set('buscar', input);
    window.location.href = url;
}












// JavaScript source code
// Función para crear las tarjetas de actividades
function createActivityCard(activity) {
    const imagen = `../assets/img/${activity.Codigo.replace(/\D/g, '')}/${activity.Img[0]}`;
    return `
                     <div class="col-md-6 col-lg-4">
                         <div class="card activity-card h-100" onclick="showDetails(${activity.Codigo})">
                             <img src="${activity.cardImage}" class="activity-image card-img-top" alt="${activity.title}">
                                 <div class="card-body">
                                     <h5 class="card-title">${activity.Nombre}</h5>
                                     <p class="card-text">${activity.Descripcion}</p>
                                 </div>
                         </div>
                     </div>
                     `;
}


// Función para mostrar detalles (modificada para usar la variable global)
function showDetails(activityId) {
    const activity = listaActividades[activityId];
    if (!activity) return;

    document.getElementById('title').classList.add('d-none');
    document.getElementById('activitysGrid').classList.add('d-none');
    document.getElementById('activityDetail').classList.remove('d-none');

    document.getElementById('detailTitle').textContent = activity.title || '';
    document.getElementById('detailText').textContent = activity.description || '';
    document.getElementById('detailImage').src = activity.image || '';

    const list = document.getElementById('detailList');
    list.innerHTML = Array.isArray(activity.details) && activity.details.length > 0
        ? activity.details.map(detail => `
            <li class="list-group-item d-flex align-items-center">
                <span class="badge bg3color me-3"><i class="bi bi-check2"></i></span>
                ${detail}
            </li>
        `).join('')
        : '';
}

// Función hideDetails se mantiene igual
function hideDetails() {
    // Mostrar nuevamente los títulos y tarjetas
    document.getElementById('title').classList.remove('d-none');
    document.getElementById('activitysGrid').classList.remove('d-none');
    document.getElementById('activityDetail').classList.add('d-none');

    // Limpiar contenido del detalle
    document.getElementById('detailTitle').textContent = '';
    document.getElementById('detailText').textContent = '';
    document.getElementById('detailImage').src = '';
    document.getElementById('detailList').innerHTML = '';
}

