@php
    use Illuminate\Support\Carbon;
    /** @var array $reporte */
    $fecha = $reporte['fecha'] instanceof Carbon ? $reporte['fecha'] : Carbon::parse($reporte['fecha']);
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte RRHH - {{ $reporte['modulo'] ?? '' }}</title>
    <style>
        @page { margin: 30px 32px 40px 32px; }
        *{ box-sizing:border-box; font-family: "DejaVu Sans", Arial, sans-serif; }
        body{ font-size:11px; color:#111827; background:#ffffff; }
        .paper{ border:1px solid #d1d5db; border-radius:10px; padding:0; }
        .p-empresa{ padding:16px 26px 10px 26px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; }
        .pe-nm{ font-size:15px; font-weight:700; margin-bottom:2px; }
        .pe-nit{ font-size:10px; color:#6b7280; margin-bottom:2px; }
        .pe-addr{ font-size:9px; color:#9ca3af; }
        .pe-r{ text-align:right; }
        .sello{ display:inline-block; border-radius:14px; border:1px solid #2563eb; color:#2563eb; padding:2px 9px; font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; margin-bottom:4px; }
        .pe-fecha{ font-size:10px; color:#374151; margin-bottom:2px; }
        .pe-ref{ font-size:9px; font-family:monospace; color:#6b7280; background:#f3f4f6; padding:1px 6px; border-radius:3px; display:inline-block; }
        .p-tbar{ background:#111827; color:#fff; padding:10px 26px; display:flex; justify-content:space-between; }
        .pt-t{ font-size:13px; font-weight:700; }
        .pt-s{ font-size:9px; color:#d1d5db; margin-top:2px; }
        .pt-ref{ font-size:9px; font-family:monospace; color:#9ca3af; margin-left:12px; }
        .p-meta{ border-bottom:1px solid #e5e7eb; display:flex; flex-wrap:wrap; }
        .pm{ padding:7px 14px; border-right:1px solid #e5e7eb; min-width:90px; }
        .pm-l{ font-size:8px; text-transform:uppercase; letter-spacing:.08em; color:#9ca3af; margin-bottom:2px; }
        .pm-v{ font-size:10px; font-weight:600; color:#111827; }
        .p-params{ padding:8px 26px; background:#eff6ff; border-bottom:1px solid #bfdbfe; }
        .pp-l{ font-size:9px; font-weight:700; color:#1d4ed8; text-transform:uppercase; margin-right:6px; }
        .pp-tag{ display:inline-block; padding:1px 7px; border-radius:999px; border:1px solid #bfdbfe; background:#ffffff; font-size:9px; color:#1d4ed8; margin:1px 3px 1px 0; }
        .p-body{ padding:16px 26px 18px 26px; }
        .tot-row{ display:grid; grid-template-columns:repeat(4, minmax(0,1fr)); gap:8px; margin-bottom:16px; }
        .tot-box{ border-radius:7px; border:1px solid #dbeafe; background:#eff6ff; padding:7px 9px; text-align:center; }
        .tot-l{ font-size:8px; text-transform:uppercase; letter-spacing:.08em; color:#1e40af; margin-bottom:2px; }
        .tot-v{ font-size:14px; font-weight:700; color:#111827; }
        .tot-s{ font-size:8px; color:#6b7280; margin-top:1px; }
        .rpt-sec{ margin-bottom:18px; page-break-inside:avoid; }
        .sec-hd{ display:flex; align-items:center; margin-bottom:6px; }
        .sec-num{ width:16px; height:16px; border-radius:4px; background:#111827; color:#fff; font-size:8px; font-weight:700; display:flex; align-items:center; justify-content:center; margin-right:6px; }
        .sec-tt{ font-size:10px; font-weight:700; color:#111827; text-transform:uppercase; letter-spacing:.06em; }
        .sec-ln{ flex:1; height:1px; background:#e5e7eb; margin-left:6px; }
        table{ width:100%; border-collapse:collapse; font-size:9px; }
        thead tr{ background:#111827; color:#e5e7eb; }
        th{ padding:5px 6px; text-align:left; text-transform:uppercase; letter-spacing:.04em; font-weight:600; }
        td{ padding:5px 6px; border-bottom:1px solid #e5e7eb; }
        tbody tr:nth-child(even){ background:#f9fafb; }
        .td-num{ text-align:right; font-variant-numeric:tabular-nums; }
        .td-hi{ font-weight:600; color:#1d4ed8; }
        .p-firma{ margin-top:20px; padding-top:12px; border-top:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:flex-end; }
        .nota{ font-size:8px; color:#9ca3af; max-width:260px; line-height:1.4; }
        .firma-bx{ text-align:center; }
        .firma-ln{ width:150px; border-top:1px solid #374151; margin:0 auto 4px auto; }
        .firma-n{ font-size:10px; font-weight:700; }
        .firma-c{ font-size:9px; color:#6b7280; }
        .firma-e{ font-size:8px; color:#9ca3af; margin-top:1px; }
        .p-foot{ margin-top:4px; padding:6px 26px 4px 26px; border-top:1px solid #e5e7eb; display:flex; justify-content:space-between; font-size:8px; color:#9ca3af; }
        .pref{ font-family:monospace; }
    </style>
</head>
<body>
<div class="paper">
    <div class="p-empresa">
        <div>
            <div class="pe-nm">Empresa S.A.S.</div>
            <div class="pe-nit">NIT 900.123.456-7 · Régimen Común</div>
            <div class="pe-addr">Carrera 10 # 20-30, Medellín, Antioquia — Colombia</div>
        </div>
        <div class="pe-r">
            <div class="sello">Reporte Oficial</div>
            <div class="pe-fecha">Medellín, {{ $fecha->translatedFormat('d \\de F \\de Y') }}</div>
            <div class="pe-ref">REF: {{ $reporte['codigo'] ?? '' }}</div>
        </div>
    </div>

    <div class="p-tbar">
        <div>
            <div class="pt-t">{{ $reporte['titulo'] ?? '' }}</div>
            <div class="pt-s">{{ $reporte['subtitulo'] ?? '' }}</div>
        </div>
        <div class="pt-ref">
            Módulo: {{ $reporte['modulo'] ?? '' }}
        </div>
    </div>

    <div class="p-meta">
        <div class="pm">
            <div class="pm-l">Código reporte</div>
            <div class="pm-v">{{ $reporte['codigo'] ?? '-' }}</div>
        </div>
        <div class="pm">
            <div class="pm-l">Fecha generación</div>
            <div class="pm-v">{{ $fecha->format('d/m/Y') }}</div>
        </div>
        @if(!empty($reporte['tipo']))
            <div class="pm">
                <div class="pm-l">Tipo</div>
                <div class="pm-v">{{ $reporte['tipo'] }}</div>
            </div>
        @endif
        @if(!empty($reporte['secciones']))
            <div class="pm">
                <div class="pm-l">Secciones</div>
                <div class="pm-v">{{ count($reporte['secciones']) }}</div>
            </div>
        @endif
    </div>

    @if(!empty($reporte['parametros']))
        <div class="p-params">
            <span class="pp-l">Parámetros</span>
            @foreach($reporte['parametros'] as $k => $v)
                @if($v !== null && $v !== '')
                    <span class="pp-tag">{{ $k }}: {{ $v }}</span>
                @endif
            @endforeach
        </div>
    @endif

    <div class="p-body">
        @if(!empty($reporte['totales']))
            <div class="tot-row">
                @foreach($reporte['totales'] as $tot)
                    <div class="tot-box">
                        <div class="tot-l">{{ $tot['label'] ?? '' }}</div>
                        <div class="tot-v">
                            {{ is_numeric($tot['valor'] ?? null) ? number_format($tot['valor'], 0, ',', '.') : ($tot['valor'] ?? '') }}
                        </div>
                        @if(!empty($tot['detalle']))
                            <div class="tot-s">{{ $tot['detalle'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        @foreach($reporte['secciones'] ?? [] as $idx => $sec)
            <div class="rpt-sec">
                <div class="sec-hd">
                    <div class="sec-num">{{ $idx + 1 }}</div>
                    <div class="sec-tt">{{ $sec['titulo'] ?? '' }}</div>
                    <div class="sec-ln"></div>
                </div>
                @if(!empty($sec['columnas']) && !empty($sec['filas']))
                    <table>
                        <thead>
                        <tr>
                            @foreach($sec['columnas'] as $col)
                                <th>{{ $col }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($sec['filas'] as $fila)
                            <tr>
                                @foreach($fila as $i => $celda)
                                    @php
                                        $texto = is_numeric($celda)
                                            ? number_format($celda, 0, ',', '.')
                                            : (string) $celda;
                                        $esNum = $i > 0 && is_numeric(str_replace(['.', ',', '%', '$', ' '], '', $texto));
                                    @endphp
                                    <td class="{{ $esNum ? 'td-num' : ($i === 0 ? 'td-hi' : '') }}">
                                        {{ $texto }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @endforeach

        <div class="p-firma">
            <div class="nota">
                La presente información ha sido generada automáticamente a partir de los datos
                registrados en el Sistema de Gestión de Recursos Humanos. Este reporte es de uso
                interno y no sustituye los certificados laborales ni los documentos legales
                expedidos por la empresa.
            </div>
            <div class="firma-bx">
                <div class="firma-ln"></div>
                <div class="firma-n">Jefe de Recursos Humanos</div>
                <div class="firma-c">Área de Gestión Humana</div>
                <div class="firma-e">Empresa S.A.S. · NIT 900.123.456-7</div>
            </div>
        </div>
    </div>

    <div class="p-foot">
        <span>Sistema RRHH — Módulo de reportes</span>
        <span class="pref">{{ $reporte['codigo'] ?? '' }} · {{ now()->format('Y-m-d H:i') }}</span>
    </div>
</div>
</body>
</html>

