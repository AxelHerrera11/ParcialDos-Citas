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

const redondearHora = (d) => {
    let hora = d.getHours();
    let min = d.getMinutes();
    if (min >= 30) {
        hora += 1;
        min = 0;
    } else {
        min = 30;
    }
    return `${String(hora % 24).padStart(2, '0')}:${String(min).padStart(2, '0')}`;
};

const sumarHora = (hora, minutos = 60) => {
    const [h, m] = hora.split(':').map(Number);
    const total = h * 60 + m + minutos;
    return `${String(Math.floor(total / 60) % 24).padStart(2, '0')}:${String(total % 60).padStart(2, '0')}`;
};

const errores = (body) => {
    if (body.errors && Object.keys(body.errors).length) {
        return Object.values(body.errors).flat().join(' · ');
    }
    return body.message ?? 'Ocurrió un error inesperado.';
};

function notificar(texto, tipo = 'success') {
    const el = document.createElement('div');
    el.className = `rounded-lg px-4 py-3 text-sm text-white shadow-lg ${
        tipo === 'error' ? 'bg-red-600' : 'bg-emerald-600'
    }`;
    el.textContent = texto;
    $('areaNotificacion').appendChild(el);
    setTimeout(() => el.remove(), 5000);
}

function abrirModal(id) {
    $(id).classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function cerrarModal(id) {
    $(id).classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function abrirModalCrear(info) {
    const form = $('formCrear');
    form.reset();
    $('crear_fecha').value = aFecha(info.date);
    if (!info.allDay) {
        const inicio = redondearHora(info.date);
        $('crear_inicio').value = inicio;
        $('crear_fin').value = sumarHora(inicio);
    }
    abrirModal('modalCrear');
}

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

let detalleActual = null;

async function crearCita(event) {
    event.preventDefault();

    const payload = {
        paciente_id: Number($('crear_paciente').value),
        doctor_id: Number($('crear_doctor').value),
        fecha: $('crear_fecha').value,
        hora_inicio: $('crear_inicio').value,
        hora_fin: $('crear_fin').value,
        motivo: $('crear_motivo').value,
    };

    const res = await fetch('/api/citas', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
    });
    const body = await res.json();

    if (!res.ok) {
        notificar(errores(body), 'error');
        return;
    }

    cerrarModal('modalCrear');
    notificar('Cita creada correctamente');
    calendario.refetchEvents();
}

function abrirModalDetalle(event) {
    const cita = event.extendedProps.cita;
    detalleActual = cita;

    $('detalle_paciente').textContent = cita.paciente?.nombre ?? `Paciente #${cita.paciente_id}`;
    $('detalle_doctor').textContent = cita.doctor?.nombre ?? `Doctor #${cita.doctor_id}`;
    $('detalle_fecha').textContent = cita.fecha;
    $('detalle_hora').textContent = `${cita.hora_inicio} - ${cita.hora_fin}`;
    $('detalle_motivo').textContent = cita.motivo;

    const badge = $('detalle_estado');
    badge.textContent = cita.estado;
    badge.style.backgroundColor = COLORES_ESTADO[cita.estado] ?? '#6b7280';

    abrirModal('modalDetalle');
}

async function cambiarEstado(estado) {
    if (!detalleActual) {
        return;
    }

    const res = await fetch(`/api/citas/${detalleActual.id}/estado`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ estado }),
    });
    const body = await res.json();

    if (!res.ok) {
        notificar(errores(body), 'error');
        return;
    }

    cerrarModal('modalDetalle');
    detalleActual = null;
    notificar(`Cita marcada como ${estado}`);
    calendario.refetchEvents();
}

async function reprogramar(info) {
    const inicio = info.event.start;
    const fin = info.event.end ?? new Date(inicio.getTime() + 60 * 60 * 1000);

    const res = await fetch(`/api/citas/${info.event.id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            fecha: aFecha(inicio),
            hora_inicio: aHora(inicio),
            hora_fin: aHora(fin),
        }),
    });
    const body = await res.json();

    if (!res.ok) {
        info.revert();
        notificar(errores(body), 'error');
        return;
    }

    notificar('Cita reprogramada correctamente');
    calendario.refetchEvents();
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
    selectable: true,
    editable: true,
    eventResizableFromStart: false,
    events: cargarEventos,
    eventDisplay: 'block',
    dateClick: abrirModalCrear,
    eventClick: abrirModalDetalle,
    eventDrop: reprogramar,
    eventResize: reprogramar,
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

$('formCrear').addEventListener('submit', crearCita);
$('btnNuevaCita').addEventListener('click', () => {
    $('formCrear').reset();
    abrirModal('modalCrear');
});

document.querySelectorAll('[data-estado]').forEach((boton) => {
    boton.addEventListener('click', () => cambiarEstado(boton.dataset.estado));
});

document.querySelectorAll('[data-cerrar]').forEach((elemento) => {
    elemento.addEventListener('click', () => cerrarModal(elemento.dataset.cerrar));
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        cerrarModal('modalCrear');
        cerrarModal('modalDetalle');
    }
});

cargarCatalogos();
calendario.render();