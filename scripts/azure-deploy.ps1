param(
    [string]$ImageTag = (Get-Date -Format "yyyyMMdd-HHmmss"),
    [switch]$SkipBuild,
    [switch]$FollowLogs
)

$ErrorActionPreference = "Stop"

$ProjectRoot = Split-Path -Parent $PSScriptRoot
$EnvFile = Join-Path $ProjectRoot ".env"

$ResourceGroup = "api"
$ContainerApp = "mi-api"
$AcrName = "acrronaldsena2026"
$ImageName = "mi-api"
$SubscriptionName = "Azure for Students"
$AppUrl = "https://mi-api.lemonforest-f9c17ad2.eastus.azurecontainerapps.io"
$Image = "$AcrName.azurecr.io/${ImageName}:$ImageTag"

function Read-DotEnv {
    param([string]$Path)

    if (-not (Test-Path $Path)) {
        throw "No se encontro el archivo .env en $Path"
    }

    $values = @{}
    Get-Content $Path | ForEach-Object {
        $line = $_.Trim()
        if ($line -eq "" -or $line.StartsWith("#")) {
            return
        }

        $parts = $line -split "=", 2
        if ($parts.Count -lt 2) {
            return
        }

        $key = $parts[0].Trim()
        $value = $parts[1].Trim()
        if ($value.StartsWith('"') -and $value.EndsWith('"')) {
            $value = $value.Substring(1, $value.Length - 2)
        }

        $values[$key] = $value
    }

    return $values
}

function Require-EnvValue {
    param(
        [hashtable]$Values,
        [string]$Key
    )

    if (-not $Values.ContainsKey($Key) -or [string]::IsNullOrWhiteSpace($Values[$Key])) {
        throw "Falta el valor de $Key en .env"
    }

    return $Values[$Key]
}

function Assert-CommandSuccess {
    param([string]$Step)

    if ($LASTEXITCODE -ne 0) {
        throw "Fallo en: $Step"
    }
}

