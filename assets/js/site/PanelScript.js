let chart;
// Obtener los elementos
let desde;
let hasta;
let opciones;
document.addEventListener("DOMContentLoaded", function () {
    // Función para verificar la sesión
    function startSession() {
        // Realizar la solicitud AJAX
        fetch('../assets/php/panel.php', {
            method: 'GET',
            credentials: 'same-origin',  // Mantener la sesión activa si es posible
        })
            .then(response => {
                // Verificamos que la respuesta sea exitosa antes de convertirla en JSON
                if (!response.ok) {
                    // En caso de error (fallo en la solicitud o procesamiento), mostrar el error
                    console.log('Error en la solicitud');
                    // Redirigir al login en caso de un fallo
                    window.location.href = 'ingreso.html';
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'error') {
                    // Si la sesión no es válida, redirigir al usuario
                 //   window.location.href = data.redirect;
                } else if (data.status === 'success') {
                    // Si la sesión es válida, mostrar el contenido HTML devuelto en el JSON
                    document.getElementById('navbaritems').innerHTML = data.navbar;
                    document.getElementById('panel').innerHTML = data.panel;
                    // Llamar al contador después de que el HTML se haya cargado
                    startPanel(data);
                }
            })
            .catch(error => {
                // En caso de error (fallo en la solicitud o procesamiento), mostrar el error
                console.error('Error al verificar la sesión:', error);
                // Redirigir al login en caso de un fallo
              //  window.location.href = 'ingreso.html';
            });
    }

    // Llamar a la función de verificación de sesión al cargar la página
    startSession();
});

// Función para inicializar el contador después de cargar el HTML
function startPanel(datos) {

    console.log(datos);  

    // Obtener los elementos
    desde = document.getElementById('desde');
    hasta = document.getElementById('hasta');
    opciones = document.getElementById('opciones');

    if ('config' in datos) {

        // Verificar y asignar 'fechaDesde'
        if ('fechaDesde' in datos.config) {
            console.log('La clave "fechaDesde" existe en el objeto');
            desde.value = datos.config['fechaDesde'];
        }

        // Verificar y asignar 'fechaHasta'
        if ('fechaHasta' in datos.config) {
            console.log('La clave "fechaHasta" existe en el objeto');
            hasta.value = datos.config['fechaHasta'];
        }

        // Verificar y asignar 'opcion'
        if ('opcion' in datos.config) {
            console.log('La clave "opcion" existe en el objeto');
            opciones.value = datos.config['opcion'];
        }

    }


    // Asignar el mismo listener a los tres elementos
    desde.addEventListener('change', function () { actualizarDatos('1'); });
    hasta.addEventListener('change', function () { actualizarDatos('2'); });
    opciones.addEventListener('change', function () { actualizarDatos('0'); });

    // Llamar al contador solo después de que el HTML con los elementos de .timer se haya cargado
    $('.timer').each(function () {
        var $this = $(this);
        var options = $.extend({}, $this.data('countToOptions') || {});
        $this.countTo(options);
    });

    const options = {
        series: [
            { name: "Visitas", data: datos.data1 },
            { name: "Suscripciones", data: datos.data2 },
            { name: "Donaciones", data: datos.data3 },
            { name: "Voluntarios", data: datos.data4 }
        ],
        legend: { position: "bottom" },
        theme: { palette: "palette4" },
        chart: { type: "bar", height: 320 },
        plotOptions: { bar: { horizontal: false, columnWidth: "55%", endingShape: "rounded" } },
        dataLabels: { enabled: false },
        stroke: { show: true, width: 2, colors: ["transparent"] },
        xaxis: { categories: datos.cat },
        yaxis: { title: { text: "Usuarios" } },
        fill: { opacity: 1 },
        tooltip: { y: { formatter: function (t) { return t + " en Total"; } } }
    };

    chart = new ApexCharts(document.querySelector("#bsb-chart-3"), options);
    chart.render();



}

