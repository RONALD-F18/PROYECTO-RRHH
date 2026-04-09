<?php

namespace App\Services;

use App\Models\ChatEntradaAyuda;
use App\Models\ChatMensaje;
use App\Repositories\Interfaces\ChatInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Asistente RRHH (Talent Sphere): conversaciones por usuario; respuestas por **diccionario
 * y palabras clave** (orden `orden` en BD). OpenAI solo si está habilitado en config.
 */
class ChatService
{
    private const ESTADO_CACHE_SEGUNDOS = 21600; // 6 horas
    private const ESTADO_MAX_TEMAS = 8;
    private const SUGERENCIAS_MAX = 5;
    private const CHIPS_POR_TEMA_SECUNDARIOS_MAX = 3;

    /** Palabras que no deben mostrarse como chips (UI / técnicas / vacías). El motor de match sigue usando todas las claves del seed. */
    private const KEYWORDS_META_CHIP = [
        'chips', 'sugerencias', 'buhito', 'palabras clave', 'catalogo', 'cod eps', 'cod arl',
        'api', 'json', 'jwt', 'endpoint', 'http', 'laravel', 'status', '422', '401',
    ];

    public function __construct(
        protected ChatInterface $chatRepository
    ) {}

    public function listarConversaciones(int $codUsuario): Collection
    {
        return $this->chatRepository->listarConversacionesPorUsuario($codUsuario);
    }

    /**
     * Valida el query `modulo` de GET /chat/ayuda (snake_case).
     */
    public function normalizarModuloFiltro(mixed $raw): ?string
    {
        if (! is_string($raw)) {
            return null;
        }
        $m = trim($raw);
        if ($m === '' || ! preg_match('/^[a-z0-9_]{1,50}$/', $m)) {
            return null;
        }

        return $m;
    }

    /**
     * Diccionario para el cliente: entradas, sugerencias, catálogo de módulos (menú raíz),
     * contexto y acciones cuando hay `?modulo=`, y temas agrupados con chips por pregunta.
     *
     * @return array{
     *     entradas: list<array<string, mixed>>,
     *     sugerencias_rapidas: list<array<string, mixed>>,
     *     catalogo_modulos: list<array<string, mixed>>,
     *     modulo_contexto: array<string, mixed>|null,
     *     acciones_navegacion: list<array<string, mixed>>,
     *     temas_agrupados: list<array<string, mixed>>
     * }
     */
    public function diccionarioAyudaParaCliente(?string $modulo = null): array
    {
        $conteos = $this->chatRepository->conteoEntradasAyudaActivasPorModulo();
        $entradas = $this->chatRepository->listarEntradasAyudaActivas($modulo);
        $entradasArr = $entradas->map(function (ChatEntradaAyuda $e) {
            $row = $e->toArray();
            $row['palabras_sugeridas'] = $this->parsePalabrasClave($e->palabras_clave);

            return $row;
        })->values()->all();

        return [
            'entradas' => $entradasArr,
            'sugerencias_rapidas' => $this->construirSugerenciasRapidas($entradas),
            'catalogo_modulos' => $modulo === null ? $this->construirCatalogoModulos($conteos) : [],
            'modulo_contexto' => $this->construirModuloContexto($modulo),
            'acciones_navegacion' => $this->construirAccionesNavegacion($modulo),
            'temas_agrupados' => $modulo !== null ? $this->construirTemasAgrupados($entradas) : [],
        ];
    }

    public function crearConversacion(int $codUsuario, ?string $titulo = null): \App\Models\ChatConversacion
    {
        return $this->chatRepository->crearConversacion([
            'cod_usuario' => $codUsuario,
            'titulo' => $titulo ? Str::limit(trim($titulo), 150, '') : null,
        ]);
    }

    public function eliminarConversacion(int $codChatConversacion, int $codUsuario): bool
    {
        return $this->chatRepository->eliminarConversacion($codChatConversacion, $codUsuario);
    }

    public function obtenerConversacionDeUsuario(int $codChatConversacion, int $codUsuario): ?\App\Models\ChatConversacion
    {
        return $this->chatRepository->obtenerConversacionDeUsuario($codChatConversacion, $codUsuario);
    }

