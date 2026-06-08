# Oviajante — API

API Laravel para cotação de seguro viagem.

## Pré-requisitos

- **PHP 8.4+** com extensões: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `zip`
- **Composer 2**
- **MySQL 8** rodando localmente

## Configuração

1. Instale as dependências:

```bash
composer install
```

2. Crie o arquivo de ambiente:

```bash
cp .env.example .env
```

Windows (PowerShell): `Copy-Item .env.example .env`

3. Ajuste o `.env` com as credenciais do seu MySQL:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=oviajante
DB_USERNAME=root
DB_PASSWORD=sua_senha
```

4. Crie o banco (se ainda não existir):

```sql
CREATE DATABASE oviajante CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

5. Gere a chave da aplicação e rode as migrations:

```bash
php artisan key:generate
php artisan migrate
```

## Rodar localmente

```bash
php artisan serve
```

A API ficará em **http://localhost:8000**.

### Endpoints

| Método | URL | Descrição |
|--------|-----|-----------|
| `POST` | `/api/quote` | Calcular e salvar cotação |
| `GET` | `/api/quotes` | Histórico de consultas |

## Testes

```bash
php artisan test
```

Ou:

```bash
composer test
```

## Frontend

O frontend consome esta API em `http://localhost:8000/api`. Com o Next.js em `http://localhost:3000`, o CORS já está configurado em `config/cors.php`.

No repositório do front, use:

```env
NEXT_PUBLIC_API_URL=http://localhost:8000/api
```

### Decisões e premissas
Para mim, foi um desafio. Demorei um pouco para entender de forma clara como funciona a contagem da viagem para o viajante.
Criei e utilizei o QuoteCalculatorService como uma forma de centralizar o cálculo das cotações dos viajantes. Dessa forma, essa lógica poderá ser reutilizada em outros contextos no futuro, como comparações entre cotações e geração de gráficos para o administrador, permitindo análises como período da viagem, cotações mais altas quando o viajante deixa a plataforma, aceitação de cotações mais baixas, entre outras.
Também criei alguns enums para facilitar o entendimento das regras de negócio, como "Tarifa Diária por Zona de Destino", "Desconto por Grupo" e "Faixa Etária". Acredito que essa abordagem ajudará futuros desenvolvedores a compreenderem o sistema mais rapidamente, reduzindo o tempo necessário para entendimento das regras.