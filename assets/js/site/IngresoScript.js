// Obtener parámetros de la URL
const urlParams = new URLSearchParams(window.location.search);
const error = urlParams.get('error');

// Seleccionar el contenedor donde mostrarás el mensaje
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
            Alerta('Error en la base de datos. Inténtalo más tarde.');
            break;
        case '4':
            Alerta('Error inesperado. Contacta al soporte.');
            break;
        case '5':
            const modalElement = document.getElementById('successModal');
            const modalsuccess = new bootstrap.Modal(modalElement);
            modalsuccess.show();
            break;
        case '6':
            // success
            break;
        case '7':
            // no coincide el input del recover
            break;
        case '8':
            // Atajar token
            document.getElementById('correoR').value = urlParams.get('correo');
            document.getElementById('tokenR').value = urlParams.get('token');
            const modalElement2 = document.getElementById('verifyModal');
            const modalverify = new bootstrap.Modal(modalElement2);
            modalverify.show();
            break;
        case '9':
            // modal contrase;a vieja invalida
            break;
        case '10':
            // modal success
            break;
        default:
            Alerta('Error desconocido.');
    }
}
function recover() {
    const modalElement = document.getElementById('recoverModal');
    const modalsuccess = new bootstrap.Modal(modalElement);
    modalsuccess.show();
}