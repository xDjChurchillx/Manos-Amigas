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

    document.getElementById('susAct').addEventListener('click', function (e) {
        const email = document.getElementById('correo').value;
        alert(`Tu correo (${email}) ha sido suscrito.`);
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
    let fechaHTML = ''; // Variable para la parte del calendario

    if (activity.Fecha != '0000-00-00 00:00:00') {
        // Extraer datos de la fecha manualmente
        const fechaPartes = activity.Fecha.split(" ");
        const fecha = fechaPartes[0].split("-"); // ["2025", "03", "23"]
        const horaPartes = fechaPartes[1].split(":"); // ["07", "05", "00"]

        // Meses en español
        const mesesAbrev = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
        const mesAbrev = mesesAbrev[parseInt(fecha[1], 10) - 1]; // Convertir el mes a índice del array
        const dia = fecha[2];
        const hora = horaPartes[0];
        const minutos = horaPartes[1];

        // Construir la parte del calendario
        fechaHTML = `
            <div class="minimal-calendar">
                <div class="minimal-calendar-header bg1gradient">${mesAbrev}</div>
                <div class="minimal-calendar-day">${dia}</div>
                <div class="minimal-calendar-time">
                    <span class="minimal-clock-icon">🕒</span>
                    <span>${hora}:${minutos}</span>
                </div>
            </div>
        `;
    }

    return `
        <div class="col-md-6 col-lg-4">
            <div class="simple-card" data-bs-toggle="modal" data-bs-target="#activityModal" data-activity="${activity.Codigo}">               
                <img src="../assets/img/${activity.Codigo.replace(/\D/g, '')}/${imagenes[0]}" class="activity-image card-img-top" alt="${activity.Codigo}">            
                <div class="card-header bg-white">
                    <h3 class="card-title">${activity.Nombre}</h3>
                    ${fechaHTML} <!-- Se agrega solo si hay fecha -->
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

    if (activity.Fecha != '0000-00-00 00:00:00') {
        const [fecha, hora] = activity.Fecha.split(' '); // Separar la fecha y la hora
        const [anio, mes, dia] = fecha.split('-'); // Extraer año, mes y día
        const [horas, minutos] = hora.split(':'); // Extraer horas y minutos

        // Meses en español
        const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

        const fechaFormateada = `${dia} de ${meses[parseInt(mes, 10) - 1]} de ${anio}`;
        const horaFormateada = `${horas}:${minutos}`;

        document.getElementById('modalDateTime').textContent = `${fechaFormateada} - ${horaFormateada}`;
    } else {
        document.getElementById('modalDateTime').textContent = '';
    }


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

    // Inicializar el carrusel manualmente
    const carousel = new bootstrap.Carousel(document.getElementById('activityCarousel'), {
        ride: 'carousel'
    });
   
}



