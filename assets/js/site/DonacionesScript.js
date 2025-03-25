// JavaScript source code
document.querySelectorAll('.payment-option').forEach(option => {
    option.addEventListener('click', function () {
        document.querySelectorAll('.payment-option').forEach(opt => {
            opt.classList.remove('active');
            const detailsId = opt.id.replace('Option', 'Details');
            document.getElementById(detailsId).style.display = 'none';
        });

        this.classList.add('active');
        const detailsId = this.id.replace('Option', 'Details');
        document.getElementById(detailsId).style.display = 'block';

        // Marcar el radio button correspondiente
        const radioId = this.id.replace('Option', 'Method');
        document.getElementById(radioId).checked = true;
    });
});

// Manejo de los destinos de donación (versión compacta)
document.querySelectorAll('.destination-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.destination-btn').forEach(b => {
            b.classList.remove('active');
        });
        this.classList.add('active');

        // Marcar el radio button correspondiente
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