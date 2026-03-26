<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificación laboral</title>
    <style>
        @page { margin: 2.4cm 2.2cm 2.2cm 2.2cm; }
        * { box-sizing: border-box; }
        body {
            font-family: "DejaVu Sans", Arial, Helvetica, sans-serif;
            font-size: 10.5pt;
            line-height: 1.55;
            color: #111;
            margin: 0;
            position: relative;
        }
        .watermark {
            position: fixed;
            top: 42%;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 92pt;
            font-weight: 700;
            color: #c8c8c8;
            opacity: 0.12;
            z-index: 0;
            letter-spacing: 0.02em;
            pointer-events: none;
        }
        .doc {
            position: relative;
            z-index: 1;
        }
        .logo-zone {
            text-align: center;
            margin-bottom: 6px;
        }
        .logo-text {
            font-size: 13pt;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin: 0;
            color: #1a1a1a;
        }
        .rule {
            border: none;
            border-top: 1px solid #333;
            margin: 10px 0 22px 0;
        }
        .certifica {
            text-align: center;
            font-size: 16pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.28em;
            margin: 18px 0 16px 0;
        }
        .a-quien {
            text-align: center;
            margin: 0 0 16px 0;
            font-size: 10.5pt;
        }
        p { margin: 0 0 12px 0; }
        .bloque {
            text-align: justify;
            text-justify: inter-word;
        }
        .destacado {
            font-weight: 700;
            text-transform: uppercase;
        }
        .constancia {
            margin-top: 20px;
            text-align: left;
        }
        .firmas-wrap {
            width: 100%;
            margin-top: 32px;
            border-collapse: collapse;
        }
        .firmas-wrap td {
            width: 50%;
            vertical-align: top;
            padding: 0 12px 0 0;
        }
        .firmas-wrap td:last-child {
            padding: 0 0 0 12px;
        }
        .firma-linea {
            border-top: 1px solid #111;
            width: 100%;
            max-width: 240px;
            margin: 36px 0 8px 0;
        }
        .firma-nombre {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9.5pt;
            margin: 0 0 2px 0;
        }
        .firma-cargo {
            font-size: 9.5pt;
            margin: 0;
        }
        .sello {
            border: 2px double #333;
            padding: 14px 12px;
            text-align: center;
            min-height: 100px;
        }
        .sello-nombre {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9pt;
            line-height: 1.3;
            margin: 0 0 10px 0;
        }
        .sello-nit {
            font-weight: 700;
            font-size: 10pt;
            margin: 0;
        }
        .footer-rule {
            border: none;
            border-top: 1px solid #333;
            margin: 28px 0 10px 0;
        }
        .footer-info {
            text-align: center;
            font-size: 8.5pt;
            color: #222;
            line-height: 1.45;
            margin: 0;
        }
        .nota-pie {
            font-size: 7.5pt;
            color: #555;
            text-align: justify;
            margin-top: 14px;
        }
    </style>
</head>
<body>
@php
    $fechaEmision = \Carbon\Carbon::parse($certificacion->fecha_emision);
    $fIngreso = $contrato
        ? \Carbon\Carbon::parse($contrato->fecha_ingreso ?? $contrato->fecha_inicio ?? $fechaEmision)
        : $fechaEmision;
    $fFin = ($contrato && !empty($contrato->fecha_fin)) ? \Carbon\Carbon::parse($contrato->fecha_fin) : null;
    $nitLinea = $empresa->nit . '-' . $empresa->dv;
    $razonLimpia = rtrim(trim((string) $empresa->razon_social), ". \t");
    $razonSocialTitulo = mb_strtoupper($razonLimpia, 'UTF-8');
    $nombreEmpleado = trim(($empleado->nombre_empleado ?? '') . ' ' . ($empleado->apellidos_empleado ?? '')) ?: ($empleado->nombre_completo ?? '');
    $docEmpleado = trim((string) ($empleado->doc_iden ?? $empleado->documento ?? ''));
    $nombreCargo = '';
    if ($contrato && $contrato->cargo) {
        $nombreCargo = $contrato->cargo->nomb_cargo ?? $contrato->cargo->nombre_cargo ?? '';
    }
    $iniciales = collect(preg_split('/\s+/', $razonLimpia))->filter()->map(fn ($p) => mb_substr($p, 0, 1))->take(5)->implode('');
    $marcaAgua = $iniciales !== '' ? mb_strtoupper($iniciales, 'UTF-8') : mb_strtoupper(mb_substr($razonLimpia, 0, 3), 'UTF-8');
