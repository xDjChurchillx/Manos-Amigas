// Obtener parámetros de la URL
const urlParams = new URLSearchParams(window.location.search);
const error = urlParams.get('error');

// Seleccionar el contenedor donde mostrarás el mensaje
const errorMessage = document.getElementById('error-message');
const errorRecover = document.getElementById('error-recover');
const errorVerify = document.getElementById('error-verify');

const modalElement = document.getElementById('successModal');
const modalsuccess = new bootstrap.Modal(modalElement);
const modalElement2 = document.getElementById('recoverModal');
const modalrecover = new bootstrap.Modal(modalElement2);
const modalElement3 = document.getElementById('verifyModal');
const modalverify = new bootstrap.Modal(modalElement3);
if (error) {   
    switch (error) {
        case '1':
            errorMessage.style.display = 'block';
            errorMessage.style.color = 'red'; 
            errorMessage.textContent = 'Credenciales invalidas.';
            break;
        case '2':
            errorMessage.style.display = 'block';
            errorMessage.style.color = 'red'; 
            errorMessage.textContent = 'Porfavor introducir credenciales.';
            break;
        case '3':
            Alerta('Error en la base de datos. Inténtalo más tarde.');
            break;
        case '4':
            Alerta('Error inesperado. Contacta al soporte.');
            break;
        case '5':
            //sucess
            modalsuccess.show();
            break;
        case '6':
            // success
            break;
        case '7':
            // no coincide el input del recover
            errorRecover.style.display = 'block';
            errorRecover.style.color = 'red';
            errorRecover.textContent = 'Usuario o correo invalidos.';
            modalrecover.show();
            break;
        case '8':
            // Atajar token
            if (urlParams.get('correo')) {
              document.getElementById('correoR').value = urlParams.get('correo');
            }
            if (urlParams.get('token')) {
                document.getElementById('tokenR').value = urlParams.get('token');           
            }           
            modalverify.show();
            break;
        case '9':
            errorRecover.style.display = 'block';
            errorRecover.style.color = 'red';
            errorRecover.textContent = 'Porfavor introducir correo o usuario.';
            modalrecover.show();
            break;
        case '10':
            // Atajar token
            if (urlParams.get('correo')) {
                document.getElementById('correoR').value = urlParams.get('correo');
            }
            if (urlParams.get('token')) {
                document.getElementById('tokenR').value = urlParams.get('token');
            } 
            errorVerify.style.display = 'block';
            errorVerify.style.color = 'red';
            errorVerify.textContent = 'Porfavor introducir los datos que se solicitan';
            modalverify.show();
            break;
        case '11':
            // Atajar token
            if (urlParams.get('correo')) {
                document.getElementById('correoR').value = urlParams.get('correo');
            }
            if (urlParams.get('token')) {
                document.getElementById('tokenR').value = urlParams.get('token');
            } 
            errorVerify.style.display = 'block';
            errorVerify.style.color = 'red';
            errorVerify.textContent = 'Las contraseñas no coinciden';
            modalverify.show();
            break;
        case '12':
            // modal success
            break;
        case '13':
            // Atajar token
            if (urlParams.get('correo')) {
                document.getElementById('correoR').value = urlParams.get('correo');
            }
            if (urlParams.get('token')) {
                document.getElementById('tokenR').value = urlParams.get('token');
            } 
            errorVerify.style.display = 'block';
            errorVerify.style.color = 'red';
            errorVerify.textContent = 'Token o correo invalido';
            modalverify.show();
            break;
        case '14':
            // Atajar token
            if (urlParams.get('correo')) {
                document.getElementById('correoR').value = urlParams.get('correo');
            }
            if (urlParams.get('token')) {
                document.getElementById('tokenR').value = urlParams.get('token');
            } 
            errorVerify.style.display = 'block';
            errorVerify.style.color = 'red';
            errorVerify.textContent = 'Formato de Nueva contraseña incorrecto(de 10 a 20 caracteres)';
            modalverify.show();
            break;
        case '15':
            // cuenta bloqueada
            errorMessage.style.display = 'block';
            errorMessage.style.color = 'red';
            errorMessage.textContent = 'Cuenta bloqueada porfavor revisar correo.';
            break;
        case '16':
            errorRecover.style.display = 'block';
            errorRecover.style.color = 'red';
            errorRecover.textContent = 'Porfavor introducir correo o usuario.(token regenerado)';
            modalrecover.show();
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
function Alerta(mensaje) {
    const alertaDiv = document.getElementById('alerta');
    alertaDiv.textContent = mensaje;
    alertaDiv.classList.remove('d-none');
    setTimeout(() => {
        alertaDiv.classList.add('d-none');
    }, 5000);
}