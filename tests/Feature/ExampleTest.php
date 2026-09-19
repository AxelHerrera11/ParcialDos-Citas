<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * RQF-02: la raíz redirige al dashboard del calendario.
     */
    public function test_la_raiz_redirige_al_dashboard(): void
    {
        $this->get('/')->assertRedirect('/dashboard');
    }

    public function test_el_dashboard_responde_ok(): void
    {
        $this->get('/dashboard')->assertOk();
    }
}