@endphp

<div class="watermark">{{ $marcaAgua }}</div>

<div class="doc">
    <div class="logo-zone">
        <p class="logo-text">{{ $razonSocialTitulo }}</p>
    </div>
    <hr class="rule">

    <p class="certifica">Certifica</p>

    <p class="a-quien">A quien interese</p>

    <p class="bloque">
        @if($empresa->nombre_representante)
            La empresa <span class="destacado">{{ $razonSocialTitulo }}</span>,
            en nombre de su representante legal
            <span class="destacado">{{ mb_strtoupper(trim($empresa->nombre_representante), 'UTF-8') }}</span>,
            identificado(a) con cédula de ciudadanía No.
            <span class="destacado">{{ $empresa->documento_representante ?? '—' }}</span>,
            hace constar que el(la) señor(a)
        @else
            La empresa <span class="destacado">{{ $razonSocialTitulo }}</span>,
            identificada con NIT <span class="destacado">{{ $nitLinea }}</span>, por medio de la presente hace constar que el(la) señor(a)
        @endif
        <span class="destacado">{{ mb_strtoupper($nombreEmpleado, 'UTF-8') }}</span>,
        identificado(a) con cédula de ciudadanía No.
        <span class="destacado">{{ $docEmpleado }}</span>,
        labora para esta empresa desempeñando el cargo de
        <span class="destacado">{{ mb_strtoupper($nombreCargo, 'UTF-8') }}</span>,
        vinculado(a) mediante contrato de
        <span class="destacado">{{ $contrato ? ($contrato->tipo_contrato ?? 'término indefinido') : 'término indefinido' }}</span>
        desde el día
        <span class="destacado">{{ $fIngreso->translatedFormat('d \\de F \\de Y') }}</span>
        @if($fFin)
            hasta el día
            <span class="destacado">{{ $fFin->translatedFormat('d \\de F \\de Y') }}</span>
        @endif
        .
    </p>

    @if($certificacion->incluye_salario && $certificacion->salario_certificado)
        <p class="bloque">
            Actualmente devenga un salario básico mensual de
            <span class="destacado">
                $ {{ number_format($certificacion->salario_certificado, 0, ',', '.') }}
            </span>,
            más las prestaciones sociales de ley.
        </p>
    @endif

    @if($certificacion->descripcion)
        <p class="bloque">
            {{ $certificacion->descripcion }}
        </p>
    @endif

    <p class="bloque">
        La presente certificación se expide a solicitud del(la) interesado(a), para los fines que estime conveniente,
        y se expide de conformidad con la legislación laboral vigente en la República de Colombia.
    </p>

    <p class="constancia bloque">
        Para constancia se firma en
        <strong>{{ $certificacion->ciudad_emision }}</strong>,
        a los
        <strong>{{ $fechaEmision->format('d') }}</strong>
        días del mes de
        <strong>{{ mb_strtoupper($fechaEmision->translatedFormat('F'), 'UTF-8') }}</strong>
        del
        <strong>{{ $fechaEmision->format('Y') }}</strong>.
    </p>

    <table class="firmas-wrap">
        <tr>
            <td>
                <div class="firma-linea"></div>
                <p class="firma-nombre">
                    @if($empresa->nombre_representante)
                        {{ mb_strtoupper(trim($empresa->nombre_representante), 'UTF-8') }}
                    @else
                        &nbsp;
                    @endif
                </p>
                <p class="firma-cargo">Representante Legal</p>
            </td>
            <td>
                <div class="sello">
                    <p class="sello-nombre">{{ $razonSocialTitulo }}</p>
                    <p class="sello-nit">NIT. {{ $nitLinea }}</p>
                </div>
            </td>
        </tr>
    </table>

    <hr class="footer-rule">
    <p class="footer-info">
        {{ $razonLimpia }}
        /
        @php
            $dir = array_filter([$empresa->direccion, $empresa->ciudad, $empresa->departamento]);
        @endphp
        {{ implode(', ', $dir) }}
        @if($empresa->pais && strtolower((string) $empresa->pais) !== 'colombia')
            — {{ $empresa->pais }}
        @endif
    </p>

    <p class="nota-pie">
        Documento expedido con base en los registros internos de la empresa a la fecha indicada. No constituye referencia
        comercial ni certificación de comportamiento financiero.
    </p>
</div>
</body>
</html>
