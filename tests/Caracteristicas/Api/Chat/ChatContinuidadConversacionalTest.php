<?php

namespace Tests\Caracteristicas\Api\Chat;

use App\Models\ChatConversacion;
use App\Models\ChatEntradaAyuda;
use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\TestCase;

class ChatContinuidadConversacionalTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;

    public function test_post_mensaje_devuelve_contexto_y_sugerencias_relacionadas(): void
    {
        ChatEntradaAyuda::query()->create([
            'titulo' => 'Cómo se calculan las cesantías',
            'modulo' => 'prestaciones_sociales',
            'palabras_clave' => 'cesantias, liquidacion',
            'contenido' => 'Guía funcional de cesantías.',
            'orden' => 100,
            'activo' => true,
        ]);
        ChatEntradaAyuda::query()->create([
            'titulo' => 'Intereses de cesantías',
            'modulo' => 'prestaciones_sociales',
            'palabras_clave' => 'intereses cesantias',
            'contenido' => 'Guía funcional de intereses.',
            'orden' => 101,
            'activo' => true,
        ]);

        $usuario = Usuario::factory()->create();
        $conv = ChatConversacion::query()->create(['cod_usuario' => $usuario->cod_usuario, 'titulo' => 'Prueba']);

        $res = $this->conJwt($usuario)
            ->postJson('/api/v1/chat/conversaciones/'.$conv->cod_chat_conversacion.'/mensajes', [
                'contenido' => 'Quiero saber sobre cesantias',
            ])
            ->assertCreated();

        $res->assertJsonPath('data.contexto.modulo_actual', 'prestaciones_sociales');
        $res->assertJsonPath('data.contexto.tema_principal', 'Cómo se calculan las cesantías');
        $this->assertIsInt($res->json('data.contexto.cod_entrada_ayuda_match'));

        $sugerencias = $res->json('data.sugerencias_relacionadas');
        $this->assertIsArray($sugerencias);
        $this->assertGreaterThanOrEqual(1, count($sugerencias));
        $this->assertLessThanOrEqual(4, count($sugerencias));
        $this->assertSame('prestaciones_sociales', $sugerencias[0]['modulo']);
    }

    public function test_contexto_reciente_prioriza_modulo_en_siguiente_pregunta(): void
    {
        ChatEntradaAyuda::query()->create([
            'titulo' => 'Cesantías en el sistema',
            'modulo' => 'prestaciones_sociales',
            'palabras_clave' => 'cesantias',
            'contenido' => 'Detalle de cesantías.',
            'orden' => 100,
            'activo' => true,
        ]);
        ChatEntradaAyuda::query()->create([
            'titulo' => 'Información general de bienvenida',
            'modulo' => 'general',
            'palabras_clave' => 'informacion',
            'contenido' => 'Mensaje general.',
            'orden' => 1,
            'activo' => true,
        ]);

        $usuario = Usuario::factory()->create();
        $conv = ChatConversacion::query()->create(['cod_usuario' => $usuario->cod_usuario, 'titulo' => 'Prueba 2']);

        $this->conJwt($usuario)->postJson('/api/v1/chat/conversaciones/'.$conv->cod_chat_conversacion.'/mensajes', [
            'contenido' => 'cesantias',
        ])->assertCreated();

        $res2 = $this->conJwt($usuario)->postJson('/api/v1/chat/conversaciones/'.$conv->cod_chat_conversacion.'/mensajes', [
            'contenido' => 'informacion',
        ])->assertCreated();

        $this->assertSame('prestaciones_sociales', $res2->json('data.contexto.modulo_actual'));
    }

    public function test_consulta_de_contrasena_no_se_tuerce_por_modulo_previo_empleados(): void
    {
        ChatEntradaAyuda::query()->create([
            'titulo' => 'Estado del empleado y tipos de documento',
            'modulo' => 'empleados',
            'palabras_clave' => 'empleado activo o retirado, tipo documento cc ce ti',
            'contenido' => 'Texto sobre empleados ACTIVO/RETIRADO.',
            'orden' => 500,
            'activo' => true,
        ]);
        ChatEntradaAyuda::query()->create([
            'titulo' => 'Sesión, contraseña y acceso',
            'modulo' => 'general',
            'palabras_clave' => 'olvide contraseña, cerrar sesion, acceso al sistema',
            'contenido' => 'Recuperación de clave y mesa de ayuda.',
            'orden' => 3,
            'activo' => true,
        ]);

        $usuario = Usuario::factory()->create();
        $conv = ChatConversacion::query()->create(['cod_usuario' => $usuario->cod_usuario, 'titulo' => 'Prueba clave']);

        $this->conJwt($usuario)->postJson('/api/v1/chat/conversaciones/'.$conv->cod_chat_conversacion.'/mensajes', [
            'contenido' => 'empleado activo o retirado',
        ])->assertCreated();

        $res2 = $this->conJwt($usuario)->postJson('/api/v1/chat/conversaciones/'.$conv->cod_chat_conversacion.'/mensajes', [
            'contenido' => 'olvide contraseña',
        ])->assertCreated();

        $this->assertSame('general', $res2->json('data.contexto.modulo_actual'));
        $this->assertStringContainsString('clave', $res2->json('data.mensaje_asistente.contenido'));
    }

    public function test_sugerencias_tras_telefono_no_repiten_la_misma_intencion(): void
    {
        ChatEntradaAyuda::query()->create([
            'titulo' => 'Correo y teléfono del empleado',
            'modulo' => 'empleados',
            'palabras_clave' => 'telefono empleado, correo empleado',
            'contenido' => 'Actualiza contacto en Empleados.',
            'orden' => 501,
            'activo' => true,
        ]);
        ChatEntradaAyuda::query()->create([
            'titulo' => 'Cuenta bancaria para pagos',
            'modulo' => 'empleados',
            'palabras_clave' => 'cuenta bancaria empleado',
            'contenido' => 'Banco y número de cuenta.',
            'orden' => 502,
            'activo' => true,
        ]);
        ChatEntradaAyuda::query()->create([
            'titulo' => 'Portal y usuario enlazado',
            'modulo' => 'empleados',
            'palabras_clave' => 'portal del empleado',
            'contenido' => 'Usuario enlazado.',
            'orden' => 503,
            'activo' => true,
        ]);

        $usuario = Usuario::factory()->create();
        $conv = ChatConversacion::query()->create(['cod_usuario' => $usuario->cod_usuario, 'titulo' => 'Sug']);

        $res = $this->conJwt($usuario)->postJson('/api/v1/chat/conversaciones/'.$conv->cod_chat_conversacion.'/mensajes', [
            'contenido' => 'telefono empleado',
        ])->assertCreated();

        $sug = $res->json('data.sugerencias_relacionadas');
        $this->assertNotEmpty($sug);
        $enviar = array_column($sug, 'enviar');
        $this->assertNotContains('telefono empleado', $enviar);
        $etiquetas = array_column($sug, 'etiqueta');
        $this->assertTrue(
            collect($etiquetas)->contains(fn (string $e) => str_contains($e, 'Portal') || str_contains($e, 'Cuenta bancaria')),
            'Se esperaba al menos una sugerencia a otra guía del módulo Empleados.'
        );
    }

    public function test_para_que_sirve_el_sistema_no_devuelve_empleados_por_contexto_previo(): void
    {
        ChatEntradaAyuda::query()->create([
            'titulo' => 'Estado del empleado y tipos de documento',
            'modulo' => 'empleados',
            'palabras_clave' => 'empleado activo o retirado',
            'contenido' => 'Texto fijo empleados ACTIVO RETIRADO.',
            'orden' => 500,
            'activo' => true,
        ]);
        ChatEntradaAyuda::query()->create([
            'titulo' => 'Qué es Talent Sphere en tu día a día',
            'modulo' => 'general',
            'palabras_clave' => 'para que sirve el sistema, que es talent sphere',
            'contenido' => 'Talent Sphere concentra RRHH en un solo lugar.',
            'orden' => 1,
            'activo' => true,
        ]);

        $usuario = Usuario::factory()->create();
        $conv = ChatConversacion::query()->create(['cod_usuario' => $usuario->cod_usuario, 'titulo' => 'Prueba sistema']);

        $this->conJwt($usuario)->postJson('/api/v1/chat/conversaciones/'.$conv->cod_chat_conversacion.'/mensajes', [
            'contenido' => 'empleado activo o retirado',
        ])->assertCreated();

        $res2 = $this->conJwt($usuario)->postJson('/api/v1/chat/conversaciones/'.$conv->cod_chat_conversacion.'/mensajes', [
            'contenido' => 'para que sirve el sistema',
        ])->assertCreated();

        $this->assertSame('general', $res2->json('data.contexto.modulo_actual'));
        $this->assertStringContainsString('Talent Sphere', $res2->json('data.mensaje_asistente.contenido'));
    }

    public function test_modulo_ayuda_opcional_afina_contexto_sin_match_diccionario(): void
    {
        $usuario = Usuario::factory()->create();
        $conv = ChatConversacion::query()->create(['cod_usuario' => $usuario->cod_usuario, 'titulo' => 'Mod ayuda']);

        $res = $this->conJwt($usuario)->postJson('/api/v1/chat/conversaciones/'.$conv->cod_chat_conversacion.'/mensajes', [
            'contenido' => 'zz_no_match_'.uniqid('', true),
            'modulo_ayuda' => 'empleados',
        ])->assertCreated();

        $this->assertSame('empleados', $res->json('data.contexto.modulo_actual'));
    }

    public function test_modulo_ayuda_invalido_devuelve_422(): void
    {
        $usuario = Usuario::factory()->create();
        $conv = ChatConversacion::query()->create(['cod_usuario' => $usuario->cod_usuario, 'titulo' => '422 mod']);

        $this->conJwt($usuario)->postJson('/api/v1/chat/conversaciones/'.$conv->cod_chat_conversacion.'/mensajes', [
            'contenido' => 'hola',
            'modulo_ayuda' => 'NoSnakeCase',
        ])->assertUnprocessable();
    }

    public function test_sugerencias_sin_match_priorizan_temas_posteriores_a_la_ultima_guia_del_hilo(): void
    {
        $tTelefono = ChatEntradaAyuda::query()->create([
            'titulo' => 'Correo y teléfono del empleado',
            'modulo' => 'empleados',
            'palabras_clave' => 'telefono empleado, correo empleado',
            'contenido' => 'Actualiza contacto en Empleados.',
            'orden' => 501,
            'activo' => true,
        ]);
        $tCuenta = ChatEntradaAyuda::query()->create([
            'titulo' => 'Cuenta bancaria para pagos',
            'modulo' => 'empleados',
            'palabras_clave' => 'cuenta bancaria empleado',
            'contenido' => 'Banco y número de cuenta.',
            'orden' => 502,
            'activo' => true,
        ]);
        ChatEntradaAyuda::query()->create([
            'titulo' => 'Estado ACTIVO o RETIRADO y documento',
            'modulo' => 'empleados',
            'palabras_clave' => 'empleado activo o retirado',
            'contenido' => 'Texto estado.',
            'orden' => 500,
            'activo' => true,
        ]);

        $usuario = Usuario::factory()->create();
        $conv = ChatConversacion::query()->create(['cod_usuario' => $usuario->cod_usuario, 'titulo' => 'Ancla hilo']);

        $this->conJwt($usuario)->postJson('/api/v1/chat/conversaciones/'.$conv->cod_chat_conversacion.'/mensajes', [
            'contenido' => 'telefono empleado',
        ])->assertCreated();

        $res2 = $this->conJwt($usuario)->postJson('/api/v1/chat/conversaciones/'.$conv->cod_chat_conversacion.'/mensajes', [
            'contenido' => 'zz_sin_match_'.uniqid('', true),
            'modulo_ayuda' => 'empleados',
        ])->assertCreated();

        $primera = $res2->json('data.sugerencias_relacionadas.0');
        $this->assertNotNull($primera);
        $this->assertSame((int) $tCuenta->cod_entrada_ayuda, (int) $primera['cod_entrada_ayuda']);
        $this->assertArrayHasKey('presentacion_chat', $res2->json('data'));
        $this->assertSame('debajo_ultimo_mensaje_usuario', $res2->json('data.presentacion_chat.sugerencias_relacionadas.ubicacion'));
    }
}

