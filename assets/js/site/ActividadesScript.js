// JavaScript source code
// Función para crear las tarjetas de actividades
function createActivityCard(activity) {
    return `
                     <div class="col-md-6 col-lg-4">
                         <div class="card activity-card h-100" onclick="showDetails(${activity.id})">
                             <img src="${activity.cardImage}" class="activity-image card-img-top" alt="${activity.title}">
                                 <div class="card-body">
                                     <h5 class="card-title">${activity.title}</h5>
                                     <p class="card-text">${activity.description}</p>
                                 </div>
                         </div>
                     </div>
                     `;
}

// Función para cargar los servicios
// Función para cargar los servicios desde PHP
async function loadActivitys() {
    try {
        const response = await fetch('assets/php/Actividades.php');
        const data = await response.json();
        const activitysGrid = document.getElementById('activitysGrid');

        // Generar tarjetas
        activitysGrid.innerHTML = data.activitys.map(activity =>
            createActivityCard(activity)
        ).join('');

        // Guardar servicios en variable global
        window.activitys = data.activitys.reduce((acc, activity) => {
            acc[activity.id] = activity;
            return acc;
        }, {});

    } catch (error) {
        console.error('Error cargando servicios:', error);
        activitysGrid.innerHTML = `
                 <div class="alert alert-danger">
                     Error cargando los servicios. Por favor intenta nuevamente más tarde.
                 </div>
                 `;
    }
}
// Función para mostrar detalles (modificada para usar la variable global)
function showDetails(activityId) {
    const activity = window.activitys[activityId];
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


// Cargar servicios al iniciar
loadActivitys();