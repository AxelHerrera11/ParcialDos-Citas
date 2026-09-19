<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Doctor;
use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CitaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lista_citas_con_filtro_por_doctor(): void
    {
        $doctorA = Doctor::factory()->create();
        $doctorB = Doctor::factory()->create();

        Cita::factory()->create(['doctor_id' => $doctorA->id, 'fecha' => Carbon::today()->toDateString()]);
        Cita::factory()->create(['doctor_id' => $doctorB->id, 'fecha' => Carbon::today()->toDateString()]);

        $response = $this->getJson('/api/citas?doctor_id='.$doctorA->id);

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.doctor_id', $doctorA->id);
    }

    public function test_index_filtra_por_rango_de_fechas(): void
    {
        $fechaDentro = Carbon::today()->toDateString();
        $fechaFuera = Carbon::today()->addMonth()->toDateString();

        Cita::factory()->create(['fecha' => $fechaDentro]);
        Cita::factory()->create(['fecha' => $fechaFuera]);

        $response = $this->getJson('/api/citas?desde='.$fechaDentro.'&hasta='.$fechaDentro);

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.fecha', $fechaDentro);
    }

    public function test_store_crea_cita_y_responde_201(): void
    {
        $paciente = Paciente::factory()->create();
        $doctor = Doctor::factory()->create();

        $response = $this->postJson('/api/citas', [
            'paciente_id' => $paciente->id,
            'doctor_id' => $doctor->id,
            'fecha' => Carbon::tomorrow()->toDateString(),
            'hora_inicio' => '09:00',
            'hora_fin' => '10:00',
            'motivo' => 'Control general',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.estado', 'pendiente')
            ->assertJsonPath('data.motivo', 'Control general');

        $this->assertDatabaseHas('citas', [
            'paciente_id' => $paciente->id,
            'doctor_id' => $doctor->id,
            'motivo' => 'Control general',
        ]);
    }

    public function test_store_responde_400_con_datos_invalidos(): void
    {
        $response = $this->postJson('/api/citas', [
            'fecha' => 'no-es-una-fecha',
            'hora_inicio' => '09:00',
            'hora_fin' => '08:00',
        ]);

        $response->assertStatus(400)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function test_show_devuelve_detalle_y_404_si_no_existe(): void
    {
        $cita = Cita::factory()->create();

        $this->getJson("/api/citas/{$cita->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $cita->id);

        $this->getJson('/api/citas/9999')->assertNotFound();
    }

    public function test_update_reprograma_cita(): void
    {
        $cita = Cita::factory()->create();
        $nuevaFecha = Carbon::today()->addDays(3)->toDateString();

        $this->putJson("/api/citas/{$cita->id}", [
            'fecha' => $nuevaFecha,
            'hora_inicio' => '14:00',
            'hora_fin' => '15:00',
        ])->assertOk()
            ->assertJsonPath('data.fecha', $nuevaFecha)
            ->assertJsonPath('data.hora_inicio', '14:00');

        $this->assertDatabaseHas('citas', ['id' => $cita->id, 'fecha' => $nuevaFecha]);
    }

    public function test_change_estado_actualiza_y_persiste(): void
    {
        $cita = Cita::factory()->create(['estado' => 'pendiente']);

        $this->patchJson("/api/citas/{$cita->id}/estado", ['estado' => 'confirmada'])
            ->assertOk()
            ->assertJsonPath('data.estado', 'confirmada');

        $this->assertDatabaseHas('citas', ['id' => $cita->id, 'estado' => 'confirmada']);
    }

    public function test_change_estado_rechaza_estado_invalido(): void
    {
        $cita = Cita::factory()->create();

        $this->patchJson("/api/citas/{$cita->id}/estado", ['estado' => 'borrada'])
            ->assertStatus(400);
    }

    public function test_doctores_y_pacientes_listan_registros(): void
    {
        $this->getJson('/api/doctores')->assertOk()->assertJsonCount(0, 'data');
        $this->getJson('/api/pacientes')->assertOk()->assertJsonCount(0, 'data');

        Doctor::factory()->count(2)->create();
        Paciente::factory()->count(3)->create();

        $this->getJson('/api/doctores')->assertOk()->assertJsonCount(2, 'data');
        $this->getJson('/api/pacientes')->assertOk()->assertJsonCount(3, 'data');
    }
}
