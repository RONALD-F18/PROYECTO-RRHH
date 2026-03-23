<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificación Laboral</title>
    <style>
        @page { margin: 30px 32px 40px 32px; }
        * { box-sizing: border-box; }
        body { font-family: "DejaVu Sans", Arial, sans-serif; font-size: 11px; line-height: 1.7; color:#111827; }
        .paper { border: 1px solid #d1d5db; border-radius: 10px; padding: 22px 28px 26px 28px; }
        .hdr { display:flex; justify-content:space-between; border-bottom:1px solid #e5e7eb; padding-bottom:10px; margin-bottom:16px; }
        .hdr-left h2 { margin:0 0 2px 0; font-size:16px; font-weight:700; }
        .hdr-left p { margin:0; font-size:9px; color:#6b7280; }
        .hdr-right { text-align:right; }
        .hdr-tag { display:inline-block; padding:2px 9px; border-radius:999px; border:1px solid #2563eb; font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#2563eb; margin-bottom:5px; }
        .hdr-meta { font-size:9px; color:#6b7280; }
        .fecha { text-align:right; font-size:10px; margin-bottom:18px; }
        .titulo { text-align:center; text-transform:uppercase; font-size:14px; margin-bottom:18px; letter-spacing:.08em; }
        .saludo { margin-bottom:10px; font-weight:700; }
        p { margin:0 0 10px 0; }
        .negrita { font-weight:600; }
        .bloque { margin-bottom:10px; text-align:justify; }
        .nota { font-size:9px; color:#9ca3af; margin-top:12px; text-align:justify; }
        .firma { margin-top:40px; text-align:left; }
        .firma p { margin:0; }
        .firma-nombre { margin-top:4px; font-weight:700; }
        .firma-linea { width:170px; border-top:1px solid #374151; margin-bottom:6px; }
    </style>
</head>
<body>
<div class="paper">
    <div class="hdr">
        <div class="hdr-left">
            <h2>{{ $empresa->razon_social }}</h2>
            <p>NIT {{ $empresa->nit }}-{{ $empresa->dv }}</p>
            <p>{{ $empresa->direccion }} — {{ $empresa->ciudad }} ({{ $empresa->departamento }}) · {{ $empresa->pais }}</p>
            @if($empresa->telefono)
                <p>Teléfono: {{ $empresa->telefono }}</p>
            @endif
            @if($empresa->correo)
                <p>Correo: {{ $empresa->correo }}</p>
            @endif
        </div>
        <div class="hdr-right">
            <div class="hdr-tag">Certificación laboral</div>
            <div class="hdr-meta">
                {{ $certificacion->ciudad_emision }},
                {{ \Carbon\Carbon::parse($certificacion->fecha_emision)->translatedFormat('d \\de F \\de Y') }}
            </div>
        </div>
    </div>

    <p class="titulo"><span class="negrita">Certificación laboral</span></p>

    <p class="saludo">A QUIEN INTERESE:</p>

    <p class="bloque">
        La empresa <span class="negrita">{{ $empresa->razon_social }}</span>, identificada con NIT
        <span class="negrita">{{ $empresa->nit }}-{{ $empresa->dv }}</span>, por medio de la presente
        certifica que el(la) señor(a)
        <span class="negrita">{{ $empleado->nombre_completo ?? '' }}</span>,
        identificado(a) con cédula de ciudadanía No.
        <span class="negrita">{{ $empleado->documento ?? '' }}</span>,
        labora en nuestra organización desempeñando el cargo de
        <span class="negrita">{{ $contrato->cargo->nombre_cargo ?? '' }}</span>,
        vinculado(a) mediante contrato
        <span class="negrita">{{ $contrato->tipo_contrato ?? 'Término Indefinido' }}</span>
        desde el día
        <span class="negrita">
            {{ \Carbon\Carbon::parse($contrato->fecha_ingreso ?? $contrato->fecha_inicio)->translatedFormat('d \\de F \\de Y') }}
        </span>.
    </p>

    @if($certificacion->incluye_salario && $certificacion->salario_certificado)
        <p class="bloque">
            Actualmente devenga un salario básico mensual de
            <span class="negrita">
                $ {{ number_format($certificacion->salario_certificado, 0, ',', '.') }} M/CTE
            </span>,
            más los recargos y prestaciones de ley.
        </p>
    @endif

    @if($certificacion->descripcion)
        <p class="bloque">
            Observaciones: {{ $certificacion->descripcion }}
        </p>
    @endif

    <p class="bloque">
        La presente certificación se expide a solicitud del(la) interesado(a) para los fines que
        estime convenientes y se ajusta a las disposiciones laborales vigentes en la República de Colombia.
    </p>

    <p class="nota">
        Este documento se emite con base en los registros internos de la empresa a la fecha indicada y
        no constituye constancia de comportamiento financiero ni referencia comercial.
    </p>

    <div class="firma">
        <p>Cordialmente,</p>
        <br><br>
        <div class="firma-linea"></div>
        <p class="firma-nombre">{{ $empresa->nombre_representante }}</p>
        <p>Representante Legal</p>
        <p>{{ $empresa->razon_social }}</p>
    </div>
</div>
</body>
</html>

