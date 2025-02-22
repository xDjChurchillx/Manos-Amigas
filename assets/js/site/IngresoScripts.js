// JavaScript source code
// Mostrar mensaje de error si existe en la URL
const urlParams = new URLSearchParams(window.location.search);
const error = urlParams.get('error');
const errorMessage = document.getElementById('error-message');

if (error) {
    errorMessage.style.display = 'block';
    switch (error) {
        case '1':
            errorMessage.textContent = 'Usuario no encontrado.';
            break;
        case '2':
            errorMessage.textContent = 'Contraseña incorrecta.';
            break;
        default:
            errorMessage.textContent = 'Error en el inicio de sesión.';
    }
}