# EVIDENCIA · Parcial 2 — Sistema de Citas Médicas

> **Proyecto:** Citas Médicas · Laravel 12 + MySQL (Docker) + FullCalendar
> **Fecha:** 2026-09-19
> **Repositorio:** <https://github.com/AxelHerrera11/ParcialDos-Citas>

---

## 1. Trazabilidad Git (RQNF-05)

### 1.1 Ramas de feature creadas desde `main` (más `develop` integrador)

| Rama | Alcance | Estado |
|---|---|---|
| `feature/docker-mysql-schema` | Docker Compose + MySQL + esquema + semillas | ✅ Merge a `main` |
| `feature/api-rest-citas` | API REST citas/doctores/pacientes (por capas) | ✅ Merge a `main` |
| `feature/validacion-conflictos-estados` | Doble reserva (409) y manejo de estados | ✅ Merge a `develop` → `main` |
| `feature/fullcalendar-ui` | Dashboard FullCalendar (crear, detalle, drag & drop) | ✅ Merge a `develop` → `main` |

### 1.2 `git log --graph --all`

```
*   b6b3ece Merge pull request #3 from AxelHerrera11/feature/fullcalendar-ui
|\
| * 0031c27 tests: ajustar prueba raíz al nuevo flujo (redirect a /dashboard) y cubrir render del dashboard
| * 4356185 RQF-04: reprogramar cita con drag & drop sincronizando via PUT /api/citas/{id} y revirtiendo el cambio ante conflicto (409)
| * 5ef0e01 RQF-01/RQF-05/RQF-08/RQF-09: crear cita al hacer clic en el calendario, modal de detalle y cambio de estado (confirmar/cancelar/atender) via PATCH /api/citas/{id}/estado
| * e761695 RQF-02/RQF-06/RQF-10/RQNF-06: dashboard con FullCalendar (vistas mes y semana), carga de citas desde la API, colores por estado y filtros por doctor y rango de fechas
|/
| *   be8db35 Merge pull request #2 from AxelHerrera11/develop
| |\
| |/
|/|
* |   b5c716a Merge pull request #1 from AxelHerrera11/feature/validacion-conflictos-estados
|\ \
| * | 1dcbfa4 RQNF-03: responder HTTP 409 ante conflicto de horario y cobertura de pruebas de doble reserva
| * | 4d95ba6 RQF-03/RQNF-07: validar doble reserva por doctor con horarios solapados en CitaService
|/ /
| *   fa4932c merge: PR develop -> main (feature/api-rest-citas: API REST de citas, doctores y pacientes)
| |\
| |/
|/|
* |   b273df6 merge: PR feature/api-rest-citas -> develop
|\ \
| * | d835007 style: aplicar Laravel Pint al código fuente
| * | e4e0ceb tests: cobertura CRUD, filtros y estados de la API de citas
| * | c0deea2 RQF-08/RQNF-03: Form Requests, Resources JSON y códigos HTTP 200/201/400/404 en la API
| * | 4efd396 RQF-07: modelos, CitaService y controladores API REST de citas, doctores y pacientes
|/ /
| *   092ab05 merge: PR develop -> main (feature/docker-mysql-schema: Docker+MySQL, esquema y semillas)
| |\
| |/
|/|
* |   d49336a merge: PR feature/docker-mysql-schema -> develop
|\ \
| |/
|/|
| * c0d6477 fix: reordenar timestamps de migraciones para que citas referencie pacientes/doctores
| * e6f5145 RQF-01: migraciones y datos semilla de pacientes, doctores y citas
| * d192faa RQNF-01/RQNF-02: docker-compose con MySQL 8.0 en contenedor, volumen persistente y puerto 3307
|/
* d1cbce6 chore: inicializar proyecto Laravel 12 (skeleton v12.12.2)
```

### 1.3 Pull Requests documentados en GitHub

- **PR #1** `feature/validacion-conflictos-estados → develop` — doble reserva impedida (409), validación en servidor.
- **PR #2** `develop → main` — entrega de la validación de conflictos.
- **PR #3** `feature/fullcalendar-ui → develop` — dashboard interactivo.
- **PR #4** `develop → main` — entrega del calendario (incluye esta evidencia).

---

## 2. Docker + MySQL con persistencia (RQF-01, RQNF-01, RQNF-02)

### 2.1 `docker compose up` (un solo comando)

