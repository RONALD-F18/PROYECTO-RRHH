<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificación de afiliaciones</title>
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
            pointer-events: none;
        }
        .doc { position: relative; z-index: 1; }
        .logo-text {
            text-align: center;
            font-size: 13pt;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin: 0 0 6px 0;
        }
        .rule {
            border: none;
            border-top: 1px solid #333;
            margin: 10px 0 22px 0;
        }
        .certifica {
            text-align: center;
            font-size: 15pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.22em;
            margin: 18px 0 16px 0;
        }
        .a-quien { text-align: center; margin: 0 0 16px 0; }
        p { margin: 0 0 12px 0; }
        .bloque { text-align: justify; }
        .destacado { font-weight: 700; text-transform: uppercase; }
        table.afil {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0 8px 0;
            font-size: 10pt;
        }
        table.afil th, table.afil td {
            border: 1px solid #333;
            padding: 8px 10px;
            text-align: left;
            vertical-align: top;
        }
        table.afil thead th {
            background: #f0f0f0;
            font-weight: 700;
        }
        table.afil th.col-ent { width: 22%; }
        table.afil th.col-nom { width: 43%; }
        table.afil th.col-fec { width: 35%; }
        table.afil tbody th {
            background: #f5f5f5;
            font-weight: 700;
        }
        .constancia { margin-top: 18px; }
        .firmas-wrap { width: 100%; margin-top: 28px; border-collapse: collapse; }
        .firmas-wrap td { width: 50%; vertical-align: top; padding: 0 12px 0 0; }
        .firmas-wrap td:last-child { padding: 0 0 0 12px; }
        .firma-linea {
            border-top: 1px solid #111;
            max-width: 240px;
            margin: 36px 0 8px 0;
        }
        .firma-nombre { font-weight: 700; text-transform: uppercase; font-size: 9.5pt; margin: 0 0 2px 0; }
        .firma-cargo { font-size: 9.5pt; margin: 0; }
        .sello {
            border: 2px double #333;
            padding: 14px 12px;
            text-align: center;
            min-height: 100px;
        }
        .sello-nombre { font-weight: 700; text-transform: uppercase; font-size: 9pt; margin: 0 0 10px 0; }
        .sello-nit { font-weight: 700; font-size: 10pt; margin: 0; }
        .footer-rule { border: none; border-top: 1px solid #333; margin: 26px 0 10px 0; }
        .footer-info { text-align: center; font-size: 8.5pt; line-height: 1.45; margin: 0; }
        .nota-pie { font-size: 7.5pt; color: #555; text-align: justify; margin-top: 14px; }
    </style>
</head>
<body>
@php
    $afiliacionRegistro = $afiliacionRegistro ?? null;
    $fechaEmision = \Carbon\Carbon::parse($certificacion->fecha_emision);
    $nitLinea = $empresa->nit . '-' . $empresa->dv;
    $razonLimpia = rtrim(trim((string) $empresa->razon_social), ". \t");
    $razonSocialTitulo = mb_strtoupper($razonLimpia, 'UTF-8');
    $nombreEmpleado = trim(($empleado->nombre_empleado ?? '') . ' ' . ($empleado->apellidos_empleado ?? '')) ?: ($empleado->nombre_completo ?? '');
    $docEmpleado = trim((string) ($empleado->doc_iden ?? $empleado->documento ?? ''));
    $iniciales = collect(preg_split('/\s+/', $razonLimpia))->filter()->map(fn ($p) => mb_substr($p, 0, 1))->take(5)->implode('');
    $marcaAgua = $iniciales !== '' ? mb_strtoupper($iniciales, 'UTF-8') : mb_strtoupper(mb_substr($razonLimpia, 0, 3), 'UTF-8');

    $fmtAfil = function ($fecha) {
        if (empty($fecha)) {
            return '—';
        }
        return \Carbon\Carbon::parse($fecha)->translatedFormat('d \\de F \\de Y');
    };

    $mismaEps = $afiliacionRegistro && (int) $afiliacionRegistro->cod_eps === (int) $certificacion->cod_eps;
    $mismaArl = $afiliacionRegistro && (int) $afiliacionRegistro->cod_arl === (int) $certificacion->cod_arl;
    $mismaPension = $afiliacionRegistro && (int) $afiliacionRegistro->cod_fondo_pensiones === (int) $certificacion->cod_pension;
    $mismaCaja = $afiliacionRegistro && (int) $afiliacionRegistro->cod_caja_compensacion === (int) $certificacion->cod_caja;
    $mismaCesantias = $afiliacionRegistro && (int) $afiliacionRegistro->cod_fondo_cesantias === (int) $certificacion->cod_cesantias;

    $filas = [];
    if ($certificacion->eps) {
        $fec = ($mismaEps && $afiliacionRegistro) ? $afiliacionRegistro->fecha_afiliacion_eps : null;
        $filas[] = ['EPS', $certificacion->eps->nombre_eps ?? '—', $fmtAfil($fec)];
    }
    if ($certificacion->arl) {
        $fec = ($mismaArl && $afiliacionRegistro) ? $afiliacionRegistro->fecha_afiliacion_arl : null;
        $filas[] = ['ARL', $certificacion->arl->nombre_arl ?? '—', $fmtAfil($fec)];
    }
    if ($certificacion->fondoPension) {
        $fec = ($mismaPension && $afiliacionRegistro) ? $afiliacionRegistro->fecha_afiliacion_fondo_pensiones : null;
        $filas[] = ['Fondo de pensiones', $certificacion->fondoPension->nombre_fondo_pension ?? '—', $fmtAfil($fec)];
    }
    if ($certificacion->cajaCompensacion) {
        $fec = ($mismaCaja && $afiliacionRegistro) ? $afiliacionRegistro->fecha_afiliacion_caja : null;
        $filas[] = ['Caja de compensación', $certificacion->cajaCompensacion->nombre_caja_compensacion ?? '—', $fmtAfil($fec)];
    }
    if ($certificacion->fondoCesantias) {
        $fec = ($mismaCesantias && $afiliacionRegistro) ? $afiliacionRegistro->fecha_afiliacion_fondo_cesantias : null;
        $filas[] = ['Fondo de cesantías', $certificacion->fondoCesantias->nombre_fondo_cesantia ?? '—', $fmtAfil($fec)];
    }
@endphp

<div class="watermark">{{ $marcaAgua }}</div>

<div class="doc">
    <p class="logo-text">{{ $razonSocialTitulo }}</p>
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
            identificada con NIT <span class="destacado">{{ $nitLinea }}</span>, hace constar que el(la) señor(a)
        @endif
        <span class="destacado">{{ mb_strtoupper($nombreEmpleado, 'UTF-8') }}</span>,
        identificado(a) con cédula de ciudadanía No.
        <span class="destacado">{{ $docEmpleado }}</span>,
        @if(count($filas) > 0)
            se encuentra afiliado(a) o vinculado(a), según los registros aportados para este certificado, a las siguientes entidades:
        @else
            solicita constancia de afiliaciones; las entidades no fueron seleccionadas en el registro de esta certificación. Si aplica, consulte el campo de observaciones.
        @endif
    </p>

    @if(count($filas) > 0)
        <table class="afil">
            <thead>
                <tr>
                    <th class="col-ent">Entidad</th>
                    <th class="col-nom">Nombre</th>
                    <th class="col-fec">Fecha de afiliación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($filas as $fila)
                    <tr>
                        <th>{{ $fila[0] }}</th>
                        <td>{{ $fila[1] }}</td>
                        <td>{{ $fila[2] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($certificacion->descripcion)
        <p class="bloque">
            <strong>Observaciones:</strong> {{ $certificacion->descripcion }}
        </p>
    @endif

    <p class="bloque">
        La presente certificación se expide a solicitud del(la) interesado(a) para los fines que estime conveniente,
        con base en la información registrada por la empresa a la fecha de emisión.
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
    </p>

    <p class="nota-pie">
        Las fechas de afiliación provienen del expediente de afiliaciones del empleado cuando la entidad coincide con la consignada en esta certificación; en caso contrario se indica guión (—). Verifique los datos con cada entidad para trámites formales.
    </p>
</div>
</body>
</html>
