$ErrorActionPreference = "Stop"

$ResourceGroup = "api"
$ContainerApp = "mi-api"
$SubscriptionName = "Azure for Students"

az account set --subscription $SubscriptionName | Out-Null

Write-Host "Estado:"
az containerapp show `
    --name $ContainerApp `
    --resource-group $ResourceGroup `
    --query "{runningStatus:properties.runningStatus,fqdn:properties.configuration.ingress.fqdn,image:properties.template.containers[0].image}" `
    --output json

Write-Host ""
Write-Host "Revisiones:"
az containerapp revision list `
    --name $ContainerApp `
    --resource-group $ResourceGroup `
    --output table
