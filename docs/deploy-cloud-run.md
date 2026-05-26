# Deploy no Cloud Run

Este projeto esta preparado para:

- JSON local em desenvolvimento.
- Firestore em producao.
- Deploy em container no Cloud Run.

## 1. Preparar Google Cloud

Crie ou selecione um projeto no Google Cloud e ative:

- Cloud Run
- Artifact Registry
- Firestore

Crie o banco Firestore em modo Native usando o database padrao `(default)`.

## 2. Service account

Use uma service account para o Cloud Run com permissao de acesso ao Firestore.

Papel sugerido para comecar:

```text
Cloud Datastore User
```

Depois, em ambiente mais rigoroso, refine as permissoes.

## 3. Variaveis de ambiente

No servico Cloud Run:

```text
APP_STORAGE=firestore
GOOGLE_CLOUD_PROJECT=seu-projeto-google
FIRESTORE_DATABASE=(default)
```

Nao coloque chave JSON de service account no repositorio.

## 4. Build local opcional

```bash
docker build -t controle-vm .
docker run --rm -p 8080:8080 -e APP_STORAGE=json controle-vm
```

Acesse:

```text
http://localhost:8080
```

## 5. Deploy manual inicial

Depois de configurar `gcloud`:

```bash
gcloud builds submit --tag REGION-docker.pkg.dev/PROJECT_ID/REPOSITORY/controle-vm
gcloud run deploy controle-vm \
  --image REGION-docker.pkg.dev/PROJECT_ID/REPOSITORY/controle-vm \
  --platform managed \
  --region REGION \
  --allow-unauthenticated \
  --set-env-vars APP_STORAGE=firestore,GOOGLE_CLOUD_PROJECT=PROJECT_ID,FIRESTORE_DATABASE='(default)'
```

Troque `REGION`, `PROJECT_ID` e `REPOSITORY`.

## 6. Migrar JSON para Firestore

Com Firestore configurado e credenciais disponiveis:

```bash
APP_STORAGE=firestore php scripts/migrate-json-to-firestore.php
```

O script le `data/controle_vm.json` e cria/atualiza os documentos no Firestore.
