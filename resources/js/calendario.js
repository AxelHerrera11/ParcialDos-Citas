import { Calendar } from 'fullcalendar';
import dayGridPlugin from 'fullcalendar/daygrid';
import timeGridPlugin from 'fullcalendar/timegrid';
import interactionPlugin from 'fullcalendar/interaction';
import esLocale from 'fullcalendar/locales/es';
import 'fullcalendar/skeleton.css';
import 'fullcalendar/themes/classic/theme.css';
import 'fullcalendar/themes/classic/palette.css';

const COLORES_ESTADO = {
    pendiente: '#f59e0b',
    confirmada: '#3b82f6',
    cancelada: '#ef4444',
    atendida: '#10b981',
};

const $ = (id) => document.getElementById(id);

const aFecha = (d) =>
    `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;

const aHora = (d) => `${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}`;

const aEvento = (cita) => ({
    id: String(cita.id),
    title: cita.paciente ? `${cita.paciente.nombre} · ${cita.motivo}` : cita.motivo,
    start: `${cita.fecha}T${cita.hora_inicio}`,
    end: `${cita.fecha}T${cita.hora_fin}`,
    allDay: false,
    backgroundColor: COLORES_ESTADO[cita.estado] ?? '#6b7280',
    borderColor: COLORES_ESTADO[cita.estado] ?? '#6b7280',
    extendedProps: { cita },
});

async function cargarEventos(info, success, failure) {
    try {
        const params = new URLSearchParams();
        params.set('desde', $('filtroDesde').value || aFecha(info.start));
        params.set('hasta', $('filtroHasta').value || aFecha(info.end));
        if ($('filtroDoctor').value) {
            params.set('doctor_id', $('filtroDoctor').value);
        }

        const res = await fetch(`/api/citas?${params.toString()}`);
        const body = await res.json();
        const citas = res.ok ? body.data ?? [] : [];
        success(citas.map(aEvento));
    } catch (error) {
        failure(error);
    }
}

async function cargarCatalogos() {
    const [doctoresRes, pacientesRes] = await Promise.all([
        fetch('/api/doctores'),
        fetch('/api/pacientes'),
    ]);
    const doctores = await doctoresRes.json();
    const pacientes = await pacientesRes.json();

    doctores.data.forEach((doctor) => {
        const opcion = new Option(`${doctor.nombre} — ${doctor.especialidad}`, String(doctor.id));
        $('filtroDoctor').appendChild(opcion.cloneNode(true));
        $('crear_doctor').appendChild(opcion.cloneNode(true));
    });

    pacientes.data.forEach((paciente) => {
        const opcion = new Option(paciente.nombre, String(paciente.id));
        $('crear_paciente').appendChild(opcion);
    });
}

const calendario = new Calendar($('calendario'), {
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    locale: esLocale,
    initialView: 'dayGridMonth',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek',
    },
    height: 'auto',
    dayMaxEvents: true,
    weekends: true,
    events: cargarEventos,
    eventDisplay: 'block',
});

$('filtroDoctor').addEventListener('change', () => calendario.refetchEvents());
$('filtroDesde').addEventListener('change', () => calendario.refetchEvents());
$('filtroHasta').addEventListener('change', () => calendario.refetchEvents());
$('btnLimpiarFiltros').addEventListener('click', () => {
    $('filtroDoctor').value = '';
    $('filtroDesde').value = '';
    $('filtroHasta').value = '';
    calendario.refetchEvents();
});

cargarCatalogos();
calendario.render();