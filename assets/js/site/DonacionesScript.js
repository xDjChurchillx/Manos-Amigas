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
    const selectedPayment = document.querySelector('input[name="paymentMethod"]:checked');
    const selectedDestination = document.querySelector('input[name="donationDestination"]:checked');
    const errorPayment = document.getElementById('errPayment');
    const errorDestination = document.getElementById('errDestination');
    errorPayment.textContent = '';
    errorDestination.textContent = '';
    if (!selectedPayment) {
         errorPayment.textContent = 'Por favor, selecciona un metodo para tu donación.';
        e.preventDefault();
    }
    if (!selectedDestination ) {
        errorDestination.textContent =  'Por favor, selecciona un destino para tu donación.';
        e.preventDefault();
    }
   
});

// Obtener parámetros de la URL
const urlParams = new URLSearchParams(window.location.search);
const error = urlParams.get('error');
const url = new URL(window.location.href);
url.searchParams.delete('error');
window.history.replaceState({}, document.title, url);
Alerta('test');
if (error) {
    switch (error) {
        case '1':
           
            break;
        case '2':
           
            break;
        case '3':
          
            break;
        case '4':
          
            break;
        default:
          
    }
}
