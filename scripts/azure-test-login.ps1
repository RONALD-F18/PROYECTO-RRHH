param(
    [string]$ApiBase = "https://mi-api.lemonforest-f9c17ad2.eastus.azurecontainerapps.io/api/v1",
    [string]$Email = "ronaldacademy223@gmail.com",
    [string]$Password = "Donald1234",
    [switch]$SoloLogin
)

$ErrorActionPreference = "Stop"

$body = @{
    email_usuario      = $Email
    contrasena_usuario = $Password
} | ConvertTo-Json

Write-Host "POST $ApiBase/login"
Write-Host "Usuario: $Email"
Write-Host ""

try {
    $login = Invoke-RestMethod `
        -Uri "$ApiBase/login" `
        -Method Post `
        -ContentType "application/json; charset=utf-8" `
        -Headers @{ Accept = "application/json" } `
        -Body $body
} catch {
    $status = $_.Exception.Response.StatusCode.value__
    $reader = [System.IO.StreamReader]::new($_.Exception.Response.GetResponseStream())
    $detalle = $reader.ReadToEnd()
    Write-Host "Error HTTP $status"
    Write-Host $detalle
    exit 1
}

$login | ConvertTo-Json -Depth 6

if (-not $login.access_token -and -not $login.token) {
    Write-Host ""
    Write-Host "FALLO: la respuesta no trae access_token ni token." -ForegroundColor Red
    exit 1
}

$token = if ($login.access_token) { $login.access_token } else { $login.token }
Write-Host ""
Write-Host "OK: token recibido (Sanctum Bearer)." -ForegroundColor Green
Write-Host "Rol: $($login.role)"
Write-Host "token_type: $($login.token_type)"

if ($SoloLogin) {
    exit 0
}

Write-Host ""
Write-Host "GET $ApiBase/empleados (con Bearer)"
try {
    $empleados = Invoke-RestMethod `
        -Uri "$ApiBase/empleados" `
        -Method Get `
        -Headers @{
            Accept        = "application/json"
            Authorization = "Bearer $token"
        }
    Write-Host "OK: empleados respondio (primer registro o lista vacia)." -ForegroundColor Green
    if ($empleados -is [array]) {
        Write-Host "Cantidad: $($empleados.Count)"
    }
} catch {
    $status = $_.Exception.Response.StatusCode.value__
    Write-Host "empleados -> HTTP $status (revisa token o migracion personal_access_tokens)" -ForegroundColor Yellow
    exit 1
}

$headers = @{
    Accept        = "application/json"
    Authorization = "Bearer $token"
}

Write-Host ""
Write-Host "GET $ApiBase/usuarios (solo administrador)"
try {
    $usuarios = Invoke-RestMethod -Uri "$ApiBase/usuarios" -Method Get -Headers $headers
    Write-Host "OK: usuarios listados." -ForegroundColor Green
} catch {
    $status = $_.Exception.Response.StatusCode.value__
    Write-Host "usuarios -> HTTP $status (403=sin rol admin; 500=revisar deploy v6+)" -ForegroundColor Yellow
    if ($status -eq 500) { exit 1 }
}
