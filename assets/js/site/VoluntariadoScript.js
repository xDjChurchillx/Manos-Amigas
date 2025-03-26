document.getElementById('volunteerForm').addEventListener('submit', function (e) {
    e.preventDefault();

    // Validación básica
    const requiredFields = ['fullName', 'email', 'phone', 'institution'];
    let isValid = true;

    requiredFields.forEach(field => {
        if (!document.getElementById(field).value) {
            isValid = false;
            document.getElementById(field).classList.add('is-invalid');
        } else {
            document.getElementById(field).classList.remove('is-invalid');
        }
    });

    // Validar que se haya seleccionado un tipo de actividad
    if (!document.querySelector('input[name="activityType"]:checked')) {
        isValid = false;
        alert('Por favor selecciona un tipo de actividad');
    }

    // Validar acuerdo con el horario
    if (!document.getElementById('scheduleAgreement').checked) {
        isValid = false;
        alert('Debes confirmar que has revisado y aceptas el horario del centro');
    }

    if (isValid) {
        alert('¡Gracias por tu propuesta de voluntariado! Hemos recibido tu solicitud.');
        // this.submit(); // Descomentar para enviar el formulario realmente
    }
});