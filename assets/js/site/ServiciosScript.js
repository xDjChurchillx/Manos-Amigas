
// Función para crear las tarjetas de servicio
function createServiceCard(service) {
    return `
                     <div class="col-md-6 col-lg-4">
                         <div class="card service-card h-100" onclick="showDetails(${service.id})">
                             <img src="${service.cardImage}" class="service-image card-img-top" alt="${service.title}">
                                 <div class="card-body">
                                     <h5 class="card-title">${service.title}</h5>
                                     <p class="card-text">${service.description}</p>
                                 </div>
                         </div>
                     </div>
                     `;
}

// Función para cargar los servicios
async function loadServices() {
    try {
        const response = await fetch('assets/json/Servicios.json');
        const data = await response.json();
        const servicesGrid = document.getElementById('servicesGrid');

        // Generar tarjetas
        servicesGrid.innerHTML = data.services.map(service =>
            createServiceCard(service)
        ).join('');

        // Guardar servicios en variable global
        window.services = data.services.reduce((acc, service) => {
            acc[service.id] = service;
            return acc;
        }, {});

    } catch (error) {
        console.error('Error cargando servicios:', error);
        servicesGrid.innerHTML = `
                 <div class="alert alert-danger">
                     Error cargando los servicios. Por favor intenta nuevamente más tarde.
                 </div>
                 `;
    }
}

// Función para mostrar detalles (modificada para usar la variable global)
function showDetails(serviceId) {
    const service = window.services[serviceId];
    if (!service) return;

    document.getElementById('servicesGrid').classList.add('d-none');
    document.getElementById('serviceDetail').classList.remove('d-none');

    document.getElementById('detailTitle').textContent = service.title;
    document.getElementById('detailText').textContent = service.description;
    document.getElementById('detailImage').src = service.image;

    const list = document.getElementById('detailList');
    list.innerHTML = service.details.map(detail => `
             <li class="list-group-item d-flex align-items-center">
                 <span class="badge bg-primary me-3"><i class="bi bi-check2"></i></span>
                 ${detail}
             </li>
 `).join('');
}

// Función hideDetails se mantiene igual
function hideDetails() {
    document.getElementById('servicesGrid').classList.remove('d-none');
    document.getElementById('serviceDetail').classList.add('d-none');
}

// Cargar servicios al iniciar
loadServices();