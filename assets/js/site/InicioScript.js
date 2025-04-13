// JavaScript source code

document.addEventListener('DOMContentLoaded', function () {
    const frm = document.getElementById('subscriptionForm');
    const emailInput = document.getElementById('Correo');
    const btnSubmit = document.getElementById('btnSubmit');
    const unsubscribeGroup = document.getElementById('unsubscribeGroup');
    const unsubscribeBtn = document.getElementById('unsubscribeBtn');

    frm.addEventListener('submit', function (e) {

        const Correo = document.getElementById('Correo');
        const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        errorCorreo.textContent = '';
        if (!regex.test(Correo)) {
            errorCorreo.textContent = 'Por favor, indique un correo.';
            e.preventDefault();
        } 
    });
    // Botón para eliminar suscripción
    unsubscribeBtn.addEventListener('click', function () {
        // Simular eliminación de suscripción
        localStorage.removeItem('correoSuscripcion');
        unsubscribeGroup.classList.add('d-none');

        emailInput.value = '';        
    });


    // Validación en tiempo real
    emailInput.addEventListener('input', function () {
        const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (regex.test(this.value) ) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else {
            this.classList.remove('is-valid');
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
                var suscripcion = {
                    correo: "Josué",
                    verificado: true,
                    fecha: new Date()
                };

                // Guardar en localStorage
                localStorage.setItem("correoSuscripcion", JSON.stringify(suscripcion));
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
    // Comprobar si ya está suscrito al cargar la página
    if (localStorage.getItem('correoSuscripcion')) {
        var suscripcion = JSON.parse(localStorage.getItem("correoSuscripcion"));
        emailInput.value = suscripcion.correo;
        if (suscripcion.verificado) {
           unsubscribeGroup.classList.remove('d-none');
        } else {
            btnSubmit.disabled = true;
        }
    }


});