```bash
$ docker compose up -d
```

### 2.2 `docker ps`

```
NAMES         IMAGE       STATUS                    PORTS
citas_mysql   mysql:8.0   Up 38 minutes (healthy)   0.0.0.0:3307->3306/tcp, [::]:3307->3306/tcp
```

### 2.3 Persistencia con volumen

```bash
$ docker volume ls
DRIVER    VOLUME NAME
local     parcialdos-citas_mysql_data
```

Los datos sobreviven a `docker compose down` gracias al volumen `parcialdos-citas_mysql_data` declarado en `docker-compose.yml`.

### 2.4 Esquema y datos semilla

```bash
$ php artisan migrate:fresh --seed
```
Salida (resumen):
```
2026_09_19_000001_create_pacientes_table .......... DONE
2026_09_19_000002_create_doctores_table ........... DONE
2026_09_19_000003_create_citas_table .............. DONE
Database\Seeders\PacienteSeeder ................... DONE
Database\Seeders\DoctorSeeder ..................... DONE
Database\Seeders\CitaSeeder ....................... DONE
```

Tablas: `pacientes`, `doctores`, `citas` (con `estado` enum `pendiente/confirmada/cancelada/atendida` e índices por doctor/fecha y paciente/fecha). Semillas: 8 pacientes, 5 doctores y 16 citas.

---

## 3. API REST (RQF-07, RQF-08, RQF-03, RQNF-03)

Endpoints implementados en `routes/api.php`:

| Método | Ruta | Propósito |
|---|---|---|
| GET | `/api/citas` | Lista con filtros `doctor_id`, `paciente_id`, `desde`, `hasta` |
| POST | `/api/citas` | Crea (201); **409** si hay conflicto de horario |
| GET | `/api/citas/{id}` | Detalle (404 si no existe) |
| PUT | `/api/citas/{id}` | Reprograma usando `fecha`, `hora_inicio`, `hora_fin` |
| PATCH | `/api/citas/{id}/estado` | Cambia estado (`pendiente/confirmada/cancelada/atendida`) |
| GET | `/api/doctores` | Lectura para filtros y formulario |
| GET | `/api/pacientes` | Lectura para el formulario de creación |

Servidor de prueba: `php artisan serve --host=127.0.0.1 --port=8000`

### 3.1 GET /api/doctores → 200

```
{"data":[{"id":1,"nombre":"Dr. Andrés Vega","especialidad":"Medicina General","email":"a.vega@clinica.com","telefono":"555-0201"}, ...]}
```

### 3.2 GET /api/citas → 200 (16 citas semilla)

```
citas: 16
```

### 3.3 GET /api/citas?doctor_id=1&desde=..&hasta=.. → 200 (filtro por doctor y rango, RQF-06)

```
citas del doctor 1 en el rango: 3
  - [confirmada] 2026-09-19 09:00:00 Chequeo general
  - [pendiente] 2026-09-20 14:00:00 Consulta de medicina general
  - [pendiente] 2026-09-22 10:00:00 Control de rutina
```

### 3.4 POST /api/citas → 201

```bash
$ curl -X POST http://127.0.0.1:8000/api/citas -H "Content-Type: application/json" \
  -d '{"paciente_id":1,"doctor_id":1,"fecha":"2026-10-01","hora_inicio":"08:00","hora_fin":"09:00","motivo":"Evidencia creacion"}'
```
```json
{"data":{"id":17,"paciente_id":1,"doctor_id":1,"fecha":"2026-10-01","hora_inicio":"08:00","hora_fin":"09:00","estado":"pendiente","motivo":"Evidencia creacion",...}}
HTTP 201
```

### 3.5 POST /api/citas → 400 (datos inválidos, RQF-08)

```bash
$ curl -X POST http://127.0.0.1:8000/api/citas -H "Content-Type: application/json" \
  -d '{"fecha":"no-fecha","hora_inicio":"10:00","hora_fin":"09:00"}'
```
```json
{"message":"Datos inválidos.","errors":{"paciente_id":["El paciente es obligatorio."],"doctor_id":["El doctor es obligatorio."],"fecha":["The fecha field must be a valid date.","La fecha debe tener formato Y-m-d."],"hora_fin":["La hora de fin debe ser posterior a la de inicio."],"motivo":["El motivo es obligatorio."]}}
HTTP 400
```

