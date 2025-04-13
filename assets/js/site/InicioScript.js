﻿// JavaScript source code

document.addEventListener('DOMContentLoaded', function () {
    const newsletterForm = document.getElementById('newsletterForm');
    const emailInput = document.getElementById('emailInput');
    const unsubscribeGroup = document.getElementById('unsubscribeGroup');
    const unsubscribeBtn = document.getElementById('unsubscribeBtn');
    const successMessage = document.getElementById('successMessage');

    // Botón para eliminar suscripción
    unsubscribeBtn.addEventListener('click', function () {
        // Simular eliminación de suscripción
        localStorage.removeItem('subscribedEmail');
        emailInput.value = '';
        unsubscribeGroup.classList.add('d-none');

        // Mostrar feedback
        const feedback = document.createElement('div');
        feedback.className = 'text-success small mt-2';
        feedback.textContent = 'Suscripción eliminada correctamente';
        unsubscribeGroup.parentNode.insertBefore(feedback, unsubscribeGroup);

        setTimeout(() => {
            feedback.remove();
        }, 3000);
    });

    // Comprobar si ya está suscrito al cargar la página
    if (localStorage.getItem('correoSuscripcion ')) {
        emailInput.value = localStorage.getItem('subscribedEmail');
        unsubscribeGroup.classList.remove('d-none');
    }

    // Validación en tiempo real
    emailInput.addEventListener('input', function () {
        if (this.checkValidity()) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else {
            this.classList.remove('is-valid');
        }
    });

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
                localStorage.setItem('correoSuscripcion', 'value');
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


});

