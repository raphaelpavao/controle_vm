# GitHub Actions para Cloud Run

Este arquivo descreve a automacao recomendada. Nao configure os secrets ate o projeto Google Cloud existir.

## Secrets sugeridos

No GitHub, configure:

```text
GCP_PROJECT_ID
GCP_REGION
GCP_ARTIFACT_REPOSITORY
GCP_SERVICE
GCP_WORKLOAD_IDENTITY_PROVIDER
GCP_SERVICE_ACCOUNT
```

Prefira Workload Identity Federation em vez de chave JSON.

## Workflow sugerido

Crie `.github/workflows/deploy.yml` quando o repositorio e o projeto Google estiverem prontos.

Fluxo:

1. Checkout do codigo.
2. Autenticacao no Google Cloud.
3. Build e push da imagem para Artifact Registry.
4. Deploy no Cloud Run com variaveis de ambiente.

As variaveis de producao esperadas pelo app:

```text
APP_STORAGE=firestore
GOOGLE_CLOUD_PROJECT=${{ secrets.GCP_PROJECT_ID }}
FIRESTORE_DATABASE=(default)
```