    public function listarMensajes(int $codChatConversacion, int $codUsuario): Collection
    {
        $conv = $this->chatRepository->obtenerConversacionDeUsuario($codChatConversacion, $codUsuario);
        if (! $conv) {
            return new Collection;
        }

        return $this->chatRepository->listarMensajesDeConversacion($codChatConversacion);
    }

    /**
     * @return array{
     *     conversacion: \App\Models\ChatConversacion,
     *     mensaje_usuario: ChatMensaje,
     *     mensaje_asistente: ChatMensaje,
     *     contexto: array{modulo_actual: string|null, tema_principal: string|null, cod_entrada_ayuda_match: int|null},
     *     sugerencias_relacionadas: list<array{etiqueta: string, enviar: string, cod_entrada_ayuda: int, modulo: string|null, prioridad: int}>
     * }|null
     */
    public function enviarMensaje(int $codUsuario, int $codChatConversacion, string $contenido): ?array
    {
        $conv = $this->chatRepository->obtenerConversacionDeUsuario($codChatConversacion, $codUsuario);
        if (! $conv) {
            return null;
        }

        return DB::transaction(function () use ($conv, $contenido) {
            $estadoPrevio = $this->obtenerEstadoConversacion((int) $conv->cod_chat_conversacion);
            $mensajeUsuario = $this->chatRepository->crearMensaje([
                'cod_chat_conversacion' => $conv->cod_chat_conversacion,
                'rol' => ChatMensaje::ROL_USUARIO,
                'contenido' => $contenido,
            ]);

            $resolucion = $this->resolverRespuestaAsistente((int) $conv->cod_chat_conversacion, $contenido, $estadoPrevio);

            $mensajeAsistente = $this->chatRepository->crearMensaje([
                'cod_chat_conversacion' => $conv->cod_chat_conversacion,
                'rol' => ChatMensaje::ROL_ASISTENTE,
                'contenido' => $resolucion['texto'],
            ]);

            $this->guardarEstadoConversacion((int) $conv->cod_chat_conversacion, $resolucion['estado_nuevo']);
            $conv->touch();

            return [
                'conversacion' => $conv->fresh(),
                'mensaje_usuario' => $mensajeUsuario,
                'mensaje_asistente' => $mensajeAsistente,
                'contexto' => $resolucion['contexto'],
                'sugerencias_relacionadas' => $resolucion['sugerencias_relacionadas'],
            ];
        });
    }

    /**
     * @param  array{ultimo_modulo: string|null, ultimos_cod_entrada_ayuda: list<int>}  $estadoPrevio
     * @return array{
     *     texto: string,
     *     contexto: array{modulo_actual: string|null, tema_principal: string|null, cod_entrada_ayuda_match: int|null},
     *     sugerencias_relacionadas: list<array{etiqueta: string, enviar: string, cod_entrada_ayuda: int, modulo: string|null, prioridad: int}>,
     *     estado_nuevo: array{ultimo_modulo: string|null, ultimos_cod_entrada_ayuda: list<int>}
     * }
     */
    private function resolverRespuestaAsistente(int $codChatConversacion, string $mensajeUsuario, array $estadoPrevio): array
    {
        $normalizado = Str::lower(Str::ascii(trim($mensajeUsuario)));
        $entradas = $this->chatRepository->listarEntradasAyudaActivas();
        $match = $this->resolverMejorEntradaAyuda($normalizado, $entradas, $estadoPrevio);

        if ($match !== null) {
            $texto = trim($match->contenido);
            if ($this->contieneJergaTecnica($texto)) {
                $texto = $this->respuestaPorDefecto($match->modulo);
            }

            $estadoNuevo = $this->actualizarEstadoConversacion($estadoPrevio, $match);

            return [
                'texto' => $texto,
                'contexto' => [
                    'modulo_actual' => $match->modulo,
                    'tema_principal' => $match->titulo,
                    'cod_entrada_ayuda_match' => (int) $match->cod_entrada_ayuda,
                ],
                'sugerencias_relacionadas' => $this->construirSugerenciasRelacionadas($match, $entradas, $estadoPrevio, $normalizado),
                'estado_nuevo' => $estadoNuevo,
            ];
        }

        if (config('chatbot.openai_enabled') && filled(config('chatbot.openai_key'))) {
            $openai = $this->intentarOpenAi($codChatConversacion, $entradas);
            if ($openai !== null) {
                return [
                    'texto' => $openai,
                    'contexto' => [
                        'modulo_actual' => $estadoPrevio['ultimo_modulo'],
                        'tema_principal' => null,
                        'cod_entrada_ayuda_match' => null,
                    ],
                    'sugerencias_relacionadas' => $this->sugerenciasDesdeModulo(
                        $estadoPrevio['ultimo_modulo'],
                        $entradas,
                        $estadoPrevio,
                        $normalizado
                    ),
                    'estado_nuevo' => $estadoPrevio,
                ];
            }
        }

        return [
            'texto' => $this->respuestaPorDefecto($estadoPrevio['ultimo_modulo']),
            'contexto' => [
                'modulo_actual' => $estadoPrevio['ultimo_modulo'],
                'tema_principal' => null,
                'cod_entrada_ayuda_match' => null,
            ],
            'sugerencias_relacionadas' => $this->sugerenciasDesdeModulo(
                $estadoPrevio['ultimo_modulo'],
                $entradas,
                $estadoPrevio,
                $normalizado
            ),
            'estado_nuevo' => $estadoPrevio,
        ];
    }