function Assert-ImageInAcr {
    param(
        [string]$Registry,
        [string]$Repository,
        [string]$Tag
    )

    $tags = az acr repository show-tags `
        --name $Registry `
        --repository $Repository `
        --output tsv 2>$null

    Assert-CommandSuccess "az acr repository show-tags"

    if ($tags -notcontains $Tag) {
        throw "La imagen ${Repository}:$Tag no esta en ACR. Ejecuta primero .\scripts\azure-push-docker.ps1 -ImageTag $Tag"
    }
}

Set-Location $ProjectRoot

if (-not (Test-Path (Join-Path $ProjectRoot "ca.pem"))) {
    throw "Falta ca.pem en la raiz del proyecto. Aiven lo necesita dentro de la imagen."
}

Write-Host "1/5 Suscripcion Azure..."
az account set --subscription $SubscriptionName | Out-Null

$envValues = Read-DotEnv -Path $EnvFile

if (-not $SkipBuild) {
    Write-Host "2/5 Construyendo imagen en ACR ($Image)..."
    try {
        az acr build `
            --registry $AcrName `
            --resource-group $ResourceGroup `
            --image "${ImageName}:$ImageTag" `
            .
    } catch {
        throw @"
No se pudo construir en ACR (suscripcion Azure for Students suele bloquear ACR Tasks).
Usa Docker Desktop y ejecuta:
  .\scripts\azure-push-docker.ps1 -ImageTag $ImageTag
  .\scripts\azure-deploy.ps1 -SkipBuild -ImageTag $ImageTag
"@
    }
} else {
    Write-Host "2/5 Build omitido (-SkipBuild)."
    Assert-ImageInAcr -Registry $AcrName -Repository $ImageName -Tag $ImageTag
}

Write-Host "3/5 Guardando secretos en Container Apps..."
az containerapp secret set `
    --name $ContainerApp `
    --resource-group $ResourceGroup `
    --secrets `
        app-key="$(Require-EnvValue $envValues 'APP_KEY')" `
        db-password="$(Require-EnvValue $envValues 'DB_PASSWORD')" `
        jwt-secret="$(Require-EnvValue $envValues 'JWT_SECRET')" `
        mail-password="$(Require-EnvValue $envValues 'MAIL_PASSWORD')"
Assert-CommandSuccess "az containerapp secret set"

$mailFrom = $envValues["MAIL_FROM_ADDRESS"]
if ([string]::IsNullOrWhiteSpace($mailFrom)) {
    $mailFrom = $envValues["MAIL_USERNAME"]
}

$mailFromName = $envValues["MAIL_FROM_NAME"]
if ([string]::IsNullOrWhiteSpace($mailFromName)) {
    $mailFromName = "Sistema RRHH"
}

$frontendUrl = $envValues["FRONTEND_URL"]
if ([string]::IsNullOrWhiteSpace($frontendUrl)) {
    $frontendUrl = "https://ronald-f18.github.io"
}

$sanctumDomains = $envValues["SANCTUM_STATEFUL_DOMAINS"]
if ([string]::IsNullOrWhiteSpace($sanctumDomains)) {
    $sanctumDomains = "localhost:5173,127.0.0.1:5173,ronald-f18.github.io"
}

Write-Host "4/5 Actualizando imagen y variables de entorno..."
az containerapp update `
    --name $ContainerApp `
    --resource-group $ResourceGroup `
    --image $Image `
    --set-env-vars `
        "APP_NAME=$($envValues['APP_NAME'])" `
        APP_ENV=production `
        APP_DEBUG=false `
        APP_KEY=secretref:app-key `
        APP_URL=$AppUrl `
        "APP_TIMEZONE=$($envValues['APP_TIMEZONE'])" `
        "APP_LOCALE=$($envValues['APP_LOCALE'])" `
        "APP_FALLBACK_LOCALE=$($envValues['APP_FALLBACK_LOCALE'])" `
        "APP_FAKER_LOCALE=$($envValues['APP_FAKER_LOCALE'])" `
        "DB_CONNECTION=$($envValues['DB_CONNECTION'])" `
        "DB_HOST=$($envValues['DB_HOST'])" `
        "DB_PORT=$($envValues['DB_PORT'])" `
        "DB_DATABASE=$($envValues['DB_DATABASE'])" `
        "DB_USERNAME=$($envValues['DB_USERNAME'])" `
        DB_PASSWORD=secretref:db-password `
        MYSQL_ATTR_SSL_CA=/var/www/html/ca.pem `
        JWT_SECRET=secretref:jwt-secret `
        "MAIL_MAILER=$($envValues['MAIL_MAILER'])" `
        "MAIL_HOST=$($envValues['MAIL_HOST'])" `
        "MAIL_PORT=$($envValues['MAIL_PORT'])" `
        "MAIL_USERNAME=$($envValues['MAIL_USERNAME'])" `
        MAIL_PASSWORD=secretref:mail-password `
        "MAIL_ENCRYPTION=$($envValues['MAIL_ENCRYPTION'])" `
        "MAIL_FROM_ADDRESS=$mailFrom" `
        "MAIL_FROM_NAME=$mailFromName" `
        "SESSION_DRIVER=$($envValues['SESSION_DRIVER'])" `
        "SESSION_LIFETIME=$($envValues['SESSION_LIFETIME'])" `
        "CACHE_STORE=$($envValues['CACHE_STORE'])" `
        "QUEUE_CONNECTION=$($envValues['QUEUE_CONNECTION'])" `
        "LOG_CHANNEL=$($envValues['LOG_CHANNEL'])" `
        "LOG_LEVEL=$($envValues['LOG_LEVEL'])" `
        "FRONTEND_URL=$frontendUrl" `
        "SANCTUM_STATEFUL_DOMAINS=$sanctumDomains"
Assert-CommandSuccess "az containerapp update"

Write-Host "5/5 Estado del despliegue..."
az containerapp revision list `
    --name $ContainerApp `
    --resource-group $ResourceGroup `
    --output table

Write-Host ""
Write-Host "URL publica: $AppUrl"
Write-Host "Imagen desplegada: $Image"

if ($FollowLogs) {
    az containerapp logs show `
        --name $ContainerApp `
        --resource-group $ResourceGroup `
        --follow
}
