<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="{{ asset('template/assets/images/icon-geotama.ico') }}" type="image/x-icon" />
    <title>@yield('title', 'Dashboard')</title>

    <!-- ========== All CSS files linkup ========= -->
    <link rel="stylesheet" href="{{ asset('template/assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/css/lineicons.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('template/assets/css/materialdesignicons.min.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('template/assets/css/fullcalendar.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/css/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/custom.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

    <style>
        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--multiple {
            min-height: 38px;
            border: 1px solid #ced4da;
            border-radius: .375rem;
        }
    </style>



    @stack('styles')
</head>

<body>
    <!-- ======== Preloader =========== -->
    <div id="preloader">
        <div class="spinner"></div>
    </div>
    <!-- ======== Preloader =========== -->

    @include('layouts.sidebar')

    <!-- ======== main-wrapper start =========== -->
    <main class="main-wrapper">
        @include('layouts.navbar')

        <!-- ========== section start ========== -->
        <section class="section">
            <div class="container-fluid">
                @yield('content')
            </div>
        </section>

        <!-- ========== section end ========== -->

        @include('layouts.footer')
    </main>
    <!-- ======== main-wrapper end =========== -->

    <!-- ========= All Javascript files linkup ======== -->
    <script src="{{ asset('template/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/Chart.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/dynamic-pie-chart.js') }}"></script>
    <script src="{{ asset('template/assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/fullcalendar.js') }}"></script>
    <script src="{{ asset('template/assets/js/jvectormap.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/world-merc.js') }}"></script>
    <script src="{{ asset('template/assets/js/polyfill.js') }}"></script>
    <script src="{{ asset('template/assets/js/main.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // ======== jvectormap activation
            const mapEl = document.querySelector("#map");
            if (mapEl && typeof jsVectorMap !== "undefined") {
                const markers = [{
                        name: "Egypt",
                        coords: [26.8206, 30.8025]
                    },
                    {
                        name: "Russia",
                        coords: [61.524, 105.3188]
                    },
                    {
                        name: "Canada",
                        coords: [56.1304, -106.3468]
                    },
                    {
                        name: "Greenland",
                        coords: [71.7069, -42.6043]
                    },
                    {
                        name: "Brazil",
                        coords: [-14.235, -51.9253]
                    },
                ];

                new jsVectorMap({
                    map: "world_merc",
                    selector: "#map",
                    zoomButtons: true,
                    regionStyle: {
                        initial: {
                            fill: "#d1d5db",
                        },
                    },
                    labels: {
                        markers: {
                            render: (marker) => marker.name,
                        },
                    },
                    markersSelectable: true,
                    selectedMarkers: markers.map((marker, index) => {
                        return (marker.name === "Russia" || marker.name === "Brazil") ? index :
                            null;
                    }).filter(index => index !== null),
                    markers: markers,
                    markerStyle: {
                        initial: {
                            fill: "#4A6CF7"
                        },
                        selected: {
                            fill: "#ff5050"
                        },
                    },
                    markerLabelStyle: {
                        initial: {
                            fontWeight: 400,
                            fontSize: 14,
                        },
                    },
                });
            }

            // ====== calendar activation
            const calendarMiniEl = document.getElementById("calendar-mini");
            if (calendarMiniEl && typeof FullCalendar !== "undefined") {
                const calendarMini = new FullCalendar.Calendar(calendarMiniEl, {
                    initialView: "dayGridMonth",
                    headerToolbar: {
                        end: "today prev,next",
                    },
                });
                calendarMini.render();
            }

            // =========== chart one start
            const chart1El = document.getElementById("Chart1");
            if (chart1El && typeof Chart !== "undefined") {
                const ctx1 = chart1El.getContext("2d");
                new Chart(ctx1, {
                    type: "line",
                    data: {
                        labels: ["Jan", "Fab", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct",
                            "Nov", "Dec"
                        ],
                        datasets: [{
                            label: "",
                            backgroundColor: "transparent",
                            borderColor: "#365CF5",
                            data: [600, 800, 750, 880, 940, 880, 900, 770, 920, 890, 976, 1100],
                            pointBackgroundColor: "transparent",
                            pointHoverBackgroundColor: "#365CF5",
                            pointBorderColor: "transparent",
                            pointHoverBorderColor: "#fff",
                            pointHoverBorderWidth: 5,
                            borderWidth: 5,
                            pointRadius: 8,
                            pointHoverRadius: 8,
                            cubicInterpolationMode: "monotone",
                        }],
                    },
                    options: {
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    labelColor: function() {
                                        return {
                                            backgroundColor: "#ffffff",
                                            color: "#171717"
                                        };
                                    },
                                },
                                intersect: false,
                                backgroundColor: "#f9f9f9",
                                multiKeyBackground: "transparent",
                                displayColors: false,
                                padding: {
                                    x: 30,
                                    y: 10
                                },
                                bodyAlign: "center",
                                titleAlign: "center",
                                titleColor: "#8F92A1",
                                bodyColor: "#171717",
                                bodyFont: {
                                    family: "Plus Jakarta Sans",
                                    size: 16,
                                    weight: "bold",
                                },
                            },
                            legend: {
                                display: false
                            },
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                grid: {
                                    display: false,
                                    drawTicks: false,
                                    drawBorder: false,
                                },
                                ticks: {
                                    padding: 35,
                                },
                                min: 500,
                                max: 1200,
                            },
                            x: {
                                grid: {
                                    drawBorder: false,
                                    color: "rgba(143, 146, 161, .1)",
                                    zeroLineColor: "rgba(143, 146, 161, .1)",
                                },
                                ticks: {
                                    padding: 20,
                                },
                            },
                        },
                    },
                });
            }

            // =========== chart two start
            const chart2El = document.getElementById("Chart2");
            if (chart2El && typeof Chart !== "undefined") {
                const ctx2 = chart2El.getContext("2d");
                new Chart(ctx2, {
                    type: "bar",
                    data: {
                        labels: ["Jan", "Fab", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct",
                            "Nov", "Dec"
                        ],
                        datasets: [{
                            label: "",
                            backgroundColor: "#365CF5",
                            borderRadius: 30,
                            barThickness: 6,
                            maxBarThickness: 8,
                            data: [600, 700, 1000, 700, 650, 800, 690, 740, 720, 1120, 876, 900],
                        }],
                    },
                    options: {
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || "";
                                        if (label) label += ": ";
                                        label += context.parsed.y;
                                        return label;
                                    },
                                },
                                backgroundColor: "#F3F6F8",
                                titleAlign: "center",
                                bodyAlign: "center",
                                bodyFont: {
                                    size: 16,
                                    weight: "bold",
                                },
                                displayColors: false,
                                padding: {
                                    x: 30,
                                    y: 10
                                },
                            },
                            legend: {
                                display: false
                            },
                            title: {
                                display: false
                            },
                        },
                        layout: {
                            padding: {
                                top: 15,
                                right: 15,
                                bottom: 15,
                                left: 15,
                            },
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                grid: {
                                    display: false,
                                    drawTicks: false,
                                    drawBorder: false,
                                },
                                ticks: {
                                    padding: 35,
                                },
                                min: 0,
                                max: 1200,
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false,
                                    color: "rgba(143, 146, 161, .1)",
                                    drawTicks: false,
                                    zeroLineColor: "rgba(143, 146, 161, .1)",
                                },
                                ticks: {
                                    padding: 20,
                                },
                            },
                        },
                    },
                });
            }

            // =========== chart three start
            const chart3El = document.getElementById("Chart3");
            if (chart3El && typeof Chart !== "undefined") {
                const ctx3 = chart3El.getContext("2d");
                new Chart(ctx3, {
                    type: "line",
                    data: {
                        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct",
                            "Nov", "Dec"
                        ],
                        datasets: [{
                                label: "Revenue",
                                backgroundColor: "transparent",
                                borderColor: "#365CF5",
                                data: [80, 120, 110, 100, 130, 150, 115, 145, 140, 130, 160, 210],
                                pointBackgroundColor: "transparent",
                                pointHoverBackgroundColor: "#365CF5",
                                pointBorderColor: "transparent",
                                pointHoverBorderColor: "#365CF5",
                                pointHoverBorderWidth: 3,
                                pointBorderWidth: 5,
                                pointRadius: 5,
                                pointHoverRadius: 8,
                                fill: false,
                                tension: 0.4,
                            },
                            {
                                label: "Profit",
                                backgroundColor: "transparent",
                                borderColor: "#9b51e0",
                                data: [120, 160, 150, 140, 165, 210, 135, 155, 170, 140, 130, 200],
                                pointBackgroundColor: "transparent",
                                pointHoverBackgroundColor: "#9b51e0",
                                pointBorderColor: "transparent",
                                pointHoverBorderColor: "#9b51e0",
                                pointHoverBorderWidth: 3,
                                pointBorderWidth: 5,
                                pointRadius: 5,
                                pointHoverRadius: 8,
                                fill: false,
                                tension: 0.4,
                            },
                            {
                                label: "Order",
                                backgroundColor: "transparent",
                                borderColor: "#f2994a",
                                data: [180, 110, 140, 135, 100, 90, 145, 115, 100, 110, 115, 150],
                                pointBackgroundColor: "transparent",
                                pointHoverBackgroundColor: "#f2994a",
                                pointBorderColor: "transparent",
                                pointHoverBorderColor: "#f2994a",
                                pointHoverBorderWidth: 3,
                                pointBorderWidth: 5,
                                pointRadius: 5,
                                pointHoverRadius: 8,
                                fill: false,
                                tension: 0.4,
                            },
                        ],
                    },
                    options: {
                        plugins: {
                            tooltip: {
                                intersect: false,
                                backgroundColor: "#fbfbfb",
                                titleColor: "#8F92A1",
                                bodyColor: "#272727",
                                multiKeyBackground: "transparent",
                                displayColors: false,
                                padding: {
                                    x: 30,
                                    y: 15
                                },
                                borderColor: "rgba(143, 146, 161, .1)",
                                borderWidth: 1,
                                enabled: true,
                            },
                            title: {
                                display: false
                            },
                            legend: {
                                display: false
                            },
                        },
                        layout: {
                            padding: {
                                top: 0
                            },
                        },
                        responsive: true,
                        scales: {
                            y: {
                                grid: {
                                    display: false,
                                    drawTicks: false,
                                    drawBorder: false,
                                },
                                ticks: {
                                    padding: 35,
                                },
                                min: 50,
                                max: 350,
                            },
                            x: {
                                grid: {
                                    drawBorder: false,
                                    color: "rgba(143, 146, 161, .1)",
                                    drawTicks: false,
                                    zeroLineColor: "rgba(143, 146, 161, .1)",
                                },
                                ticks: {
                                    padding: 20,
                                },
                            },
                        },
                    },
                });
            }

            // =========== chart four start
            const chart4El = document.getElementById("Chart4");
            if (chart4El && typeof Chart !== "undefined") {
                const ctx4 = chart4El.getContext("2d");
                new Chart(ctx4, {
                    type: "bar",
                    data: {
                        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
                        datasets: [{
                                label: "",
                                backgroundColor: "#365CF5",
                                borderColor: "transparent",
                                borderRadius: 20,
                                borderWidth: 5,
                                barThickness: 20,
                                maxBarThickness: 20,
                                data: [600, 700, 1000, 700, 650, 800],
                            },
                            {
                                label: "",
                                backgroundColor: "#d50100",
                                borderColor: "transparent",
                                borderRadius: 20,
                                borderWidth: 5,
                                barThickness: 20,
                                maxBarThickness: 20,
                                data: [690, 740, 720, 1120, 876, 900],
                            },
                        ],
                    },
                    options: {
                        plugins: {
                            tooltip: {
                                backgroundColor: "#F3F6F8",
                                titleColor: "#8F92A1",
                                bodyColor: "#171717",
                                bodyFont: {
                                    weight: "bold",
                                    size: 16,
                                },
                                multiKeyBackground: "transparent",
                                displayColors: false,
                                padding: {
                                    x: 30,
                                    y: 10
                                },
                                bodyAlign: "center",
                                titleAlign: "center",
                                enabled: true,
                            },
                            legend: {
                                display: false
                            },
                            title: {
                                display: false
                            },
                        },
                        layout: {
                            padding: {
                                top: 0
                            },
                        },
                        responsive: true,
                        scales: {
                            y: {
                                grid: {
                                    display: false,
                                    drawTicks: false,
                                    drawBorder: false,
                                },
                                ticks: {
                                    padding: 35,
                                },
                                min: 0,
                                max: 1200,
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false,
                                    color: "rgba(143, 146, 161, .1)",
                                    zeroLineColor: "rgba(143, 146, 161, .1)",
                                },
                                ticks: {
                                    padding: 20,
                                },
                            },
                        },
                    },
                });
            }

        });
    </script>

    @stack('scripts')
</body>

</html>