    /**
     * @param  array{ultimo_modulo: string|null, ultimos_cod_entrada_ayuda: list<int>}  $estadoPrevio
     */
    private function resolverMejorEntradaAyuda(string $normalizadoUsuario, Collection $entradas, array $estadoPrevio): ?ChatEntradaAyuda
    {
        $mejor = null;
        $mejorPuntaje = -1;
        $mejorOrden = PHP_INT_MAX;
        $mejorModuloContexto = null;
        $puntajeModuloContexto = -1;
        $ordenModuloContexto = PHP_INT_MAX;

        foreach ($entradas as $entrada) {
            $puntaje = $this->puntuarEntrada($normalizadoUsuario, $entrada, $estadoPrevio);
            if ($puntaje <= 0) {
                continue;
            }
            $orden = (int) ($entrada->orden ?? 9999);
            if ($puntaje > $mejorPuntaje || ($puntaje === $mejorPuntaje && $orden < $mejorOrden)) {
                $mejor = $entrada;
                $mejorPuntaje = $puntaje;
                $mejorOrden = $orden;
            }
            if (
                $estadoPrevio['ultimo_modulo'] !== null
                && $entrada->modulo === $estadoPrevio['ultimo_modulo']
                && ($puntaje > $puntajeModuloContexto || ($puntaje === $puntajeModuloContexto && $orden < $ordenModuloContexto))
            ) {
                $mejorModuloContexto = $entrada;
                $puntajeModuloContexto = $puntaje;
                $ordenModuloContexto = $orden;
            }
        }

        // Si el mejor match es "general" pero el usuario venía de un módulo, a veces conviene
        // mantener el hilo (ej. una sola palabra ambigua tras hablar de cesantías). No sustituyas
        // la respuesta general cuando el mensaje es ya una frase clara (varias palabras o largo)
        // o cuando la entrada general es de sesión/contraseña (ver método siguiente).
        if (
            $mejor instanceof ChatEntradaAyuda
            && $mejor->modulo === 'general'
            && $mejorModuloContexto instanceof ChatEntradaAyuda
            && ! $this->entradaGeneralGanaSobreContinuidadDeModulo($mejor)
            && ! $this->consultaUsuarioPriorizaRespuestaGeneral($normalizadoUsuario)
        ) {
            return $mejorModuloContexto;
        }

        return $mejor;
    }

