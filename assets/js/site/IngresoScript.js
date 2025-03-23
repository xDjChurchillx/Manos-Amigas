// Obtener par�metros de la URL
const urlParams = new URLSearchParams(window.location.search);
const error = urlParams.get('error');

// Seleccionar el contenedor donde mostrar�s el mensaje
const errorMessage = document.getElementById('error-message');

if (error) {
    errorMessage.style.display = 'block';
    errorMessage.style.color = 'red'; 
    switch (error) {
        case '1':
            errorMessage.textContent = 'Credenciales invalidas.';
            break;
        case '2':
            errorMessage.textContent = 'Porfavor introducir credenciales.';
            break;
        case '3':
            errorMessage.textContent = 'Error en la base de datos. Int�ntalo m�s tarde.';
            break;
        case '4':
            errorMessage.textContent = 'Error inesperado. Contacta al soporte.';
            break;
        default:
            errorMessage.textContent = 'Error desconocido.';
    }
}
