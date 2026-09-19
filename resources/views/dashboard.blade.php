<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Citas Médicas · Calendario</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Calendario de Citas Médicas</h1>
                <p class="text-sm text-gray-500">Laravel 12 · API REST · FullCalendar</p>
            </div>
            <button id="btnNuevaCita" type="button"
                class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                + Nueva cita
            </button>
        </div>
    </header>

    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-wrap items-end gap-4 mb-4">
            <div class="flex flex-col gap-1">
                <label for="filtroDoctor" class="text-xs font-medium text-gray-600">Doctor</label>
                <select id="filtroDoctor"
                    class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none">
                    <option value="">Todos los doctores</option>
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label for="filtroDesde" class="text-xs font-medium text-gray-600">Desde</label>
                <input type="date" id="filtroDesde"
                    class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none">
            </div>
            <div class="flex flex-col gap-1">
                <label for="filtroHasta" class="text-xs font-medium text-gray-600">Hasta</label>
                <input type="date" id="filtroHasta"
                    class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none">
            </div>
            <button type="button" id="btnLimpiarFiltros"
                class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 shadow-sm hover:bg-gray-100">
                Limpiar
            </button>
        </div>

        <div class="flex flex-wrap items-center gap-x-5 gap-y-2 mb-4 text-sm text-gray-600" aria-label="Leyenda de estados">
            <span class="font-medium text-gray-500">Estados:</span>
            <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-amber-500"></span>Pendiente</span>
            <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-blue-500"></span>Confirmada</span>
            <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-red-500"></span>Cancelada</span>
            <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-emerald-500"></span>Atendida</span>
        </div>

        <div id="calendario" class="bg-white rounded-lg border border-gray-200 p-2 sm:p-4 shadow-sm"></div>
    </main>

    <div id="modalCrear" class="hidden fixed inset-0 z-40 overflow-y-auto">
        <div class="fixed inset-0 bg-gray-900/50" data-cerrar="modalCrear"></div>
        <div class="relative flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg rounded-lg bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">Nueva cita</h2>
                    <button type="button" class="text-gray-400 hover:text-gray-600 text-xl leading-none" data-cerrar="modalCrear">&times;</button>
                </div>
                <form id="formCrear" class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-6">
                    <div class="flex flex-col gap-1 sm:col-span-2">
                        <label for="crear_paciente" class="text-xs font-medium text-gray-600">Paciente</label>
                        <select id="crear_paciente" required
                            class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                            <option value="">Selecciona un paciente</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1 sm:col-span-2">
                        <label for="crear_doctor" class="text-xs font-medium text-gray-600">Doctor</label>
                        <select id="crear_doctor" required
                            class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                            <option value="">Selecciona un doctor</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="crear_fecha" class="text-xs font-medium text-gray-600">Fecha</label>
                        <input type="date" id="crear_fecha" required
                            class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="crear_motivo" class="text-xs font-medium text-gray-600">Motivo</label>
                        <input type="text" id="crear_motivo" required maxlength="255"
                            class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="crear_inicio" class="text-xs font-medium text-gray-600">Hora inicio</label>
                        <input type="time" id="crear_inicio" required
                            class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="crear_fin" class="text-xs font-medium text-gray-600">Hora fin</label>
                        <input type="time" id="crear_fin" required
                            class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    </div>
                    <div class="flex justify-end gap-3 sm:col-span-2 pt-2">
                        <button type="button" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" data-cerrar="modalCrear">Cancelar</button>
                        <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Guardar cita</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="modalDetalle" class="hidden fixed inset-0 z-40 overflow-y-auto">
        <div class="fixed inset-0 bg-gray-900/50" data-cerrar="modalDetalle"></div>
        <div class="relative flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg rounded-lg bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">Detalle de la cita</h2>
                    <button type="button" class="text-gray-400 hover:text-gray-600 text-xl leading-none" data-cerrar="modalDetalle">&times;</button>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                        <span id="detalle_estado" class="rounded-full px-3 py-1 text-xs font-semibold capitalize text-white"></span>
                        <span id="detalle_hora" class="text-sm text-gray-600"></span>
                    </div>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Paciente</dt>
                            <dd id="detalle_paciente" class="font-medium text-gray-900"></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Doctor</dt>
                            <dd id="detalle_doctor" class="font-medium text-gray-900"></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Fecha</dt>
                            <dd id="detalle_fecha" class="font-medium text-gray-900"></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Motivo</dt>
                            <dd id="detalle_motivo" class="font-medium text-gray-900"></dd>
                        </div>
                    </dl>
                    <div class="flex flex-wrap justify-end gap-3 pt-6">
                        <button type="button" data-estado="cancelada" class="rounded-md border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100">Cancelar</button>
                        <button type="button" data-estado="confirmada" class="rounded-md border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100">Confirmar</button>
                        <button type="button" data-estado="atendida" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Marcar atendida</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="areaNotificacion" class="fixed bottom-4 right-4 z-50 space-y-2"></div>
</body>
</html>