### 3.6 POST /api/citas → 409 (doble reserva mismo doctor, RQF-03 / RQNF-07)

```bash
$ curl -X POST http://127.0.0.1:8000/api/citas -H "Content-Type: application/json" \
  -d '{"paciente_id":2,"doctor_id":1,"fecha":"2026-10-01","hora_inicio":"08:30","hora_fin":"09:30","motivo":"Conflicto"}'
```
```json
{"message":"El doctor Dr. Andrés Vega ya tiene una cita en el horario 08:30 - 09:30 de 2026-10-01."}
HTTP 409
```
> La validación vive en `app/Services/CitaService::verificarDisponibilidad()` (**servidor**, no cliente); excluye citas `canceladas` y, en actualizaciones, la propia cita.

### 3.7 GET /api/citas/9999 → 404

```
HTTP 404
```

### 3.8 PUT /api/citas/17 (reprogramar) y PATCH estado → persisten en BD

```bash
$ curl -X PUT http://127.0.0.1:8000/api/citas/17 -H "Content-Type: application/json" \
  -d '{"fecha":"2026-10-02","hora_inicio":"15:00","hora_fin":"16:00"}'
$ curl -X PATCH http://127.0.0.1:8000/api/citas/17/estado -H "Content-Type: application/json" \
  -d '{"estado":"confirmada"}'
```
Verificación en base de datos:
```
cita 17: 2026-10-02 15:00:00-16:00:00 estado=confirmada
```

---

## 4. Interfaz FullCalendar (RQF-02 al RQF-10, RQNF-06)

Dashboard en `GET /dashboard` (HTTP 200). Características:

- [x] **Vistas mes y semana** (`dayGridMonth`, `timeGridWeek`) — RQF-02
- [x] **Crear cita al hacer clic** en el día/hora (formulario: paciente, doctor, fecha, hora inicio/fin, motivo) — RQF-01
- [x] **Detalle al hacer clic** en el evento (paciente, doctor, motivo, hora, estado) — RQF-09
- [x] **Cambio de estado** (confirmar / cancelar / atender) con `PATCH` — RQF-05, RQF-10
- [x] **Drag & drop** para reprogramar con `PUT`; si el servidor responde 409 se **revierte** el evento y se muestra el error (validación del servidor, RQNF-07) — RQF-04
- [x] **Colores por estado**: pendiente=ámbar, confirmada=azul, cancelada=roja, atendida=verde — RQF-10
- [x] **Filtro por doctor y rango de fechas** que re-consultan la API — RQF-06
- [x] **Responsive** (Tailwind; escritorio y tablet) — RQNF-06

Capturas pendientes de insertar en esta sección:
- Calendario en vista mes con citas coloreadas (`/dashboard`)
- Vista semana
- Modal "Nueva cita" tras clic en el día
- Modal de detalle con botones de estado
- Notificación de conflicto 409 al arrastrar una cita sobre otra

---

## 5. Suite de pruebas

```bash
$ php artisan test
```
```
PASS  Tests\Feature\CitaApiTest
  ✓ index lista citas con filtro por doctor
  ✓ index filtra por rango de fechas
  ✓ store crea cita y responde 201
  ✓ store responde 400 con datos invalidos
  ✓ store responde 409 ante doble reserva del mismo doctor
  ✓ store no conflicto con horarios adyacentes
  ✓ store no conflicto si el doctor difiere
  ✓ store no conflicto con cita cancelada
  ✓ update responde 409 si reprograma sobre cita existente
  ✓ show devuelve detalle y 404 si no existe
  ✓ update reprograma cita
  ✓ change estado actualiza y persiste
  ✓ change estado rechaza estado invalido
  ✓ doctores y pacientes listan registros
PASS  Tests\Feature\ExampleTest
  ✓ la raiz redirige al dashboard
  ✓ el dashboard responde ok

Tests:    17 passed (45 assertions)
```

```bash
$ ./vendor/bin/pint --dirty   # estilo PSR-12
$ npm run build               # compila Vite (FullCalendar + Tailwind)
```

---

## 6. Cómo reproducir

```bash
git clone https://github.com/AxelHerrera11/ParcialDos-Citas.git
cd ParcialDos-Citas
composer install
npm install
cp .env.example .env && php artisan key:generate
docker compose up -d
php artisan migrate:fresh --seed
npm run build
php artisan serve
# Abrir http://127.0.0.1:8000/dashboard
```