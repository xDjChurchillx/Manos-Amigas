// Objeto global para almacenar los datos
let listaActividades = {};

document.addEventListener("DOMContentLoaded", function () {
    // Llamar a la función de verificación de sesión al cargar la página
    startPage();
});
// Función para verificar la sesión
function startPage() {
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

    document.getElementById('susAct').addEventListener('submit', function (e) {
        e.preventDefault(); // Evita recargar la página
        const email = e.target.querySelector('input').value;
        alert(`¡Gracias! Tu correo (${email}) ha sido registrado.`);
        // Aquí podrías añadir lógica AJAX/Fetch para enviar el dato a tu backend
    });



}





// Función para inicializar el contador después de cargar el HTML
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


    var activityModal = document.getElementById('activityModal');

    activityModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var activityId = button.getAttribute('data-activity');
        const activity = listaActividades[activityId.toString()];     
        if (!activity) return;
        showDetails(activity);
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

function createActivityCard(activity) {
    const imagenes = JSON.parse(activity.Img);
    return `
            <div class="col-md-6 col-lg-4">
                <div class="card activity-card h-100" data-bs-toggle="modal" data-bs-target="#activityModal" data-activity="${activity.Codigo}">
                    <img src="../assets/img/${activity.Codigo.replace(/\D/g, '')}/${imagenes[0]}" class="activity-image card-img-top" alt="${activity.Codigo}">
                    <div class="card-body">
                        <h5 class="card-title">${activity.Nombre}</h5>
                        <p class="card-text">${activity.Descripcion}</p>
                    </div>
                </div>
            </div>
        `;
}


// Función para mostrar detalles (modificada para usar la variable global)
function showDetails(activity) {

    const imagenes = JSON.parse(activity.Img);
    const imagePath = `../assets/img/${activity.Codigo.replace(/\D/g, '')}/`;
    console.log(activity);
    console.log(imagenes);
    document.getElementById('modalTitle').textContent = activity.Nombre || '';
    document.getElementById('activityDescription').textContent = activity.Descripcion || '';

    const detailImgs = document.getElementById('modalImgs');
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

    // Inicializar el carrusel manualmente
    const carousel = new bootstrap.Carousel(document.getElementById('activityCarousel'), {
        ride: 'carousel'
    });
   
}



