// Obtener parámetros de la URL
const urlParams = new URLSearchParams(window.location.search);
const error = urlParams.get('error');

// Seleccionar el contenedor donde mostrarás el mensaje
const errorMessage = document.getElementById('error-message');

if (error) {
    errorMessage.style.display = 'block';
    errorMessage.style.color = 'red'; // Opcional: darle color al texto
    switch (error) {
        case '1':
            errorMessage.textContent = 'Usuario no encontrado.';
            break;
        case '2':
            errorMessage.textContent = 'Contraseña incorrecta.';
            break;
        case '3':
            errorMessage.textContent = 'Error en la base de datos. Inténtalo más tarde.';
            break;
        case '4':
            errorMessage.textContent = 'Error inesperado. Contacta al soporte.';
            break;
        default:
            errorMessage.textContent = 'Error desconocido.';
    }
}
