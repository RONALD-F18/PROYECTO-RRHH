param(
    [string]$ImageTag = "v2"
)

$ErrorActionPreference = "Stop"

$ResourceGroup = "api"
$AcrName = "acrronaldsena2026"
$ImageName = "mi-api"
$ProjectRoot = Split-Path -Parent $PSScriptRoot
$RemoteImage = "$AcrName.azurecr.io/${ImageName}:$ImageTag"

function Assert-CommandSuccess {
    param([string]$Step)

    if ($LASTEXITCODE -ne 0) {
        throw "Fallo en: $Step"
    }
}

Set-Location $ProjectRoot

Write-Host "Comprobando Docker Desktop..."
docker info *> $null
Assert-CommandSuccess "Docker Desktop no esta en ejecucion. Abrelo y espera a que quede listo."

Write-Host "Iniciando sesion en ACR..."
az acr login --name $AcrName *> $null
Assert-CommandSuccess "az acr login"

Write-Host "Construyendo imagen local..."
docker build -t $ImageName .
Assert-CommandSuccess "docker build"

Write-Host "Etiquetando imagen..."
docker tag $ImageName $RemoteImage
Assert-CommandSuccess "docker tag"

Write-Host "Subiendo imagen a ACR..."
docker push $RemoteImage
Assert-CommandSuccess "docker push"

Write-Host "Imagen lista: $RemoteImage"
Write-Host "Siguiente paso:"
Write-Host "  .\scripts\azure-deploy.ps1 -SkipBuild -ImageTag $ImageTag"