    /**
     * Mensajes cortos (una palabra o pocos caracteres) suelen ser seguimientos del módulo
     * anterior; frases más largas suelen ser una intención nueva y deben respetar el match
     * en "general" (ej. "para qué sirve el sistema").
     */
    private function consultaUsuarioPriorizaRespuestaGeneral(string $normalizadoUsuario): bool
    {
        if (strlen($normalizadoUsuario) >= 20) {
            return true;
        }
        $palabras = preg_split('/\s+/u', trim($normalizadoUsuario), -1, PREG_SPLIT_NO_EMPTY);

        return count($palabras) >= 4;
    }

    /**
     * Temas de acceso / credenciales / sesión: no deben sustituirse por una entrada débil
     * del último módulo solo por el bonus de contexto conversacional.
     */
    private function entradaGeneralGanaSobreContinuidadDeModulo(ChatEntradaAyuda $entrada): bool
    {
        if ($entrada->modulo !== 'general') {
            return false;
        }
        $titulo = Str::lower(Str::ascii(trim($entrada->titulo)));
        if ($titulo === '') {
            return false;
        }

        return (bool) preg_match(
            '/\b(sesion|contrasena|contrasenya|olvide|clave|ingresar|login|cerrar\s+sesion)\b/u',
            $titulo
        );
    }

    /**
     * @param  array{ultimo_modulo: string|null, ultimos_cod_entrada_ayuda: list<int>}  $estadoPrevio
     */
    private function puntuarEntrada(string $normalizadoUsuario, ChatEntradaAyuda $entrada, array $estadoPrevio): int
    {
        $puntaje = 0;

        $titulo = Str::lower(Str::ascii(trim($entrada->titulo)));
        if ($titulo !== '') {
            if ($normalizadoUsuario === $titulo) {
                $puntaje += 120;
            } elseif (Str::contains($normalizadoUsuario, $titulo)) {
                $puntaje += 90;
            }
        }

        $keywords = $this->parsePalabrasClave($entrada->palabras_clave);
        foreach ($keywords as $kw) {
            $kwNorm = Str::lower(Str::ascii(trim($kw)));
            if ($kwNorm === '') {
                continue;
            }
            if ($normalizadoUsuario === $kwNorm) {
                $puntaje += 75;
            } elseif (Str::contains($normalizadoUsuario, $kwNorm)) {
                $puntaje += 55;
            }
        }

        foreach ($this->sinonimosModulo((string) ($entrada->modulo ?? '')) as $syn) {
            if (Str::contains($normalizadoUsuario, $syn)) {
                $puntaje += 25;
                break;
            }
        }

        if ($estadoPrevio['ultimo_modulo'] !== null && $entrada->modulo === $estadoPrevio['ultimo_modulo']) {
            $puntaje += 30;
        }
        if ($estadoPrevio['ultimo_modulo'] !== null && $entrada->modulo === 'general') {
            $puntaje -= 10;
        }

        return $puntaje;
    }

    /**
     * @return list<string>
     */
    private function parsePalabrasClave(?string $raw): array
    {
        if ($raw === null || trim($raw) === '') {
            return [];
        }
        $out = [];
        foreach (preg_split('/[,;]+/', $raw) as $p) {
            $p = trim($p);
            if ($p !== '') {
                $out[] = $p;
            }
        }

        return $out;
    }

    /**
     * @return list<string>
     */
    private function sinonimosModulo(string $modulo): array
    {
        $raw = config("chat_modulos.sinonimos.{$modulo}", []);
        if (! is_array($raw)) {
            return [];
        }

        $out = [];
        foreach ($raw as $syn) {
            if (! is_string($syn)) {
                continue;
            }
            $synNorm = Str::lower(Str::ascii(trim($syn)));
            if ($synNorm !== '') {
                $out[] = $synNorm;
            }
        }

        return array_values(array_unique($out));
    }

