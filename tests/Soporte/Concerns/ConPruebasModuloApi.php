<?php

namespace Tests\Soporte\Concerns;

use App\Models\Usuario;

trait ConPruebasModuloApi
{
    protected function reiniciarCabecerasHttp(): void
    {
        $this->defaultHeaders = [];
    }

    /**
     * @param  int  $statusCrear  Código HTTP esperado al crear (201 o 200 según el controlador).
     */
    protected function probarCrudModuloApi(
        Usuario $usuario,
        string $uri,
        array $payloadCrear,
        array $payloadActualizar,
        string $campoId,
        int $statusCrear = 201
    ): mixed {
        $this->reiniciarCabecerasHttp();
        $this->getJson($uri)->assertUnauthorized();

        $this->conJwt($usuario)
            ->getJson($uri)
            ->assertOk()
            ->assertJsonStructure(['message', 'data']);

        $respuesta = $this->conJwt($usuario)
            ->postJson($uri, $payloadCrear)
            ->assertStatus($statusCrear)
            ->assertJsonStructure(['message', 'data']);

        $id = data_get($respuesta->json(), 'data.'.$campoId);
        $this->assertNotNull($id, "El campo {$campoId} debe venir en la respuesta de creación.");

        $this->conJwt($usuario)
            ->getJson("{$uri}/{$id}")
            ->assertOk();

        $this->conJwt($usuario)
            ->putJson("{$uri}/{$id}", $payloadActualizar)
            ->assertOk();

        $this->conJwt($usuario)
            ->deleteJson("{$uri}/{$id}")
            ->assertOk();

        $this->conJwt($usuario)
            ->getJson("{$uri}/{$id}")
            ->assertNotFound();

        return $id;
    }

    /** CRUD para controladores que devuelven el modelo plano (sin message/data). */
    protected function probarCrudModuloApiPlano(
        Usuario $usuario,
        string $uri,
        array $payloadCrear,
        array $payloadActualizar,
        string $campoId
    ): mixed {
        $this->reiniciarCabecerasHttp();
        $this->getJson($uri)->assertUnauthorized();

        $this->conJwt($usuario)->getJson($uri)->assertOk();

        $respuesta = $this->conJwt($usuario)
            ->postJson($uri, $payloadCrear)
            ->assertCreated();

        $id = data_get($respuesta->json(), $campoId);
        $this->assertNotNull($id);

        $this->conJwt($usuario)->getJson("{$uri}/{$id}")->assertOk();
        $this->conJwt($usuario)->putJson("{$uri}/{$id}", $payloadActualizar)->assertOk();
        $this->conJwt($usuario)->deleteJson("{$uri}/{$id}")->assertOk();
        $this->conJwt($usuario)->getJson("{$uri}/{$id}")->assertNotFound();

        return $id;
    }
}
