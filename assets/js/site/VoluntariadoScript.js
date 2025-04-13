document.addEventListener('DOMContentLoaded', function () {

    document.getElementById('volunteerForm').addEventListener('submit', function (e) {
        const Nombre = document.getElementById('Nombre');
        const Correo = document.getElementById('Correo');
        const Telefono = document.getElementById('Telefono');
        const Propuesta = document.querySelector('input[name="Propuesta"]:checked');
        const otraPropuesta = document.getElementById('otraPropuesta');
        const errorNombre = document.getElementById('errNombre');
        const errorCorreo = document.getElementById('errCorreo');
        const errorTelefono = document.getElementById('errTelefono');
        const errorPropuesta = document.getElementById('errPropuesta');
        const errorotraPropuesta = document.getElementById('errotraPropuesta');

        errorNombre.textContent = '';
        errorCorreo.textContent = '';
        errorTelefono.textContent = '';
        errorPropuesta.textContent = '';
        errorotraPropuesta.textContent = '';

        if (Nombre.value == '' ) {
            errorNombre.textContent =  'Por favor, selecciona un destino para tu donación.';
            e.preventDefault();
        }
        if (Correo.value == ''  && Telefono.value == '') {
            errorCorreo.textContent = 'Por favor, indique un correo o telefono.';
            errorTelefono.textContent = 'Por favor, indique un correo o telefono.';
            e.preventDefault();
        }
        if (!Propuesta) {
            errorPropuesta.textContent = 'Por favor, selecciona una propuesta de voluntariado.';
            e.preventDefault();
        }
        if (Propuesta.value == 'otro' && otraPropuesta.value == '') {
            errorotraPropuesta.textContent = 'Por favor, indique una propuesta de voluntariado.';
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

});



