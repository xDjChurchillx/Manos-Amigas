// Mostrar/ocultar toast
const copyToast = new bootstrap.Toast(document.getElementById('copyToast'));

// Función para copiar al portapapeles
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        copyToast.show();
    });
}

// Manejar clic en botones de copiar
document.querySelectorAll('.copy-btn').forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        const textToCopy = this.previousElementSibling.textContent.trim();
        copyToClipboard(textToCopy);
    });
});

// Copiar al hacer clic en los detalles de IBAN o SINPE
document.querySelectorAll('.payment-details p:first-child').forEach(detail => {
    detail.style.cursor = 'pointer';
    detail.addEventListener('click', function () {
        const textToCopy = this.querySelector('.copy-text').textContent.trim();
        copyToClipboard(textToCopy);
    });
});

// Manejo de los destinos de donación
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

// Manejo del envío del formulario
document.getElementById('donationForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
    const destination = document.querySelector('input[name="donationDestination"]:checked').value;

    if (paymentMethod === 'paypal') {
        alert('Serás redirigido a PayPal para completar tu donación para ' + destination + '.');
        // window.location.href = 'https://www.paypal.com/donate?hosted_button_id=...';
    } else {
        alert(`Gracias por tu donación para ${destination}. Por favor completa la transferencia con los datos proporcionados.`);
    }
});