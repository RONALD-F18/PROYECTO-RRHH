<?php

namespace Tests\Caracteristicas\Api\Reportes;

use App\Models\ReporteRegistro;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\TestCase;

/**
 * Historial de reportes (tabla reporte_registros) para el front React.
 */
class ReporteRegistroApiTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;

    public function test_sin_token_no_lista_registros(): void
    {
        $this->getJson(route('reportes.registros.index'))
            ->assertUnauthorized();
    }

    public function test_post_crea_registro_con_cod_usuario_autenticado(): void
    {
        $usuario = Usuario::factory()->create();

        $this->conJwt($usuario)
            ->postJson(route('reportes.registros.store'), [
                'modulo' => 'contratos',
                'tipo' => 'resumen_general',
                'estado' => 'Generado',
                'descripcion' => 'Corte mensual',
            ])
            ->assertCreated()
            ->assertJsonPath('data.modulo', 'contratos')
            ->assertJsonPath('data.tipo', 'resumen_general')
            ->assertJsonPath('data.estado', 'Generado')
            ->assertJsonPath('data.descripcion', 'Corte mensual')
            ->assertJsonPath('data.nombre_usuario', $usuario->nombre_usuario);

        $this->assertDatabaseHas('reporte_registros', [
            'cod_usuario' => $usuario->cod_usuario,
            'modulo' => 'contratos',
            'estado' => 'Generado',
        ]);
    }

    public function test_funcionario_solo_ve_sus_registros(): void
    {
        $a = Usuario::factory()->create();
        $b = Usuario::factory()->create();

        ReporteRegistro::factory()->create(['cod_usuario' => $a->cod_usuario, 'modulo' => 'empleados']);
        ReporteRegistro::factory()->create(['cod_usuario' => $b->cod_usuario, 'modulo' => 'contratos']);

        $res = $this->conJwt($a)
            ->getJson(route('reportes.registros.index'))
            ->assertOk()
            ->assertJsonStructure(['data']);

        $ids = collect($res->json('data'))->pluck('id')->all();
        $this->assertCount(1, $ids);
    }

    public function test_administrador_ve_registros_de_todos(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $a = Usuario::factory()->create();
        $b = Usuario::factory()->create();

        ReporteRegistro::factory()->create(['cod_usuario' => $a->cod_usuario]);
        ReporteRegistro::factory()->create(['cod_usuario' => $b->cod_usuario]);

        $this->conJwt($admin)
            ->getJson(route('reportes.registros.index'))
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_filtro_por_modulo(): void
    {
        $usuario = Usuario::factory()->create();
        ReporteRegistro::factory()->create(['cod_usuario' => $usuario->cod_usuario, 'modulo' => 'empleados']);
        ReporteRegistro::factory()->create(['cod_usuario' => $usuario->cod_usuario, 'modulo' => 'contratos']);

        $this->conJwt($usuario)
            ->getJson(route('reportes.registros.index', ['modulo' => 'contratos']))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.modulo', 'contratos');
    }

    public function test_filtro_por_rango_de_fechas_en_created_at(): void
    {
        $usuario = Usuario::factory()->create();
        $viejo = ReporteRegistro::factory()->create(['cod_usuario' => $usuario->cod_usuario]);
        $nuevo = ReporteRegistro::factory()->create(['cod_usuario' => $usuario->cod_usuario]);

        DB::table('reporte_registros')->where('id', $viejo->id)->update([
            'created_at' => now()->subDays(20),
            'updated_at' => now()->subDays(20),
        ]);
        DB::table('reporte_registros')->where('id', $nuevo->id)->update([
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        $desde = now()->subDays(5)->toDateString();
        $hasta = now()->toDateString();

        $this->conJwt($usuario)
            ->getJson(route('reportes.registros.index', [
                'fecha_desde' => $desde,
                'fecha_hasta' => $hasta,
            ]))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $nuevo->id);
    }

    public function test_funcionario_puede_eliminar_su_registro(): void
    {
        $usuario = Usuario::factory()->create();
        $registro = ReporteRegistro::factory()->create(['cod_usuario' => $usuario->cod_usuario]);

        $this->conJwt($usuario)
            ->deleteJson(route('reportes.registros.destroy', ['reporte_registro' => $registro->id]))
            ->assertNoContent();

        $this->assertDatabaseMissing('reporte_registros', ['id' => $registro->id]);
    }

    public function test_funcionario_no_puede_eliminar_registro_ajeno(): void
    {
        $a = Usuario::factory()->create();
        $b = Usuario::factory()->create();
        $registro = ReporteRegistro::factory()->create(['cod_usuario' => $b->cod_usuario]);

        $this->conJwt($a)
            ->deleteJson(route('reportes.registros.destroy', ['reporte_registro' => $registro->id]))
            ->assertForbidden();
    }

    public function test_administrador_puede_eliminar_registro_ajeno(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $otro = Usuario::factory()->create();
        $registro = ReporteRegistro::factory()->create(['cod_usuario' => $otro->cod_usuario]);

        $this->conJwt($admin)
            ->deleteJson(route('reportes.registros.destroy', ['reporte_registro' => $registro->id]))
            ->assertNoContent();
    }

    public function test_validacion_store_modulo_invalido(): void
    {
        $usuario = Usuario::factory()->create();

        $this->conJwt($usuario)
            ->postJson(route('reportes.registros.store'), [
                'modulo' => 'inventado',
                'tipo' => 'resumen_general',
                'estado' => 'Generado',
            ])
            ->assertUnprocessable();
    }
}