    /**
     * Menú de áreas para la vista sin `?modulo=` (el front llama GET ayuda?modulo=clave al elegir).
     *
     * @param  array<string, int>  $conteos
     * @return list<array<string, mixed>>
     */
    private function construirCatalogoModulos(array $conteos): array
    {
        $vistos = [];
        $filas = [];
        foreach (config('chat_modulos.definiciones', []) as $def) {
            $clave = $def['clave'] ?? '';
            if ($clave === '' || ($conteos[$clave] ?? 0) < 1) {
                continue;
            }
            $filas[] = [
                'clave' => $clave,
                'etiqueta' => $def['etiqueta'] ?? $clave,
                'descripcion' => $def['descripcion'] ?? null,
                'cantidad_temas' => $conteos[$clave],
                'orden' => (int) ($def['orden'] ?? 500),
                'accion' => 'filtrar_ayuda',
                'parametros' => ['modulo' => $clave],
            ];
            $vistos[$clave] = true;
        }
        foreach ($conteos as $clave => $n) {
            if ($n < 1 || isset($vistos[$clave])) {
                continue;
            }
            $filas[] = [
                'clave' => $clave,
                'etiqueta' => Str::title(str_replace('_', ' ', $clave)),
                'descripcion' => null,
                'cantidad_temas' => $n,
                'orden' => 900,
                'accion' => 'filtrar_ayuda',
                'parametros' => ['modulo' => $clave],
            ];
        }

        usort($filas, function (array $a, array $b): int {
            $oa = $a['orden'] <=> $b['orden'];
            if ($oa !== 0) {
                return $oa;
            }

            return strcmp($a['etiqueta'], $b['etiqueta']);
        });

        return $filas;
    }

    private function construirModuloContexto(?string $modulo): ?array
    {
        if ($modulo === null || $modulo === '') {
            return null;
        }
        $meta = $this->resolverMetaModulo($modulo);

        return array_merge(['clave' => $modulo], $meta);
    }

