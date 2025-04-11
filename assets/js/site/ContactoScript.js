document.getElementById('contactForm').addEventListener('submit', function (e) {
    const Nombre = document.getElementById('Nombre');
    const Correo = document.getElementById('Correo');
    const Telefono = document.getElementById('Telefono');
    const Mensaje = document.getElementById('Mensaje');
    const errorCorreo = document.getElementById('errCorreo');
    const errorTelefono = document.getElementById('errTelefono');
    const errorMensaje = document.getElementById('errMensaje');

    errorCorreo.textContent = '';
    errorTelefono.textContent = '';
    errorMensaje.textContent = '';

   
    if (Correo.value == '' && Telefono.value == '') {
        errorCorreo.textContent = 'Por favor, indique un correo o telefono.';
        errorTelefono.textContent = 'Por favor, indique un correo o telefono.';
        e.preventDefault();
    }
    if (!Mensaje.value == '') {
        errorMensaje.textContent = 'Por favor, digita el mensaje que desea enviar.';
        e.preventDefault();
    }

});

// Obtener parámetros de la URL
const urlParams = new URLSearchParams(window.location.search);
const error = urlParams.get('error');
const url = new URL(window.location.href);
url.searchParams.delete('error');
window.history.replaceState({}, document.title, url);
if (error) {
    switch (error) {
        case '0':

            break;
        case '1':
            Alerta('Los campos obligatorios no fueron completados');
            break;
        case '2':
            Alerta('Error en servidor');
            break;
        default:

    }
}
