// JavaScript source code
document.getElementById('subscriptionForm').addEventListener('submit', function (e) {
   
    const Correo = document.getElementById('Correo');
   
    const errorCorreo = document.getElementById('errCorreo');
 

    errorCorreo.textContent = '';
   

    if (Correo.value == '') {
        errorCorreo.textContent = 'Por favor, indique un correo.';      
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