    /**
     * @return array{etiqueta: string, descripcion: string|null}
     */
    private function resolverMetaModulo(string $clave): array
    {
        foreach (config('chat_modulos.definiciones', []) as $def) {
            if (($def['clave'] ?? '') === $clave) {
                return [
                    'etiqueta' => $def['etiqueta'] ?? $clave,
                    'descripcion' => $def['descripcion'] ?? null,
                ];
            }
        }

        return [
            'etiqueta' => Str::title(str_replace('_', ' ', $clave)),
            'descripcion' => null,
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function construirAccionesNavegacion(?string $modulo): array
    {
        if ($modulo === null || $modulo === '') {
            return [];
        }

        return [
            [
                'id' => 'volver_modulos',
                'etiqueta' => 'Ver todos los módulos',
                'tipo' => 'filtrar_ayuda',
                'parametros' => ['modulo' => null],
            ],
            [
                'id' => 'escribir_consulta',
                'etiqueta' => 'Escribir mi consulta con mis palabras',
                'tipo' => 'foco_input',
                'parametros' => [],
            ],
        ];
    }

    /**
     * Por cada tema del listado filtrado: chips con etiqueta legible (evita listar solo palabras sueltas).
     *
     * @param  Collection<int, ChatEntradaAyuda>  $entradas
     * @return list<array<string, mixed>>
     */
    private function construirTemasAgrupados(Collection $entradas): array
    {
        $temas = [];
        foreach ($entradas as $e) {
            $preguntas = $this->construirPreguntasChipsParaEntrada($e);
            if ($preguntas === []) {
                continue;
            }
            $temas[] = [
                'titulo' => $e->titulo,
                'modulo' => $e->modulo,
                'cod_entrada_ayuda' => (int) $e->cod_entrada_ayuda,
                'preguntas' => $preguntas,
            ];
        }

        return $temas;
    }

    /**
     * Chips para UI: sin duplicar el título como primer botón ni mostrar términos meta (chips, sugerencias…).
     *
     * @return list<array{etiqueta: string, enviar: string}>
     */
    private function construirPreguntasChipsParaEntrada(ChatEntradaAyuda $e): array
    {
        $raw = $this->parsePalabrasClave($e->palabras_clave);
        $filtradas = $this->filtrarKeywordsParaChips($raw);
        $preguntas = [];

        if ($filtradas !== []) {
            $principal = $filtradas[0];
            $preguntas[] = [
                'etiqueta' => 'Abrir esta guía',
                'enviar' => $principal,
            ];
            foreach (array_slice($filtradas, 1, self::CHIPS_POR_TEMA_SECUNDARIOS_MAX) as $kw) {
                $preguntas[] = [
                    'etiqueta' => $this->etiquetaLegibleParaChip($kw),
                    'enviar' => $kw,
                ];
            }

            return $preguntas;
        }

        $enviar = $this->textoParaDisparadorChip($e);
        if ($enviar === null) {
            return [];
        }

        return [[
            'etiqueta' => 'Abrir esta guía',
            'enviar' => $enviar,
        ]];
    }

    /**
     * @param  list<string>  $keywords
     * @return list<string>
     */
    private function filtrarKeywordsParaChips(array $keywords): array
    {
        $out = [];
        $seen = [];
        foreach ($keywords as $kw) {
            $kw = trim($kw);
            if ($kw === '' || $this->esKeywordMetaParaChip($kw)) {
                continue;
            }
            $k = Str::lower(Str::ascii($kw));
            if (isset($seen[$k])) {
                continue;
            }
            $seen[$k] = true;
            $out[] = $kw;
        }

        return $out;
    }

    private function esKeywordMetaParaChip(string $kw): bool
    {
        $n = Str::lower(Str::ascii(trim($kw)));
        if ($n === '' || strlen($n) < 2) {
            return true;
        }
        foreach (self::KEYWORDS_META_CHIP as $m) {
            if ($n === Str::lower(Str::ascii($m))) {
                return true;
            }
        }
        if (str_contains($n, 'chip')) {
            return true;
        }

        return false;
    }

    private function etiquetaLegibleParaChip(string $kw): string
    {
        $k = trim($kw);
        $ascii = Str::lower(Str::ascii($k));
        if (preg_match('/^(como|donde|que|cuando|por que)\b/', $ascii)) {
            return '¿'.Str::ucfirst($k).'?';
        }

        return Str::limit(Str::ucfirst($k), 72, '…');
    }

    /**
     * Sugerencias tras un match: primero **otras guías del mismo módulo** (saltos útiles), luego
     * palabras clave de la guía actual que **no** repiten lo que el usuario ya escribió.
     *
     * @param  array{ultimo_modulo: string|null, ultimos_cod_entrada_ayuda: list<int>}  $estadoPrevio
     * @return list<array{etiqueta: string, enviar: string, cod_entrada_ayuda: int, modulo: string|null, prioridad: int}>
     */
    private function construirSugerenciasRelacionadas(
        ChatEntradaAyuda $match,
        Collection $entradas,
        array $estadoPrevio,
        string $normalizadoUsuario = ''
    ): array {
        $list = [];
        $seenEnviar = [];
        $recientes = array_flip($estadoPrevio['ultimos_cod_entrada_ayuda']);

        $push = function (string $etiqueta, string $enviar, ChatEntradaAyuda $e) use (&$list, &$seenEnviar): void {
            $key = Str::lower(trim($enviar));
            if ($key === '' || isset($seenEnviar[$key])) {
                return;
            }
            $seenEnviar[$key] = true;
            $list[] = [
                'etiqueta' => $etiqueta,
                'enviar' => $enviar,
                'cod_entrada_ayuda' => (int) $e->cod_entrada_ayuda,
                'modulo' => $e->modulo,
                'prioridad' => 100 - count($list),
            ];
        };

        foreach ($entradas as $e) {
            if ($e->modulo !== $match->modulo || (int) $e->cod_entrada_ayuda === (int) $match->cod_entrada_ayuda) {
                continue;
            }
            if (isset($recientes[(int) $e->cod_entrada_ayuda])) {
                continue;
            }
            $enviar = $this->textoParaDisparadorChip($e);
            if ($enviar === null) {
                continue;
            }
            $push(Str::limit($e->titulo, 72, '…'), $enviar, $e);
            if (count($list) >= self::SUGERENCIAS_MAX) {
                return $list;
            }
        }

        foreach ($this->filtrarKeywordsParaChips($this->parsePalabrasClave($match->palabras_clave)) as $kw) {
            $kw = trim($kw);
            if ($kw === '') {
                continue;
            }
            $kwNorm = Str::lower(Str::ascii($kw));
            if (isset($seenEnviar[Str::lower($kw)])) {
                continue;
            }
            if ($this->palabraClaveYaCubiertaPorMensaje($normalizadoUsuario, $kwNorm)) {
                continue;
            }
            $push($this->etiquetaLegibleParaChip($kw), $kw, $match);
            if (count($list) >= self::SUGERENCIAS_MAX) {
                break;
            }
        }

        return array_slice($list, 0, self::SUGERENCIAS_MAX);
    }

    /**
     * Evita sugerir de nuevo la misma intención que el usuario acaba de escribir.
     */
    private function palabraClaveYaCubiertaPorMensaje(string $normalizadoUsuario, string $kwNorm): bool
    {
        if ($normalizadoUsuario === '' || $kwNorm === '') {
            return false;
        }
        if (Str::contains($normalizadoUsuario, $kwNorm)) {
            return true;
        }
        if (strlen($normalizadoUsuario) >= 8 && Str::contains($kwNorm, $normalizadoUsuario)) {
            return true;
        }

        return false;
    }

    /**
     * @param  array{ultimo_modulo: string|null, ultimos_cod_entrada_ayuda: list<int>}  $estadoPrevio
     * @return list<array{etiqueta: string, enviar: string, cod_entrada_ayuda: int, modulo: string|null, prioridad: int}>
     */
    private function sugerenciasDesdeModulo(
        ?string $modulo,
        Collection $entradas,
        array $estadoPrevio,
        string $normalizadoUsuario = ''
    ): array {
        if ($modulo === null || $modulo === '') {
            return [];
        }
        $base = $entradas->first(fn (ChatEntradaAyuda $e) => $e->modulo === $modulo);
        if (! $base instanceof ChatEntradaAyuda) {
            return [];
        }

        return $this->construirSugerenciasRelacionadas($base, $entradas, $estadoPrevio, $normalizadoUsuario);
    }

    /**
     * Una sugerencia por entrada: el usuario pulsa y se envía `enviar` como `contenido` del mensaje.
     *
     * @param  Collection<int, ChatEntradaAyuda>  $entradas
     * @return list<array{cod_entrada_ayuda: int, modulo: string|null, etiqueta: string, enviar: string}>
     */
    private function construirSugerenciasRapidas(Collection $entradas): array
    {
        $list = [];
        foreach ($entradas as $e) {
            $enviar = $this->textoParaDisparadorChip($e);
            if ($enviar === null) {
                continue;
            }
            $list[] = [
                'cod_entrada_ayuda' => (int) $e->cod_entrada_ayuda,
                'modulo' => $e->modulo,
                'etiqueta' => Str::limit($e->titulo, 80, ''),
                'enviar' => $enviar,
            ];
        }

        return $list;
    }

    private function textoParaDisparadorChip(ChatEntradaAyuda $e): ?string
    {
        $keywords = $this->parsePalabrasClave($e->palabras_clave);
        if ($keywords !== []) {
            return $keywords[0];
        }
        $t = trim(Str::lower(Str::ascii($e->titulo)));
        if ($t === '') {
            return null;
        }

        return Str::limit($t, 120, '');
    }

    /**
     * @return array{ultimo_modulo: string|null, ultimos_cod_entrada_ayuda: list<int>}
     */
    private function obtenerEstadoConversacion(int $codChatConversacion): array
    {
        $estado = Cache::get($this->cacheKeyEstado($codChatConversacion));
        if (! is_array($estado)) {
            return ['ultimo_modulo' => null, 'ultimos_cod_entrada_ayuda' => []];
        }

        return [
            'ultimo_modulo' => isset($estado['ultimo_modulo']) && is_string($estado['ultimo_modulo'])
                ? $estado['ultimo_modulo']
                : null,
            'ultimos_cod_entrada_ayuda' => array_values(array_filter(
                array_map('intval', (array) ($estado['ultimos_cod_entrada_ayuda'] ?? [])),
                fn (int $n): bool => $n > 0
            )),
        ];
    }

    /**
     * @param  array{ultimo_modulo: string|null, ultimos_cod_entrada_ayuda: list<int>}  $estado
     */
    private function guardarEstadoConversacion(int $codChatConversacion, array $estado): void
    {
        Cache::put($this->cacheKeyEstado($codChatConversacion), $estado, self::ESTADO_CACHE_SEGUNDOS);
    }

    private function cacheKeyEstado(int $codChatConversacion): string
    {
        return 'chat_ctx_'.$codChatConversacion;
    }

    /**
     * @param  array{ultimo_modulo: string|null, ultimos_cod_entrada_ayuda: list<int>}  $estadoPrevio
     * @return array{ultimo_modulo: string|null, ultimos_cod_entrada_ayuda: list<int>}
     */
    private function actualizarEstadoConversacion(array $estadoPrevio, ChatEntradaAyuda $match): array
    {
        $lista = $estadoPrevio['ultimos_cod_entrada_ayuda'];
        $lista[] = (int) $match->cod_entrada_ayuda;
        $lista = array_values(array_unique(array_reverse($lista)));
        $lista = array_reverse(array_slice($lista, 0, self::ESTADO_MAX_TEMAS));

        return [
            'ultimo_modulo' => $match->modulo,
            'ultimos_cod_entrada_ayuda' => $lista,
        ];
    }

    private function intentarOpenAi(int $codChatConversacion, Collection $entradas): ?string
    {
        $max = max(2, (int) config('chatbot.max_mensajes_contexto', 12));
        $historial = $this->chatRepository->listarMensajesDeConversacion($codChatConversacion, 500);
        $ultimos = $historial->slice(-$max)->values();

        $system = $this->construirSystemPrompt($entradas);

        $messages = [['role' => 'system', 'content' => $system]];
        foreach ($ultimos as $m) {
            if ($m->rol === ChatMensaje::ROL_USUARIO) {
                $messages[] = ['role' => 'user', 'content' => $m->contenido];
            } elseif ($m->rol === ChatMensaje::ROL_ASISTENTE) {
                $messages[] = ['role' => 'assistant', 'content' => $m->contenido];
            }
        }

        try {
            $response = Http::withToken((string) config('chatbot.openai_key'))
                ->timeout((int) config('chatbot.openai_timeout', 45))
                ->acceptJson()
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('chatbot.openai_model', 'gpt-4o-mini'),
                    'messages' => $messages,
                    'temperature' => 0.4,
                ]);

            if (! $response->successful()) {
                Log::warning('Chatbot OpenAI HTTP no exitoso', ['status' => $response->status(), 'body' => $response->body()]);

                return null;
            }

            $content = data_get($response->json(), 'choices.0.message.content');
            if (! is_string($content) || trim($content) === '') {
                return null;
            }

            return trim($content);
        } catch (\Throwable $e) {
            Log::warning('Chatbot OpenAI excepción: '.$e->getMessage());

            return null;
        }
    }

