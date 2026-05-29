# Atualizar producao

Este passo a passo deve ser usado sempre que houver alteracao no codigo e voce quiser publicar uma nova versao no Cloud Run.

Use placeholders neste arquivo publico:

```text
PROJECT_ID
REGION
ARTIFACT_REPOSITORY
IMAGE_NAME
SERVICE_NAME
SERVICE_ACCOUNT_EMAIL
ADMIN_EMAIL
SECRET_NAME
```

Se quiser manter uma copia com valores reais, crie `docs/update-production.local.md`. Arquivos `*.local.md` sao ignorados pelo Git.

## 1. Na sua maquina local

Entre na pasta do projeto:

```powershell
cd CAMINHO_DO_PROJETO
```

Confira os arquivos alterados:

```powershell
git status --short
```

Adicione as alteracoes:

```powershell
git add .
```

Crie o commit:

```powershell
git commit -m "Descreva a alteracao"
```

Envie para o GitHub:

```powershell
git push
```

## 2. No Google Cloud Shell

Abra o Cloud Shell no Google Cloud Console.

Defina o projeto:

```bash
gcloud config set project PROJECT_ID
```

Entre na pasta do repositorio:

```bash
cd ~/controle_vm
```

Atualize o codigo:

```bash
git pull
```

## 3. Criar a imagem no Artifact Registry

Rode o build e deploy via `cloudbuild.yaml`:

```bash
gcloud builds submit --config cloudbuild.yaml
```

Para informar uma tag manual:

```bash
gcloud builds submit --config cloudbuild.yaml --substitutions _TAG=manual
```

Aguarde terminar com:

```text
STATUS: SUCCESS
```

## 4. Fazer deploy no Cloud Run manualmente

O `cloudbuild.yaml` ja faz o deploy. Use o comando abaixo apenas se precisar fazer deploy manual de uma imagem ja criada:

```bash
gcloud run deploy SERVICE_NAME \
  --project PROJECT_ID \
  --image REGION-docker.pkg.dev/PROJECT_ID/ARTIFACT_REPOSITORY/IMAGE_NAME \
  --region REGION \
  --platform managed \
  --allow-unauthenticated \
  --service-account SERVICE_ACCOUNT_EMAIL \
  --set-env-vars APP_STORAGE=firestore,GOOGLE_CLOUD_PROJECT=PROJECT_ID,FIRESTORE_DATABASE='(default)',ADMIN_DEFAULT_EMAIL=ADMIN_EMAIL \
  --set-secrets ADMIN_DEFAULT_PASSWORD=SECRET_NAME:latest
```

## 5. Validar

Ao final, o Cloud Run exibira a URL do servico.

Abra a URL no navegador e teste:

```text
login
cadastro
edicao
listagem
```

## Observacoes

- Nao suba arquivos `.env`.
- Nao suba arquivos em `data/*.json`.
- A senha inicial deve ficar no Secret Manager.
- Se o Cloud Shell perder a sessao, repita `gcloud config set project PROJECT_ID`.
