# Build, push y deploy tag v6 (Sanctum + fixes auth()->user)
param(
    [string]$ImageTag = "v6"
)

$ErrorActionPreference = "Stop"
$ProjectRoot = Split-Path -Parent $PSScriptRoot

Set-Location $ProjectRoot
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass -Force

Write-Host "=== 1/3 Docker push $ImageTag ===" -ForegroundColor Cyan
& "$PSScriptRoot\azure-push-docker.ps1" -ImageTag $ImageTag

Write-Host "=== 2/3 Azure deploy $ImageTag ===" -ForegroundColor Cyan
& "$PSScriptRoot\azure-deploy.ps1" -SkipBuild -ImageTag $ImageTag

Write-Host "=== 3/3 Estado + pruebas API ===" -ForegroundColor Cyan
& "$PSScriptRoot\azure-status.ps1"
& "$PSScriptRoot\azure-test-login.ps1"

Write-Host ""
Write-Host "Listo. Imagen: acrronaldsena2026.azurecr.io/mi-api:$ImageTag" -ForegroundColor Green
