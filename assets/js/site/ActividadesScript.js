// Objeto global para almacenar los datos
let listaActividades = {};

document.addEventListener("DOMContentLoaded", function () {
    // Llamar a la funci�n de verificaci�n de sesi�n al cargar la p�gina
    startSession();
});
// Funci�n para verificar la sesi�n
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
// Funci�n para inicializar el contador despu�s de cargar el HTML
function startPanel(datos) {

    console.log(datos);
    
    const urlParams = new URLSearchParams(window.location.search);
    const buscar = urlParams.get('buscar');
    const activitysGrid = document.getElementById('activitysGrid');

    if (buscar) {
        document.getElementById('buscar').value = buscar;
    }
    datos.filas.forEach(function (item) {
        listaActividades[item.Codigo] = item;
        activitysGrid.innerHTML += createActivityCard(item);
        
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

    // Actualizar la URL y recargar la p�gina
    const url = new URL(window.location);
    url.searchParams.set('buscar', input);
    window.location.href = url;
}

function createActivityCard(activity) {
    console.log(activity);
    const imagenes = JSON.parse(activity.Img);
    const imagen = `../assets/img/${activity.Codigo.replace(/\D/g, '')}/${imagenes[0]}`;
    console.log(imagen);
    return `
                     <div class="col-md-6 col-lg-4">
                         <div class="card activity-card h-100" onclick="showDetails(${activity.Codigo})">
                             <img src="${imagen}" class="activity-image card-img-top" alt="${activity.Codigo}">
                                 <div class="card-body">
                                     <h5 class="card-title">${activity.Nombre}</h5>
                                     <p class="card-text">${activity.Descripcion}</p>
                                 </div>
                         </div>
                     </div>
                     `;
}


// Funci�n para mostrar detalles (modificada para usar la variable global)
function showDetails(activityId) {
    const activity = listaActividades[activityId];
    const imagenes = JSON.parse(activity.Img);
    const imagePath = `../assets/img/${activity.Codigo.replace(/\D/g, '')}/`;
    if (!activity) return;

    document.getElementById('title').classList.add('d-none');
    document.getElementById('activitysGrid').classList.add('d-none');
    document.getElementById('activityDetail').classList.remove('d-none');

    document.getElementById('detailTitle').textContent = activity.Nombre || '';
    document.getElementById('detailText').textContent = activity.Descripcion || '';
    document.getElementById('detailImage').src = imagePath + imagenes[0];  
}

// Funci�n hideDetails se mantiene igual
function hideDetails() {
    // Mostrar nuevamente los t�tulos y tarjetas
    document.getElementById('title').classList.remove('d-none');
    document.getElementById('activitysGrid').classList.remove('d-none');
    document.getElementById('activityDetail').classList.add('d-none');

    // Limpiar contenido del detalle
    document.getElementById('detailTitle').textContent = '';
    document.getElementById('detailText').textContent = '';
    document.getElementById('detailImage').src = '';
}

