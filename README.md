# Bookstand API

API REST para gerenciamento de livros e avaliações, construída com Laravel 13, Laravel Sanctum e Laravel Sail.

REST API for managing books and reviews, built with Laravel 13, Laravel Sanctum, and Laravel Sail.

## Sumário / Table of Contents

- [Português](#português)
- [English](#english)

## Português

### Visão Geral

A Bookstand API fornece autenticação de usuários, consulta e cadastro de livros, além de criação e listagem de avaliações. A autenticação é feita com tokens via Laravel Sanctum.

Regras atuais de acesso:

- Usuários autenticados podem consultar livros.
- Apenas administradores podem cadastrar livros.
- Usuários autenticados podem listar e criar reviews.
- No cadastro, novos usuários recebem automaticamente o papel `usuario`.

### Stack

- PHP 8.3+
- Laravel 13
- Laravel Sanctum
- Laravel Sail
- PostgreSQL via Docker

### Como Executar com Sail

#### 1. Instalar dependências

Se este for o primeiro setup local:

```bash
composer install
cp .env.example .env
```

#### 2. Configurar o ambiente

Como o projeto usa Sail com PostgreSQL em [`compose.yaml`](/home/paulo/personal/BookstandAPI/compose.yaml), ajuste o `.env` para usar `pgsql`:

```env
APP_NAME=BookstandAPI
APP_URL=http://localhost

DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=bookstand
DB_USERNAME=sail
DB_PASSWORD=password
```

Se preferir, mantenha as demais variáveis padrão do Laravel.

#### 3. Subir os containers

```bash
./vendor/bin/sail up -d
```

#### 4. Gerar a chave da aplicação

```bash
./vendor/bin/sail artisan key:generate
```

#### 5. Rodar as migrations

```bash
./vendor/bin/sail artisan migrate
```

#### 6. Acessar a API

Por padrão:

- API: `http://localhost`

### Comandos Úteis com Sail

```bash
./vendor/bin/sail artisan migrate:fresh
./vendor/bin/sail artisan test
./vendor/bin/sail composer test
./vendor/bin/sail php artisan route:list
```

### Autenticação

A API usa Laravel Sanctum com token Bearer.

Depois do login ou registro, a resposta inclui:

- `token`: token de acesso
- `user`: usuário autenticado com a relação `role`

Envie o token no header:

```http
Authorization: Bearer SEU_TOKEN
Accept: application/json
```

### Papéis e Autorização

O projeto possui a tabela `roles` e relacionamento `users.role_id`.

Papéis relevantes:

- `usuario`: atribuído automaticamente no registro
- `admin`: necessário para `POST /api/book`

Se precisar transformar um usuário em administrador, atualize o `role_id` dele para o papel `admin` no banco.

### Endpoints

Base URL local:

```text
http://localhost/api
```

#### Auth

##### `POST /auth/register`

Cria um novo usuário com papel padrão `usuario`.

Payload:

```json
{
  "name": "Paulo Silva",
  "email": "paulo@example.com",
  "password": "password123"
}
```

Resposta:

```json
{
  "success": true,
  "message": "Usuario cadastrado com sucesso.",
  "token": "1|token...",
  "user": {
    "id": 1,
    "name": "Paulo Silva",
    "email": "paulo@example.com",
    "role_id": 1,
    "role": {
      "id": 1,
      "name": "usuario"
    }
  }
}
```

##### `POST /auth/login`

Autentica um usuário existente.

Payload:

```json
{
  "email": "paulo@example.com",
  "password": "password123"
}
```

##### `GET /auth/me`

Retorna o usuário autenticado.

Requer autenticação.

#### Livros

##### `GET /book`

Lista livros paginados.

Requer autenticação.

##### `POST /book`

Cadastra um novo livro.

Requer autenticação e papel `admin`.

Payload:

```json
{
  "title": "Clean Code",
  "author": "Robert C. Martin",
  "synopsis": "A book about software craftsmanship and code quality.",
  "release_date": 2008
}
```

Campos:

- `title`: string obrigatória
- `author`: string obrigatória
- `synopsis`: string obrigatória
- `release_date`: inteiro obrigatório

#### Reviews

##### `GET /review`

Lista reviews paginadas.

Requer autenticação.

##### `POST /review`

Cria uma nova review.

Requer autenticação.

Payload:

```json
{
  "note": 9,
  "considerations": "Leitura muito boa.",
  "book_id": 1,
  "user_id": 1
}
```

Campos:

- `note`: inteiro obrigatório entre 0 e 10
- `considerations`: string opcional
- `book_id`: ID existente em `books`
- `user_id`: ID existente em `users`

### Exemplo de Uso com cURL

#### Registrar usuário

```bash
curl -X POST http://localhost/api/auth/register \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Paulo Silva",
    "email": "paulo@example.com",
    "password": "password123"
  }'
```

#### Login

```bash
curl -X POST http://localhost/api/auth/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "paulo@example.com",
    "password": "password123"
  }'
```

#### Listar livros

```bash
curl http://localhost/api/book \
  -H "Accept: application/json" \
  -H "Authorization: Bearer SEU_TOKEN"
```

#### Cadastrar livro como admin

```bash
curl -X POST http://localhost/api/book \
  -H "Accept: application/json" \
  -H "Authorization: Bearer SEU_TOKEN_ADMIN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Domain-Driven Design",
    "author": "Eric Evans",
    "synopsis": "Strategic and tactical design for complex software.",
    "release_date": 2003
  }'
```

#### Criar review

```bash
curl -X POST http://localhost/api/review \
  -H "Accept: application/json" \
  -H "Authorization: Bearer SEU_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "note": 10,
    "considerations": "Excelente livro.",
    "book_id": 1,
    "user_id": 1
  }'
```

### Estrutura Funcional

- [`routes/api.php`](/home/paulo/personal/BookstandAPI/routes/api.php): definição das rotas da API
- [`app/Http/Controllers/Api/AuthController.php`](/home/paulo/personal/BookstandAPI/app/Http/Controllers/Api/AuthController.php): registro, login e usuário autenticado
- [`app/Http/Controllers/Api/BooksController.php`](/home/paulo/personal/BookstandAPI/app/Http/Controllers/Api/BooksController.php): listagem e criação de livros
- [`app/Http/Controllers/Api/ReviewController.php`](/home/paulo/personal/BookstandAPI/app/Http/Controllers/Api/ReviewController.php): listagem e criação de reviews
- [`app/Http/Middleware/AdminMiddleware.php`](/home/paulo/personal/BookstandAPI/app/Http/Middleware/AdminMiddleware.php): restrição de criação de livros para admins

### Observações

- As rotas estão protegidas com `auth:sanctum`.
- O endpoint `POST /book` também usa o middleware `admin`.
- O projeto atualmente expõe operações de listagem e criação. Endpoints de atualização e remoção ainda não estão disponíveis.
- O campo `user_id` da review é enviado no payload. Em uma evolução futura, o ideal é vincular a review ao usuário autenticado no backend.

## English

### Overview

Bookstand API is a REST API for user authentication, book management, and review creation. Authentication is handled through Laravel Sanctum bearer tokens.

Current access rules:

- Authenticated users can list books.
- Only administrators can create books.
- Authenticated users can list and create reviews.
- New users are automatically assigned the `usuario` role during registration.

### Stack

- PHP 8.3+
- Laravel 13
- Laravel Sanctum
- Laravel Sail
- PostgreSQL via Docker

### Running with Sail

#### 1. Install dependencies

```bash
composer install
cp .env.example .env
```

#### 2. Configure the environment

Since the project uses Sail with PostgreSQL in [`compose.yaml`](/home/paulo/personal/BookstandAPI/compose.yaml), update your `.env` accordingly:

```env
APP_NAME=BookstandAPI
APP_URL=http://localhost

DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=bookstand
DB_USERNAME=sail
DB_PASSWORD=password
```

#### 3. Start the containers

```bash
./vendor/bin/sail up -d
```

#### 4. Generate the application key

```bash
./vendor/bin/sail artisan key:generate
```

#### 5. Run migrations

```bash
./vendor/bin/sail artisan migrate
```

#### 6. Access the API

Default local URL:

- API: `http://localhost`

### Useful Sail Commands

```bash
./vendor/bin/sail artisan migrate:fresh
./vendor/bin/sail artisan test
./vendor/bin/sail composer test
./vendor/bin/sail php artisan route:list
```

### Authentication

The API uses Laravel Sanctum bearer tokens.

After login or registration, the response includes:

- `token`: access token
- `user`: authenticated user with the `role` relationship loaded

Send the token in the request headers:

```http
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```

### Roles and Authorization

The project includes a `roles` table and a `users.role_id` relationship.

Relevant roles:

- `usuario`: assigned automatically on registration
- `admin`: required for `POST /api/book`

If you need to promote a user to administrator, update that user's `role_id` to the `admin` role in the database.

### Endpoints

Local base URL:

```text
http://localhost/api
```

#### Auth

##### `POST /auth/register`

Registers a new user with the default `usuario` role.

Request body:

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123"
}
```

##### `POST /auth/login`

Authenticates an existing user.

Request body:

```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

##### `GET /auth/me`

Returns the currently authenticated user.

Requires authentication.

#### Books

##### `GET /book`

Returns a paginated list of books.

Requires authentication.

##### `POST /book`

Creates a new book.

Requires authentication and the `admin` role.

Request body:

```json
{
  "title": "Clean Code",
  "author": "Robert C. Martin",
  "synopsis": "A book about software craftsmanship and code quality.",
  "release_date": 2008
}
```

#### Reviews

##### `GET /review`

Returns a paginated list of reviews.

Requires authentication.

##### `POST /review`

Creates a new review.

Requires authentication.

Request body:

```json
{
  "note": 9,
  "considerations": "Very solid read.",
  "book_id": 1,
  "user_id": 1
}
```

Validation rules:

- `note`: required integer between 0 and 10
- `considerations`: nullable string
- `book_id`: must exist in `books`
- `user_id`: must exist in `users`

### cURL Examples

#### Register

```bash
curl -X POST http://localhost/api/auth/register \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123"
  }'
```

#### Login

```bash
curl -X POST http://localhost/api/auth/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

#### List books

```bash
curl http://localhost/api/book \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### Create a book as admin

```bash
curl -X POST http://localhost/api/book \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Domain-Driven Design",
    "author": "Eric Evans",
    "synopsis": "Strategic and tactical design for complex software.",
    "release_date": 2003
  }'
```

#### Create a review

```bash
curl -X POST http://localhost/api/review \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "note": 10,
    "considerations": "Excellent book.",
    "book_id": 1,
    "user_id": 1
  }'
```

### Project Structure

- [`routes/api.php`](/home/paulo/personal/BookstandAPI/routes/api.php): API route definitions
- [`app/Http/Controllers/Api/AuthController.php`](/home/paulo/personal/BookstandAPI/app/Http/Controllers/Api/AuthController.php): registration, login, authenticated user
- [`app/Http/Controllers/Api/BooksController.php`](/home/paulo/personal/BookstandAPI/app/Http/Controllers/Api/BooksController.php): book listing and creation
- [`app/Http/Controllers/Api/ReviewController.php`](/home/paulo/personal/BookstandAPI/app/Http/Controllers/Api/ReviewController.php): review listing and creation
- [`app/Http/Middleware/AdminMiddleware.php`](/home/paulo/personal/BookstandAPI/app/Http/Middleware/AdminMiddleware.php): admin-only protection for book creation

### Notes

- Routes are protected with `auth:sanctum`.
- `POST /book` is additionally protected by the `admin` middleware.
- The API currently provides listing and creation flows only. Update and delete endpoints are not implemented yet.
- `user_id` is currently sent in the review payload. A future improvement would be deriving it from the authenticated user on the backend.
