// Mostrar/ocultar toast


// Funci�n para copiar al portapapeles
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        console.log("Texto copiado al portapapeles");
    }).catch(err => {
        console.error("Error al copiar: ", err);
    });
}



// Manejo de los destinos de donaci�n
document.querySelectorAll('.destination-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.destination-btn').forEach(b => {
            b.classList.remove('active');
        });
        this.classList.add('active');

        const radio = this.querySelector('input[type="radio"]');
        radio.checked = true;
    });
});

// Manejo del env�o del formulario
document.getElementById('donationForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
    const destination = document.querySelector('input[name="donationDestination"]:checked').value;

    if (paymentMethod === 'paypal') {
        alert('Ser�s redirigido a PayPal para completar tu donaci�n para ' + destination + '.');
        // window.location.href = 'https://www.paypal.com/donate?hosted_button_id=...';
    } else {
        alert(`Gracias por tu donaci�n para ${destination}. Por favor completa la transferencia con los datos proporcionados.`);
    }
});