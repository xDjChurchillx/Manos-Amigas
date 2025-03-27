// JavaScript source code
document.addEventListener("DOMContentLoaded", function () {

    const path = window.location.pathname;
    const segmentos = path.split("/").filter(segmento => segmento !== ""); // Eliminar vac�os
    if (segmentos.length <= 1) {
        fetch("assets/php/Visita.php", {
            method: 'GET',
            credentials: 'same-origin'
        })
            .then(response => response.text())
            .then(text => {
                console.log('visita=' + text);
            })
            .catch(error => {
                // En caso de error (fallo en la solicitud o procesamiento), mostrar el error
                console.error('Error al generar vista:', error);
            });
    } else {
        console.log('No se generar� vista');
    }
});
function Alerta(mensaje) {
    const alertaDiv = document.getElementById('alerta');
    alertaDiv.textContent = mensaje;
    alertaDiv.classList.remove('d-none');
    setTimeout(() => {
        alertaDiv.classList.add('d-none');
    }, 5000);
}