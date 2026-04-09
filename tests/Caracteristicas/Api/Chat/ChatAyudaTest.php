<?php

namespace Tests\Caracteristicas\Api\Chat;

use App\Models\ChatEntradaAyuda;
use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\TestCase;

/**
 * GET /api/v1/chat/ayuda — diccionario, palabras_sugeridas y sugerencias_rapidas.
 */
class ChatAyudaTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;

    public function test_ayuda_incluye_palabras_sugeridas_y_sugerencias_rapidas(): void
    {
        ChatEntradaAyuda::query()->create([
            'titulo' => 'Tema demo prestaciones',
            'modulo' => 'prestaciones_sociales',
            'palabras_clave' => 'demo_presta, otra_clave',
            'contenido' => 'Texto de ayuda demo.',
            'orden' => 1,
            'activo' => true,
        ]);
        ChatEntradaAyuda::query()->create([
            'titulo' => 'Tema demo contratos',
            'modulo' => 'contratos',
            'palabras_clave' => 'demo_contrato',
            'contenido' => 'Otro texto.',
            'orden' => 2,
            'activo' => true,
        ]);

        $usuario = Usuario::factory()->create();

        $res = $this->conJwt($usuario)
            ->getJson('/api/v1/chat/ayuda')
            ->assertOk()
            ->assertJsonPath('message', 'Diccionario del asistente');

        $data = $res->json('data');
        $this->assertIsArray($data);
        $this->assertGreaterThanOrEqual(2, count($data));

        $presta = collect($data)->firstWhere('modulo', 'prestaciones_sociales');
        $this->assertNotNull($presta);
        $this->assertSame(['demo_presta', 'otra_clave'], $presta['palabras_sugeridas']);

        $sug = $res->json('sugerencias_rapidas');
        $this->assertIsArray($sug);
        $demo = collect($sug)->firstWhere('enviar', 'demo_presta');
        $this->assertNotNull($demo);
        $this->assertSame('prestaciones_sociales', $demo['modulo']);
        $this->assertStringContainsString('Tema demo prestaciones', $demo['etiqueta']);

        $cat = $res->json('catalogo_modulos');
        $this->assertIsArray($cat);
        $this->assertGreaterThanOrEqual(2, count($cat));
        $this->assertNotNull(collect($cat)->firstWhere('clave', 'prestaciones_sociales'));
        $this->assertSame([], $res->json('acciones_navegacion'));
        $this->assertNull($res->json('modulo_contexto'));
        $this->assertSame([], $res->json('temas_agrupados'));
    }

    public function test_ayuda_filtra_por_modulo_query_incluye_general(): void
    {
        ChatEntradaAyuda::query()->create([
            'titulo' => 'Tema transversal',
            'modulo' => 'general',
            'palabras_clave' => 'general_demo',
            'contenido' => 'G',
            'orden' => 0,
            'activo' => true,
        ]);
        ChatEntradaAyuda::query()->create([
            'titulo' => 'Solo módulo A',
            'modulo' => 'modulo_a',
            'palabras_clave' => 'clave_a',
            'contenido' => 'A',
            'orden' => 1,
            'activo' => true,
        ]);
        ChatEntradaAyuda::query()->create([
            'titulo' => 'Solo módulo B',
            'modulo' => 'modulo_b',
            'palabras_clave' => 'clave_b',
            'contenido' => 'B',
            'orden' => 2,
            'activo' => true,
        ]);

        $usuario = Usuario::factory()->create();

        $res = $this->conJwt($usuario)
            ->getJson('/api/v1/chat/ayuda?modulo=modulo_a')
            ->assertOk();

        $data = $res->json('data');
        $this->assertCount(2, $data);
        $modulos = collect($data)->pluck('modulo')->sort()->values()->all();
        $this->assertSame(['general', 'modulo_a'], $modulos);

        $sug = $res->json('sugerencias_rapidas');
        $this->assertCount(2, $sug);

        $this->assertSame([], $res->json('catalogo_modulos'));
        $ctx = $res->json('modulo_contexto');
        $this->assertIsArray($ctx);
        $this->assertSame('modulo_a', $ctx['clave']);
        $acc = $res->json('acciones_navegacion');
        $this->assertCount(2, $acc);
        $this->assertSame('volver_modulos', $acc[0]['id']);

        $temas = $res->json('temas_agrupados');
        $this->assertIsArray($temas);
        $this->assertGreaterThanOrEqual(2, count($temas));
        $primer = $temas[0];
        $this->assertArrayHasKey('titulo', $primer);
        $this->assertArrayHasKey('preguntas', $primer);
        $this->assertNotEmpty($primer['preguntas']);
        $this->assertArrayHasKey('etiqueta', $primer['preguntas'][0]);
        $this->assertArrayHasKey('enviar', $primer['preguntas'][0]);
    }

    public function test_modulo_query_invalido_se_ignora_y_devuelve_todo(): void
    {
        ChatEntradaAyuda::query()->create([
            'titulo' => 'X',
            'modulo' => 'ok_mod',
            'palabras_clave' => 'x',
            'contenido' => 'Y',
            'orden' => 1,
            'activo' => true,
        ]);

        $usuario = Usuario::factory()->create();

        $this->conJwt($usuario)
            ->getJson('/api/v1/chat/ayuda?modulo=NO-VALIDO!!')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }
}
