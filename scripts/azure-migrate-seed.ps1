param(
    [switch]$SeedOnly,
    [switch]$MigrateOnly
)

$ErrorActionPreference = "Stop"
$ResourceGroup = "api"
$ContainerApp = "mi-api"

function Invoke-ContainerArtisan {
    param([string]$ArtisanArgs)

    $cmd = "php artisan $ArtisanArgs"
    Write-Host "Ejecutando en contenedor: $cmd"
    az containerapp exec `
        --name $ContainerApp `
        --resource-group $ResourceGroup `
        --command $cmd
    if ($LASTEXITCODE -ne 0) {
        throw "Fallo: $cmd"
    }
}

if (-not $SeedOnly) {
    Invoke-ContainerArtisan "migrate --force --no-interaction"
}

if (-not $MigrateOnly) {
    Invoke-ContainerArtisan "db:seed --force"
}

Write-Host "Listo."
