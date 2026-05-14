param(
    [switch]$Follow
)

$ErrorActionPreference = "Stop"

$ResourceGroup = "api"
$ContainerApp = "mi-api"
$SubscriptionName = "Azure for Students"

az account set --subscription $SubscriptionName | Out-Null

if ($Follow) {
    az containerapp logs show `
        --name $ContainerApp `
        --resource-group $ResourceGroup `
        --follow
    return
}

az containerapp revision list `
    --name $ContainerApp `
    --resource-group $ResourceGroup `
    --output table

$revisions = az containerapp revision list `
    --name $ContainerApp `
    --resource-group $ResourceGroup `
    -o json | ConvertFrom-Json

$latestRevision = ($revisions | Where-Object { $_.properties.active -eq $true } | Select-Object -First 1).name

if ([string]::IsNullOrWhiteSpace($latestRevision)) {
    throw "No hay revision activa para consultar logs."
}

az containerapp logs show `
    --name $ContainerApp `
    --resource-group $ResourceGroup `
    --revision $latestRevision
