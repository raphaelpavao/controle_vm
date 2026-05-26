# Controle de VMs

Sistema web em PHP MVC para administrar empresas, servidores fisicos e maquinas virtuais.

## Recursos

- Login por usuario e senha.
- CRUD de usuarios restrito a administradores.
- CRUD de empresas.
- CRUD de servidores fisicos vinculados a uma empresa proprietaria.
- CRUD de VMs vinculadas a servidores fisicos e a uma empresa de uso.
- Cadastro de IP, vCPUs, RAM, disco e status da VM.
- Dashboard com totais e consumo agregado por servidor.
- Persistencia configuravel: JSON local ou Firestore em producao.

## Como executar localmente

Requisitos:

- PHP 8.1 ou superior.
- O Firestore em producao usa a API REST e a service account do Cloud Run.

Por padrao, o sistema usa JSON local:

```text
APP_STORAGE=json
```

No diretorio do projeto:

```bash
php -S localhost:8000 -t public
```

Acesse:

```text
http://localhost:8000
```

Usuario inicial:

```text
Configure ADMIN_DEFAULT_EMAIL e ADMIN_DEFAULT_PASSWORD antes do primeiro acesso.
```

Se essas variaveis nao forem configuradas e ainda nao existir usuario salvo, o sistema cria um administrador com e-mail `admin@local` e senha aleatoria. Nesse caso, defina as variaveis e recrie o armazenamento local ou crie o usuario diretamente no ambiente escolhido.

## Persistencia

### Desenvolvimento local com JSON

Sem configurar nada, o sistema le e grava em:

```text
data/controle_vm.json
```

Esse modo e recomendado para testes locais e poucos dados na sua maquina.

### Producao com Firestore

Em producao, configure variaveis de ambiente:

```text
APP_STORAGE=firestore
GOOGLE_CLOUD_PROJECT=seu-projeto-google
FIRESTORE_DATABASE=(default)
ADMIN_DEFAULT_EMAIL=seu-email
ADMIN_DEFAULT_PASSWORD=uma-senha-forte
```

No Google Cloud Run, prefira autenticar pelo service account do servico, sem salvar chave JSON no projeto.

### Migracao JSON para Firestore

Quando o Firestore estiver configurado, e possivel migrar os dados locais:

```bash
php scripts/migrate-json-to-firestore.php
```

Esse script le `data/controle_vm.json` e grava os registros nas colecoes do Firestore.

## Estrutura

```text
app/
  controllers/
  core/
  models/
  storage/
  views/
config/
public/
  assets/
data/
docker/
docs/
scripts/
```

## Docker e Cloud Run

O projeto ja inclui `Dockerfile` e arquivos de Apache para Cloud Run.

Documentacao complementar:

- `docs/deploy-cloud-run.md`
- `docs/github-actions-cloud-run.md`
