# Actualiza solo PASSWORD_RESET_URL en Azure (lee .env). No reconstruye la imagen.
# Usar tras cambiar el enlace del correo; para codigo nuevo sigue siendo push + deploy.

$ErrorActionPreference = "Stop"

$ResourceGroup = "api"
$ContainerApp = "mi-api"
$ProjectRoot = Split-Path -Parent $PSScriptRoot
$EnvFile = Join-Path $ProjectRoot ".env"

if (-not (Test-Path $EnvFile)) {
    throw "No se encontro .env en $ProjectRoot"
}

$passwordResetUrl = $null
Get-Content $EnvFile | ForEach-Object {
    $line = $_.Trim()
    if ($line -match '^\s*PASSWORD_RESET_URL\s*=\s*(.+)\s*$') {
        $passwordResetUrl = $Matches[1].Trim().Trim('"')
    }
}

if ([string]::IsNullOrWhiteSpace($passwordResetUrl)) {
    $passwordResetUrl = "https://ronald-f18.github.io/PROYECTO-REACT-RRHH/#/cambiar-contrasena"
    Write-Host "PASSWORD_RESET_URL no esta en .env; usando default: $passwordResetUrl"
}

if ($passwordResetUrl -match 'recuperar-contrasena') {
    Write-Host "AVISO: la URL apunta a recuperar-contrasena. En .env usa cambiar-contrasena o reset-password.html"
}

Write-Host "Actualizando PASSWORD_RESET_URL en Azure..."
Write-Host "  $passwordResetUrl"

az containerapp update `
    --name $ContainerApp `
    --resource-group $ResourceGroup `
    --set-env-vars "PASSWORD_RESET_URL=$passwordResetUrl"

if ($LASTEXITCODE -ne 0) {
    throw "Fallo az containerapp update"
}

Write-Host "Listo. Pide un correo NUEVO (forgot-password); los enlaces viejos siguen con la URL anterior."
