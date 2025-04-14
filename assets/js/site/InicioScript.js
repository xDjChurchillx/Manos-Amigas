// JavaScript source code

document.addEventListener('DOMContentLoaded', function () {
    const frm = document.getElementById('subscriptionForm');
    const emailInput = document.getElementById('Correo');
    const emailAnular = document.getElementById('ACorreo');
    const errorCorreo = document.getElementById('errCorreo');
    const unsubscribeGroup = document.getElementById('unsubscribeGroup');

    frm.addEventListener('submit', function (e) {
        const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        errorCorreo.textContent = '';
        if (!regex.test(emailInput.value)) {
            errorCorreo.textContent = 'Por favor, indique un correo.';
            e.preventDefault();
        } 
    });
  


    // Validación en tiempo real
    emailInput.addEventListener('input', function () {
        var suscripcion = JSON.parse(localStorage.getItem("correoSuscripcion"));
        const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (regex.test(this.value) ) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else {
            this.classList.remove('is-valid');
        }
        if (this.value == suscripcion.correo && suscripcion.verificado == true) {
            unsubscribeGroup.classList.remove('d-none');
        }
    });


    // Obtener parámetros de la URL
    var newSus = false;
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    const correourl = decodeURIComponent(urlParams.get('correo'));
    const url = new URL(window.location.href);
   // url.searchParams.delete('error');
   //  window.history.replaceState({}, document.title, url);
    // Inicializar el modal
    const modalElement = document.getElementById('suscripcionModal');
    const modalConf = new bootstrap.Modal(modalElement);
    const modalElement2 = document.getElementById('activacionModal');
    const modalAct = new bootstrap.Modal(modalElement2);
    const modalElement3 = document.getElementById('anulacionModal');
    const modalAnul = new bootstrap.Modal(modalElement3);
    const modalElement4 = document.getElementById('anulacionExitosaModal');
    const modalExitpAnul = new bootstrap.Modal(modalElement4);
    if (error) {
        switch (error) {
            case '0':
                var suscripcion = {
                    correo: correourl,
                    verificado: false,
                    fecha: new Date()
                };

                // Guardar en localStorage
                localStorage.setItem("correoSuscripcion", JSON.stringify(suscripcion));
                newSus = true;
                modalAct.show();
                window.scrollTo({
                    top: document.body.scrollHeight,
                    behavior: 'smooth' // Para un desplazamiento suave
                });
                break;
            case '1':
                Alerta('Correo no Valido');
                break;
            case '2':
                var suscripcion = {
                    correo: correourl,
                    verificado: true,
                    fecha: new Date()
                };
                localStorage.setItem("correoSuscripcion", JSON.stringify(suscripcion));
                modalConf.show();
                window.scrollTo({
                    top: document.body.scrollHeight,
                    behavior: 'smooth' // Para un desplazamiento suave
                });
                break;
            case '3':
                var suscripcion = {
                    correo: correourl,
                    verificado: false,
                    fecha: new Date()
                };
                localStorage.setItem("correoSuscripcion", JSON.stringify(suscripcion));
                window.scrollTo({
                    top: document.body.scrollHeight,
                    behavior: 'smooth' // Para un desplazamiento suave
                });
                break;
            case '4':
                Alerta('Error en Base de datos');
                break;
            case '5':
                Alerta('Error inesperado');
                break;
            case '6':
                Alerta('Token Invalido');
                break;
            case '7':
                Alerta('Token o Correo Invalido');
                break;
            case '8':
                var suscripcion = {
                    correo: correourl,
                    verificado: true,
                    fecha: new Date()
                };
                localStorage.setItem("correoSuscripcion", JSON.stringify(suscripcion));
                window.scrollTo({
                    top: document.body.scrollHeight,
                    behavior: 'smooth' // Para un desplazamiento suave
                });
                break;
            case '9':
                modalAnul.show();
                window.scrollTo({
                    top: document.body.scrollHeight,
                    behavior: 'smooth' // Para un desplazamiento suave
                });
                break;
            case '10':
                localStorage.removeItem('correoSuscripcion');
                modalExitpAnul.show();
                window.scrollTo({
                    top: document.body.scrollHeight,
                    behavior: 'smooth' // Para un desplazamiento suave
                });
                break;
            case '11':
               
                break;
            case '12':
              
                break;
            default:

        }
    }
    // Comprobar si ya está suscrito al cargar la página
    if (localStorage.getItem('correoSuscripcion')) {
        var suscripcion = JSON.parse(localStorage.getItem("correoSuscripcion"));
        emailInput.value = suscripcion.correo;
        emailAnular.value = suscripcion.correo;
        if (suscripcion.verificado) {
           unsubscribeGroup.classList.remove('d-none');
        } else {
            if (newSus) {

            } else {
              errorCorreo.textContent = 'Revisa tu correo o el spam,aun no ha sido verficado';
            }
        }
    }
    // Configuración del cierre automático
    let seconds = 10;
    const countdownElement = document.getElementById('countdown');
    const countdownElement2 = document.getElementById('countdown-activacion');
    const countdownElement3 = document.getElementById('countdown-anulacion');
    const countdownElement4 = document.getElementById('countdown-anulacion-exitosa');
    const countdownInterval = setInterval(updateCountdown, 1000);

    function updateCountdown() {
        seconds--;
        countdownElement.textContent = `Cerrando en ${seconds} segundo${seconds !== 1 ? 's' : ''}...`;
        countdownElement2.textContent = `Cerrando en ${seconds} segundo${seconds !== 1 ? 's' : ''}...`;
        countdownElement3.textContent = `Cerrando en ${seconds} segundo${seconds !== 1 ? 's' : ''}...`;
        countdownElement4.textContent = `Cerrando en ${seconds} segundo${seconds !== 1 ? 's' : ''}...`;
        if (seconds <= 0) {
            clearInterval(countdownInterval);
            closeModal();
        }
    }

    function closeModal() {
        modalConf.hide();
        modalAct.hide();
        modalAnul.hide();
        modalExitpAnul.hide();
    }

    // Cerrar manualmente
    document.querySelector('.btn-suscripcion').addEventListener('click', function () {
        clearInterval(countdownInterval);
        closeModal();
    });

  

});