    private function construirSystemPrompt(Collection $entradas): string
    {
        $base = 'Eres el asistente virtual de Talent Sphere (gestión de RRHH en Colombia). '
            .'Respondes en español, con tono profesional y claro. '
            .'No inventes datos de empleados ni ejecutes acciones: orientas sobre el uso del sistema y la normativa general. '
            .'Si no sabes algo concreto de un empleado o contrato, indica que revisen el módulo correspondiente en Talent Sphere o a un responsable de RRHH.';

        if ($entradas->isEmpty()) {
            return $base;
        }

        $diccionario = $entradas->map(function ($e) {
            return "### {$e->titulo}\n{$e->contenido}";
        })->implode("\n\n");

        return $base."\n\n## Diccionario del sistema (prioriza coherencia con esto):\n\n".$diccionario;
    }

    private function contieneJergaTecnica(string $texto): bool
    {
        return (bool) preg_match('/\b(api|endpoint|http|status|422|401|jwt|laravel|json)\b/i', $texto);
    }

    private function respuestaPorDefecto(?string $modulo = null): string
    {
        $moduloTexto = $modulo ? $this->resolverMetaModulo($modulo)['etiqueta'] : 'RRHH';

        return "Puedo ayudarte con procesos de **{$moduloTexto}**. Elige una opción relacionada o escribe tu consulta con otras palabras clave del módulo.";
    }
}
