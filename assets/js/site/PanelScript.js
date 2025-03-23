﻿let chart;
// Obtener los elementos
let desde;
let hasta;
let opciones;
document.addEventListener("DOMContentLoaded", function () {
    // Llamar a la función de verificación de sesión al cargar la página
    startSession();
});
// Función para verificar la sesión
function startSession() {
    fetch("../assets/php/Panel.php", {
        method: 'GET',
        credentials: 'same-origin'
    })
        .then(response => response.text()) 
        .then(text => {
            try {
                let data = JSON.parse(text); 
                if (data.status === "success") {
                    startPanel(data);
                } else {
                    if ("ex" in data) {
                        Alerta(data.ex)
                    } else {
                        Alerta("Error");
                    }
                    if ("redirect" in data) {
                        window.location.href = data.redirect;
                    }
                }
            } catch (error) {
                console.error("La respuesta no es JSON:", text); // Imprime el texto antes de que falle
                Alerta("Error inesperado: " + text); // Opcional: mostrar el error en un alert
            }
        })
        .catch(error => {
            // En caso de error (fallo en la solicitud o procesamiento), mostrar el error
            console.error('Error al verificar la sesión:', error);
            // Redirigir al login en caso de un fallo
            window.location.href = 'ingreso.html?error=1';
        });
}
// Función para inicializar el contador después de cargar el HTML
function startPanel(datos) {

    console.log(datos);  
    // Si la sesión es válida, mostrar el contenido HTML devuelto en el JSON
    document.getElementById('navbaritems').innerHTML = datos.navbar;
    document.getElementById('panel').innerHTML = datos.panel;
    // Obtener los elementos
    desde = document.getElementById('desde');
    hasta = document.getElementById('hasta');
    opciones = document.getElementById('opciones');

    if ('config' in datos) {

        // Verificar y asignar 'fechaDesde'
        if ('fechaDesde' in datos.config) {
            desde.value = datos.config['fechaDesde'];
        }

        // Verificar y asignar 'fechaHasta'
        if ('fechaHasta' in datos.config) {
            hasta.value = datos.config['fechaHasta'];
        }

        // Verificar y asignar 'opcion'
        if ('opcion' in datos.config) {
            opciones.value = datos.config['opcion'];
        }

    }
    if (desde.value == '') {
        hasta.disabled = true;
        hasta.value = '';
    } else {
        hasta.disabled = false;
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
                desde.value = sumarDias(hoy, -6);
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
        if (new Date(desde.value) > new Date(hasta.value)) {
            if (val === '1') {
                hasta.value = desde.value;
            } else {
                desde.value = hasta.value;
            }
        }
    }

    hasta.disabled = !desde.value;
    if (!desde.value) {
        hasta.value = '';
    }

    const datos = {
        fechaDesde: desde.value,
        fechaHasta: hasta.value,
        opcion: opciones.value
    };

    try {
        const response = await fetch("../assets/php/panel.php", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datos)
        });

        const text = await response.text();
        try {
            const data = JSON.parse(text);
            if (data.status === "success") {
                document.getElementById("visitas").innerText = data.sumas.visitas;
                document.getElementById("suscripciones").innerText = data.sumas.suscripciones;
                document.getElementById("donaciones").innerText = data.sumas.donaciones;
                document.getElementById("voluntarios").innerText = data.sumas.voluntarios;
                chart.updateOptions({
                    series: [
                        { name: "Visitas", data: data.data1 },
                        { name: "Suscripciones", data: data.data2 },
                        { name: "Donaciones", data: data.data3 },
                        { name: "Voluntarios", data: data.data4 }
                    ],
                    xaxis: {
                        categories: data.cat
                    }
                });
            } else {
                if ("ex" in data) {
                    Alerta(data.ex);
                }
                if ("redirect" in data) {
                    window.location.href = data.redirect;
                }
            }
        } catch (error) {
            console.error("La respuesta no es JSON:", text);
            Alerta("Error inesperado: " + text);
        }
    } catch (error) {
        console.error('Error al cargar los datos:', error);
        Alerta('Hubo un error al cargar los datos');
    }
}
function Alerta(mensaje) {
    const alertaDiv = document.getElementById('alerta');
    alertaDiv.textContent = mensaje;
    alertaDiv.classList.remove('d-none');
    setTimeout(() => {
        alertaDiv.classList.add('d-none');
    }, 5000);
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