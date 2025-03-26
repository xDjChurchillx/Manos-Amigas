async function copyToClipboard(text) {
    try {
        // Método moderno (navegadores modernos)
        await navigator.clipboard.writeText(text);
        console.log("Texto copiado al portapapeles: " + text);

        // Opcional: Mostrar feedback visual (ej: tooltip, cambio de ícono)
        alert("¡Copiado: " + text + "!"); 
    } catch (err) {
        console.error("Error al copiar (usando fallback):", err);

        // Fallback para navegadores antiguos
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed'; // Evitar scroll
        document.body.appendChild(textarea);
        textarea.select();

        try {
            document.execCommand('copy');
            console.log("Texto copiado (fallback): " + text);
            alert("¡Copiado: " + text + "!"); // Feedback para el fallback
        } catch (fallbackErr) {
            console.error("Fallback fallido:", fallbackErr);
            alert("No se pudo copiar. Por favor, selecciona el texto y usa Ctrl+C.");
        } finally {
            document.body.removeChild(textarea);
        }
    }
}



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