# Automacao para Cloud Run

O projeto usa `cloudbuild.yaml` para automatizar build e deploy pelo Cloud Build.

## Fluxo recomendado

```text
push na main
  -> Cloud Build Trigger
  -> docker build
  -> push no Artifact Registry
  -> deploy no Cloud Run
```

## Substituicoes usadas

```text
_REGION
_ARTIFACT_REPOSITORY
_IMAGE_NAME
_TAG
_SERVICE_NAME
_SERVICE_ACCOUNT_NAME
_ADMIN_EMAIL
_SECRET_NAME
```

Essas substituicoes possuem valores padrao no `cloudbuild.yaml`. Se mudar nomes de recursos, ajuste os valores no arquivo ou no trigger.
