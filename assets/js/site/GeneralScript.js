// JavaScript source code
document.addEventListener("DOMContentLoaded", function () {
    fetch("assets/php/Visita.php", {
        method: 'GET',
        credentials: 'same-origin'
    })
        .then(response => response.text()) 
        .then(text => {
            console.log('visita='+text);
        })
        .catch(error => {
            // En caso de error (fallo en la solicitud o procesamiento), mostrar el error
            console.error('Error al generar vista:', error);
        });
});
function Alerta(mensaje) {
    const alertaDiv = document.getElementById('alerta');
    alertaDiv.textContent = mensaje;
    alertaDiv.classList.remove('d-none');
    setTimeout(() => {
        alertaDiv.classList.add('d-none');
    }, 5000);
}