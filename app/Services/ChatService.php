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
    private const CHIPS_POR_TEMA_SECUNDARIOS_MAX = 2;

    /** Palabras que no deben mostrarse como chips (UI / técnicas / vacías). El motor de match sigue usando todas las claves del seed. */
    private const KEYWORDS_META_CHIP = [
        'chips', 'sugerencias', 'buhito', 'palabras clave', 'catalogo', 'cod eps', 'cod arl',
        'api', 'json', 'jwt', 'endpoint', 'http', 'laravel', 'status', '422', '401',
    ];

    public function __construct(
        protected ChatInterface $chatRepository
    ) {}

    private function limiteSugerenciasRelacionadas(): int
    {
        return max(3, min(6, (int) config('chatbot.sugerencias_relacionadas_max', 4)));
    }

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
     * Si `modulo` es un área operativa (≠ `general`), `data` / `sugerencias_rapidas` / `temas_agrupados`
     * se limitan a ese módulo para que la UI no mezcle ayuda transversal con el flujo del funcionario.
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
        $entradasMotor = $this->chatRepository->listarEntradasAyudaActivas($modulo);
        $entradasVista = $this->entradasSoloModuloActivoParaAyudaUi($entradasMotor, $modulo);
        $entradasArr = $entradasVista->map(function (ChatEntradaAyuda $e) {
            $row = $e->toArray();
            $row['palabras_sugeridas'] = $this->parsePalabrasClave($e->palabras_clave);

            return $row;
        })->values()->all();

        return [
            'entradas' => $entradasArr,
            'sugerencias_rapidas' => $this->construirSugerenciasRapidas($entradasVista),
            'catalogo_modulos' => $modulo === null ? $this->construirCatalogoModulos($conteos) : [],
            'modulo_contexto' => $this->construirModuloContexto($modulo),
            'acciones_navegacion' => $this->construirAccionesNavegacion($modulo),
            'temas_agrupados' => $modulo !== null ? $this->construirTemasAgrupados($entradasVista) : [],
        ];
    }

    /**
     * Con `?modulo=empleados` (cualquier área distinta de `general`) el listado del GET /ayuda debe mostrar
     * **solo** temas de ese módulo: si se mezclan filas `general`, la UI llena la pantalla con “Qué es
     * Talent Sphere” y tapa el chat. El motor de POST sigue usando el diccionario ampliado en repositorio.
     *
     * @param  Collection<int, ChatEntradaAyuda>  $entradas
     * @return Collection<int, ChatEntradaAyuda>
     */
    private function entradasSoloModuloActivoParaAyudaUi(Collection $entradas, ?string $modulo): Collection
    {
        if ($modulo === null || $modulo === '' || $modulo === 'general') {
            return $entradas;
        }

        return $entradas->filter(fn (ChatEntradaAyuda $e) => $e->modulo === $modulo)->values();
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
     *     sugerencias_relacionadas: list<array{etiqueta: string, enviar: string, cod_entrada_ayuda: int, modulo: string|null, prioridad: int}>,
     *     presentacion_chat: array{registro_estilo: string, sugerencias_relacionadas: array{ubicacion: string, alineacion: string, columna: string, nota: string}}
     * }|null
     *
     * @param  string|null  $moduloAyudaCliente  Misma clave que `GET /chat/ayuda?modulo=` (snake_case); acota contexto y estado.
     */
    public function enviarMensaje(int $codUsuario, int $codChatConversacion, string $contenido, ?string $moduloAyudaCliente = null): ?array
    {
        $conv = $this->chatRepository->obtenerConversacionDeUsuario($codChatConversacion, $codUsuario);
        if (! $conv) {
            return null;
        }

        return DB::transaction(function () use ($conv, $contenido, $moduloAyudaCliente) {
            $estadoPrevio = $this->obtenerEstadoConversacion((int) $conv->cod_chat_conversacion);
            $estadoParaResolver = $estadoPrevio;
            if ($moduloAyudaCliente !== null) {
                $estadoParaResolver['ultimo_modulo'] = $moduloAyudaCliente;
            }

            $mensajeUsuario = $this->chatRepository->crearMensaje([
                'cod_chat_conversacion' => $conv->cod_chat_conversacion,
                'rol' => ChatMensaje::ROL_USUARIO,
                'contenido' => $contenido,
            ]);

            $resolucion = $this->resolverRespuestaAsistente((int) $conv->cod_chat_conversacion, $contenido, $estadoParaResolver);

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
                'presentacion_chat' => $this->metaPresentacionChat(),
            ];
        });
    }

    /**
     * Guía de UX para el cliente (burbujas tipo mensajería y dónde colocar chips de seguimiento).
     *
     * @return array{registro_estilo: string, sugerencias_relacionadas: array{ubicacion: string, alineacion: string, columna: string, nota: string}}
     */
    private function metaPresentacionChat(): array
    {
        return [
            'registro_estilo' => 'mensajeria',
            'sugerencias_relacionadas' => [
                'ubicacion' => 'debajo_ultimo_mensaje_usuario',
                'alineacion' => 'inicio',
                'columna' => 'asistente_izquierda',
                'nota' => 'Burbujas cronológicas usuario/bot. Chips debajo de la última burbuja del bot, alineados a la izquierda, scroll horizontal si desbordan.',
            ],
        ];
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
            $openai = $this->intentarOpenAi($codChatConversacion, $entradas, $estadoPrevio['ultimo_modulo']);
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
     * Chips por tema: solo frases cortas derivadas de palabras clave (estilo “Prima de servicios”),
     * sin repetir el título del bloque. `enviar` = clave tal cual para el POST.
     *
     * @return list<array{etiqueta: string, enviar: string}>
     */
    private function construirPreguntasChipsParaEntrada(ChatEntradaAyuda $e): array
    {
        $raw = $this->parsePalabrasClave($e->palabras_clave);
        $filtradas = $this->filtrarKeywordsParaChips($raw);
        $max = 1 + self::CHIPS_POR_TEMA_SECUNDARIOS_MAX;
        $preguntas = [];

        if ($filtradas !== []) {
            $etiquetasNorm = [];
            foreach ($filtradas as $kw) {
                if (count($preguntas) >= $max) {
                    break;
                }
                $kw = trim($kw);
                if ($kw === '') {
                    continue;
                }
                if (strlen($kw) < 6 || str_word_count($kw) < 2) {
                    continue;
                }
                $etiqueta = $this->etiquetaChipCortaDesdePalabraClave($kw);
                $claveEt = Str::lower(Str::ascii($etiqueta));
                if ($claveEt === '' || isset($etiquetasNorm[$claveEt])) {
                    continue;
                }
                $etiquetasNorm[$claveEt] = true;
                $preguntas[] = ['etiqueta' => $etiqueta, 'enviar' => $kw];
            }

            return $preguntas;
        }

        $enviar = $this->textoParaDisparadorChip($e);
        if ($enviar === null) {
            return [];
        }
        if (strlen($enviar) < 6 || str_word_count($enviar) < 2) {
            return [];
        }

        return [[
            'etiqueta' => $this->etiquetaChipCortaDesdePalabraClave($enviar),
            'enviar' => $enviar,
        ]];
    }

    private function etiquetaChipCortaDesdePalabraClave(string $kw): string
    {
        return Str::limit($this->etiquetaLegibleParaChip($kw), 44, '…');
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
     * Sugerencias tras un match: solo otras guías del mismo módulo (orden hacia adelante en el catálogo,
     * luego las anteriores), sin repetir temas recientes ni el texto ya enviado.
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
        $max = $this->limiteSugerenciasRelacionadas();
        $list = [];
        $seenEnviar = [];
        $recientes = array_flip($estadoPrevio['ultimos_cod_entrada_ayuda']);

        $push = function (string $etiqueta, string $enviar, ChatEntradaAyuda $e) use (&$list, &$seenEnviar, $max): void {
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

        $candidatos = $entradas->filter(function (ChatEntradaAyuda $e) use ($match, $recientes, $estadoPrevio) {
            if ($e->modulo !== $match->modulo || (int) $e->cod_entrada_ayuda === (int) $match->cod_entrada_ayuda) {
                return false;
            }
            if (isset($recientes[(int) $e->cod_entrada_ayuda])) {
                return false;
            }
            if ($this->debeOmitirSugerenciaGeneralPorHilo($match, $e, $estadoPrevio)) {
                return false;
            }

            return true;
        });

        $ordenMatch = (int) $match->orden;
        $forward = $candidatos->filter(fn (ChatEntradaAyuda $e) => (int) $e->orden > $ordenMatch)
            ->sortBy(fn (ChatEntradaAyuda $e) => (int) $e->orden)
            ->values();
        $backward = $candidatos->filter(fn (ChatEntradaAyuda $e) => (int) $e->orden < $ordenMatch)
            ->sortByDesc(fn (ChatEntradaAyuda $e) => (int) $e->orden)
            ->values();
        $ordenados = $forward->concat($backward);

        foreach ($ordenados as $e) {
            $enviar = $this->textoParaDisparadorChip($e);
            if ($enviar === null) {
                continue;
            }
            $envNorm = Str::lower(Str::ascii(trim($enviar)));
            if ($this->palabraClaveYaCubiertaPorMensaje($normalizadoUsuario, $envNorm)) {
                continue;
            }
            $push(Str::limit($e->titulo, 72, '…'), $enviar, $e);
            if (count($list) >= $max) {
                return $list;
            }
        }

        return array_slice($list, 0, $max);
    }

    /**
     * Si el usuario venía de un módulo operativo y esta respuesta cayó en `general`, no ofrecer
     * chips de “qué es el sistema” o listados genéricos que desvían del hilo.
     *
     * @param  array{ultimo_modulo: string|null, ultimos_cod_entrada_ayuda: list<int>}  $estadoPrevio
     */
    private function debeOmitirSugerenciaGeneralPorHilo(
        ChatEntradaAyuda $match,
        ChatEntradaAyuda $candidata,
        array $estadoPrevio
    ): bool {
        if ($match->modulo !== 'general' || $candidata->modulo !== 'general') {
            return false;
        }
        $ctx = $estadoPrevio['ultimo_modulo'] ?? null;
        if ($ctx === null || $ctx === '' || $ctx === 'general') {
            return false;
        }

        return $this->esEntradaGeneralIntroductoria($candidata);
    }

    private function esEntradaGeneralIntroductoria(ChatEntradaAyuda $e): bool
    {
        $t = Str::lower(Str::ascii(trim($e->titulo)));
        if ($t === '') {
            return false;
        }
        $patrones = [
            'que es talent sphere',
            'modulos que encontraras',
            'como usar este asistente',
            'modulos que encontraras en la aplicacion',
        ];
        foreach ($patrones as $p) {
            if (str_contains($t, $p)) {
                return true;
            }
        }

        return false;
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
        $ancla = $this->resolverEntradaAnclaParaSugerencias($modulo, $estadoPrevio, $entradas, $normalizadoUsuario);
        if (! $ancla instanceof ChatEntradaAyuda) {
            return [];
        }

        return $this->construirSugerenciasRelacionadas($ancla, $entradas, $estadoPrevio, $normalizadoUsuario);
    }

    /**
     * Tema de referencia para chips cuando no hubo match en diccionario: última guía tocada en este
     * módulo en el hilo; si no hay, la guía que mejor puntúe con el texto actual; si no, la primera
     * por orden en ese módulo.
     *
     * @param  array{ultimo_modulo: string|null, ultimos_cod_entrada_ayuda: list<int>}  $estadoPrevio
     */
    private function resolverEntradaAnclaParaSugerencias(
        string $modulo,
        array $estadoPrevio,
        Collection $entradas,
        string $normalizadoUsuario
    ): ?ChatEntradaAyuda {
        foreach (array_reverse($estadoPrevio['ultimos_cod_entrada_ayuda']) as $cod) {
            $e = $entradas->first(fn (ChatEntradaAyuda $x) => (int) $x->cod_entrada_ayuda === (int) $cod);
            if ($e instanceof ChatEntradaAyuda && $e->modulo === $modulo) {
                return $e;
            }
        }

        $estadoNeutro = ['ultimo_modulo' => null, 'ultimos_cod_entrada_ayuda' => []];
        $mejor = null;
        $mejorPuntaje = -1;
        foreach ($entradas as $e) {
            if ($e->modulo !== $modulo) {
                continue;
            }
            $p = $this->puntuarEntrada($normalizadoUsuario, $e, $estadoNeutro);
            if ($p > $mejorPuntaje) {
                $mejorPuntaje = $p;
                $mejor = $e;
            }
        }
        if ($mejor instanceof ChatEntradaAyuda) {
            return $mejor;
        }

        return $entradas->filter(fn (ChatEntradaAyuda $e) => $e->modulo === $modulo)
            ->sort(function (ChatEntradaAyuda $a, ChatEntradaAyuda $b): int {
                $o = (int) $a->orden <=> (int) $b->orden;

                return $o !== 0 ? $o : (int) $a->cod_entrada_ayuda <=> (int) $b->cod_entrada_ayuda;
            })
            ->first();
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

    private function intentarOpenAi(int $codChatConversacion, Collection $entradas, ?string $moduloActivo): ?string
    {
        $max = max(2, (int) config('chatbot.max_mensajes_contexto', 12));
        $historial = $this->chatRepository->listarMensajesDeConversacion($codChatConversacion, 500);
        $ultimos = $historial->slice(-$max)->values();

        $system = $this->construirSystemPrompt($entradas, $moduloActivo);

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

    private function construirSystemPrompt(Collection $entradas, ?string $moduloActivo): string
    {
        $base = trim((string) config('chatbot.system_prompt_base'));
        if ($base === '') {
            $base = 'Eres el asistente in-app de Talent Sphere (RRHH, Colombia). Español claro y profesional. '
                .'No inventes datos de expediente; orienta sobre el uso del sistema.';
        }

        if ($moduloActivo !== null && $moduloActivo !== '') {
            $base .= "\n\nEl cliente envió `modulo_ayuda` en esta petición: \"{$moduloActivo}\". "
                .'Úsalo como área activa salvo que el historial muestre claramente otra intención.';
        }

        $paraDiccionario = $entradas;
        if ($moduloActivo !== null && $moduloActivo !== '' && $moduloActivo !== 'general') {
            $filtradas = $entradas->filter(
                fn (ChatEntradaAyuda $e) => $e->modulo === $moduloActivo || $e->modulo === 'general'
            );
            if ($filtradas->isNotEmpty()) {
                $paraDiccionario = $filtradas;
            }
        }

        if ($paraDiccionario->isEmpty()) {
            return $base;
        }

        $diccionario = $paraDiccionario->map(function (ChatEntradaAyuda $e) {
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