async function actualizarDatos(val) {

    // Obtener los valores
    const hoy = new Date().toLocaleDateString('en-CA', {
        timeZone: 'America/Costa_Rica',
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
    if (val === '0') {
        switch (opciones.value) {
            case 'hoy':
                desde.value = hoy;
                hasta.value = hoy;
                break;
            case 'semana':
                desde.value = sumarDias(hoy, -7);
                hasta.value = hoy;
                break;
            case 'mes':
                desde.value = sumarDias(hoy, -30);
                hasta.value = hoy;
                break;
            default:
                break;
        }
    } else {
        opciones.value = '';
        if (val === '1') {
            if (new Date(desde.value) > new Date(hasta.value)) {
                hasta.value = desde.value; // Asignar el mismo valor a 'hasta' que 'desde'
            }
        } else {
            if (new Date(desde.value) > new Date(hasta.value)) {
                desde.value = hasta.value; // Asignar el mismo valor a 'hasta' que 'desde'
            }
        }
        
    }
  
    const opcion = opciones.value; 
    const fechaDesde = desde.value;
    const fechaHasta = hasta.value;

    // Crear un objeto con los datos
    const datos = {
        fechaDesde: fechaDesde,
        fechaHasta: fechaHasta,
        opcion: opcion
    };
    
    try {
        // Enviar los datos al PHP usando fetch
        const response = await fetch('../assets/php/panel.php', {
            method: 'POST', // Usar POST para enviar los datos
            headers: {
                'Content-Type': 'application/json' // Indicar que el cuerpo es JSON
            },
            body: JSON.stringify(datos) // Convertir el objeto a JSON
        });

        // Verificar si la respuesta es exitosa
        if (!response.ok) {
            throw new Error('Error en la solicitud');
        }

        // Procesar la respuesta JSON
        const respuesta = await response.json(); // 👈 Cambiado a "respuesta"
        console.log('Respuesta del servidor:', respuesta);

        // Aquí puedes manejar la respuesta del servidor
        if (respuesta.status === 'success') {
            chart.updateOptions({
                series: [
                    { name: "Visitas", data: respuesta.data1 },
                    { name: "Suscripciones", data: respuesta.data2 },
                    { name: "Donaciones", data: respuesta.data3 },
                    { name: "Voluntarios", data: respuesta.data4 }
                ],
                xaxis: {
                    categories: respuesta.cat
                }
            });
        } else {
            alert('Error al cargar los datos');
        }
    } catch (error) {
        console.error('Error al cargar los datos:', error);
        alert('Hubo un error al cargar los datos');
    }
}

function sumarDias(fecha, dias) {
    const date = new Date(fecha);
    date.setDate(date.getDate() + dias);
    return date.toISOString().split('T')[0];
}



(function ($) {
    $.fn.countTo = function (options) {
        options = options || {};

        return $(this).each(function () {
            // set options for current element
            var settings = $.extend({}, $.fn.countTo.defaults, {
                from: $(this).data('from'),
                to: $(this).data('to'),
                speed: $(this).data('speed'),
                refreshInterval: $(this).data('refresh-interval'),
                decimals: $(this).data('decimals')
            }, options);

            // how many times to update the value, and how much to increment the value on each update
            var loops = Math.ceil(settings.speed / settings.refreshInterval),
                increment = (settings.to - settings.from) / loops;

            // references & variables that will change with each update
            var self = this,
                $self = $(this),
                loopCount = 0,
                value = settings.from,
                data = $self.data('countTo') || {};

            $self.data('countTo', data);

            // if an existing interval can be found, clear it first
            if (data.interval) {
                clearInterval(data.interval);
            }
            data.interval = setInterval(updateTimer, settings.refreshInterval);

            // initialize the element with the starting value
            render(value);

            function updateTimer() {
                value += increment;
                loopCount++;

                render(value);

                if (typeof (settings.onUpdate) == 'function') {
                    settings.onUpdate.call(self, value);
                }

                if (loopCount >= loops) {
                    // remove the interval
                    $self.removeData('countTo');
                    clearInterval(data.interval);
                    value = settings.to;

                    if (typeof (settings.onComplete) == 'function') {
                        settings.onComplete.call(self, value);
                    }
                }
            }

            function render(value) {
                var formattedValue = settings.formatter.call(self, value, settings);
                $self.html(formattedValue);
            }
        });
    };

    $.fn.countTo.defaults = {
        from: 0,               // the number the element should start at
        to: 0,                 // the number the element should end at
        speed: 1000,           // how long it should take to count between the target numbers
        refreshInterval: 100,  // how often the element should be updated
        decimals: 0,           // the number of decimal places to show
        formatter: formatter,  // handler for formatting the value before rendering
        onUpdate: null,        // callback method for every time the element is updated
        onComplete: null       // callback method for when the element finishes updating
    };

    function formatter(value, settings) {
        return value.toFixed(settings.decimals);
    }
}(jQuery));

jQuery(function ($) {
    // custom formatting example
    $('.count-number').data('countToOptions', {
        formatter: function (value, options) {
            return value.toFixed(options.decimals).replace(/\B(?=(?:\d{3})+(?!\d))/g, ',');
        }
    });

    // start all the timers after HTML content is loaded
    $('.timer').each(function () {
        var $this = $(this);
        var options = $.extend({}, $this.data('countToOptions') || {});
        $this.countTo(options);
    });
});