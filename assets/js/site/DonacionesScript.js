async function copyToClipboard(text) {
    if (navigator.clipboard) {
        await navigator.clipboard.writeText(text);
        console.log("Texto copiado al portapapeles: " + text);
    } else {
        // Fallback para navegadores antiguos
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed'; // Evitar scroll
        document.body.appendChild(textarea);
        textarea.select();

        try {
            document.execCommand('copy');
            console.log("Texto copiado (fallback): " + text);
        } catch (fallbackErr) {
            console.error("Fallback fallido:", fallbackErr);
            alert("No se pudo copiar. Por favor, selecciona el texto y usa Ctrl+C.");
        } finally {
            document.body.removeChild(textarea);
        }
    }
  
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
    const selectedPayment = document.querySelector('input[name="paymentMethod"]:checked');
    const selectedDestination = document.querySelector('input[name="donationDestination"]:checked');
    const errorPayment = document.getElementById('errPayment');
    const errorDestination = document.getElementById('errDestination');
    if (!selectedPayment) {
         errorPayment.textContent = 'Por favor, selecciona un metodo para tu donaci�n.';
        e.preventDefault();
    }
    if (!selectedDestination ) {
        errorDestination.textContent =  'Por favor, selecciona un destino para tu donaci�n.';
        e.preventDefault();
    }
   
});