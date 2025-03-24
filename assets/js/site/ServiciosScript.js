// Objeto global para almacenar los datos
let listaActividades = {};

document.addEventListener("DOMContentLoaded", function () {
    // Llamar a la función de verificación de sesión al cargar la página
    startPage();
});
// Función para verificar la sesión
function startPage() {
    // Realizar la solicitud AJAX
    fetch("../assets/json/Servicios.json", {
        method: 'GET'
    })
        .then(response => response.text())
        .then(text => {
            try {
                let data = JSON.parse(text);
                startPanel(data);              
            } catch (error) {
                console.error("Respuesta:", text); // Imprime el texto antes de que falle
                Alerta("Error al cargar Servicios");
            }
        })
        .catch(error => {
            Alerta("Main Error al cargar Servicios");
        });

}

// Función para inicializar el contador después de cargar el HTML
function startPanel(datos) {

    console.log(datos);

    const servicesGrid = document.getElementById('servicesGrid');

    
    datos.services.forEach(function (item) {
        listaActividades[item.id] = item;
        servicesGrid.innerHTML += createServiceCard(item);
    });


    var serviceModal = document.getElementById('serviceModal');

    serviceModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var serviceId = button.getAttribute('data-service');
        const service = listaActividades[serviceId.toString()];
        if (!service) return;
        showDetails(service);
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

function createServiceCard(service) {
    return `
                     <div class="col-md-6 col-lg-4">
                         <div class="card service-card h-100" data-bs-toggle="modal" data-bs-target="#serviceModal" data-service="${service.id}">
                             <img src="${service.cardImage}" class="service-image card-img-top" alt="${service.title}">
                                 <div class="card-body">
                                     <h5 class="card-title">${service.title}</h5>
                                     <p class="card-text">${service.description}</p>
                                 </div>
                         </div>
                     </div>
                     `;
}

// Función para mostrar detalles (modificada para usar la variable global)
function showDetails(service) {

    const imagenes = JSON.parse(service.Img);
    const imagePath = `../assets/img/${service.Codigo.replace(/\D/g, '')}/`;
    console.log(service);
    console.log(imagenes);
    document.getElementById('modalTitle').textContent = service.Nombre || '';
    document.getElementById('serviceDescription').textContent = service.Descripcion || '';

    const detailImgs = document.getElementById('modalImgs');
    detailImgs.innerHTML = '';
    // Agregar las imágenes al carrusel
    imagenes.forEach((img, index) => {
        const carouselItem = document.createElement('div');
        carouselItem.classList.add('carousel-item');
        if (index === 0) {
            carouselItem.classList.add('active'); // La primera imagen estará activa
        }

        const imgElement = document.createElement('img');
        imgElement.src = imagePath + img;
        imgElement.classList.add('d-block', 'w-100');
        imgElement.alt = img;

        carouselItem.appendChild(imgElement);
        detailImgs.appendChild(carouselItem);
    });

    
}








































function showDetails(serviceId) {
    const service = window.services[serviceId];
    if (!service) return;

    document.getElementById('title').classList.add('d-none');
    document.getElementById('servicesGrid').classList.add('d-none');
    document.getElementById('serviceDetail').classList.remove('d-none');

    document.getElementById('detailTitle').textContent = service.title || '';
    document.getElementById('detailText').textContent = service.description || '';
    document.getElementById('detailImage').src = service.image || '';

    const list = document.getElementById('detailList');
    list.innerHTML = Array.isArray(service.details) && service.details.length > 0
        ? service.details.map(detail => `
            <li class="list-group-item d-flex align-items-center">
                <span class="badge bg3color me-3"><i class="bi bi-check2"></i></span>
                ${detail}
            </li>
        `).join('')
        : '';
}

