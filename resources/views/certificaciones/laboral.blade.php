<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificación Laboral</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; line-height: 1.6; }
        .encabezado { text-align: left; margin-bottom: 30px; }
        .encabezado h2 { margin: 0; font-size: 16px; }
        .encabezado p { margin: 0; }
        .titulo { text-align: center; text-transform: uppercase; font-size: 14px; margin-bottom: 25px; }
        .firma { margin-top: 50px; text-align: left; }
        .firma p { margin: 0; }
        .negrita { font-weight: bold; }
    </style>
</head>
<body>
    <div class="encabezado">
        <h2>{{ $empresa->razon_social }}</h2>
        <p>NIT {{ $empresa->nit }}-{{ $empresa->dv }}</p>
        <p>{{ $empresa->direccion }} - {{ $empresa->ciudad }} ({{ $empresa->departamento }}) - {{ $empresa->pais }}</p>
        @if($empresa->telefono)
            <p>Teléfono: {{ $empresa->telefono }}</p>
        @endif
        @if($empresa->correo)
            <p>Correo: {{ $empresa->correo }}</p>
        @endif
    </div>

    <p style="text-align: right;">
        {{ $certificacion->ciudad_emision }},
        {{ \Carbon\Carbon::parse($certificacion->fecha_emision)->translatedFormat('d \\de F \\de Y') }}
    </p>

    <p class="titulo"><span class="negrita">CERTIFICACIÓN LABORAL</span></p>

    <p><span class="negrita">A QUIEN INTERESE:</span></p>

    <p>
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
        <p>
            Actualmente devenga un salario básico mensual de
            <span class="negrita">
                $ {{ number_format($certificacion->salario_certificado, 0, ',', '.') }} M/CTE
            </span>,
            más los recargos y prestaciones de ley.
        </p>
    @endif

    @if($certificacion->descripcion)
        <p>
            Observaciones: {{ $certificacion->descripcion }}
        </p>
    @endif

    <p>
        La presente certificación se expide a solicitud del(la) interesado(a) para los fines que
        estime convenientes, y se ajusta a las disposiciones laborales vigentes en la República de Colombia.
    </p>

    <div class="firma">
        <p>Cordialmente,</p>
        <br><br>
        <p class="negrita">{{ $empresa->nombre_representante }}</p>
        <p>Representante Legal</p>
        <p>{{ $empresa->razon_social }}</p>
    </div>
</body>
</html>

