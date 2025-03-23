// JavaScript source code
document.addEventListener("DOMContentLoaded", function () {
    fetch("assets/php/Visita.php", {
        method: 'GET',
        credentials: 'same-origin'
    })
        .then(response => response.text()) // Primero obtenemos el texto en bruto
        .then(text => {
            console.log('visita='+text);
        })
        .catch(error => {
            // En caso de error (fallo en la solicitud o procesamiento), mostrar el error
            console.error('Error al generar vista:', error);
            // Redirigir al login en caso de un fallo
            // window.location.href = 'ingreso.html?error=1';
        });
});
