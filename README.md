# Desafio Onfly

Este projeto é uma aplicação Full Stack para gerenciamento de pedidos de viagem corporativa, composta por uma API REST em Laravel e um front-end em Vue.js.

### Login
<img width="100%" alt="image" src="https://github.com/user-attachments/assets/34f58e02-e08d-44af-b8d7-640a6c327006" />

### Dashboard
<img width="100%" alt="image" src="https://github.com/user-attachments/assets/9441a7c9-ee2b-4bf1-8be0-9a69387eb3cf" />

### Solicitação
<img width="100%" alt="image" src="https://github.com/user-attachments/assets/16c7ac60-b629-46a1-8f56-7d04a2417cb4" />

## Pré-requisitos

- PHP 8.2+
- Composer
- Node.js & NPM
- SQLite (ou outro banco de dados configurado)
- Docker & Docker Compose (Opcional, para rodar via container)

## Como rodar com Docker (Recomendado)

A maneira mais simples de rodar a aplicação é utilizando o Docker Compose, que subirá tanto o back-end quanto o front-end automaticamente.

1.  Certifique-se de ter o Docker e o Docker Compose instalados.
2.  Na raiz do projeto, execute:
    ```bash
    docker-compose up --build
    ```
3.  Acesse:
    - **Front-end**: http://localhost:5173
    - **Back-end**: http://localhost:8000

_Nota: O banco de dados SQLite será persistido na pasta `back/database`._

## Configuração Manual e Execução

### Back-end (Laravel)

1.  Acesse a pasta `back`:
    ```bash
    cd back
    ```
2.  Instale as dependências do PHP:
    ```bash
    composer install
    ```
3.  Configure o arquivo `.env`:
    - O arquivo `.env` já deve estar configurado para usar SQLite com o banco de dados em `database/database.sqlite`.
    - Caso contrário, copie o exemplo: `cp .env.example .env` e configure `DB_CONNECTION=sqlite`.
4.  Gere a chave da aplicação:
    ```bash
    php artisan key:generate
    ```
5.  Execute as migrações do banco de dados (isso criará as tabelas):
    ```bash
    php artisan migrate
    ```
6.  Inicie o servidor de desenvolvimento:
    ```bash
    php artisan serve
    ```
    O servidor rodará em `http://localhost:8000`.

### Front-end (Vue.js)

1.  Acesse a pasta `front`:
    ```bash
    cd front
    ```
2.  Instale as dependências do Node.js:
    ```bash
    npm install
    ```
3.  Inicie o servidor de desenvolvimento:
    ```bash
    npm run dev
    ```
    O front-end estará acessível (geralmente em `http://localhost:5173`).

## Testes

Para executar os testes unitários e de funcionalidade do back-end:

1.  Na pasta `back`, execute:
    ```bash
    php artisan test
    ```

## Funcionalidades

- **Autenticação**: Login com JWT (Laravel Sanctum).
- **Dashboard**: Visualização de pedidos de viagem com filtro por status.
- **Criação de Pedidos**: Formulário para novos pedidos.
- **Administração**: Usuários administradores podem aprovar ou cancelar pedidos.
- **Notificações**: O sistema registra notificações de alteração de status (via banco de dados).

## Usuários Padrão (Seed)

O `DatabaseSeeder` cria dois usuários automaticamente para teste:

1.  **Administrador**:
    - Email: `admin@example.com`
    - Senha: `password`
2.  **Usuário Comum**:
    - Email: `user@example.com`
    - Senha: `password`

Para rodar o seed:

```bash
php artisan db:seed
```

## Observações

- O banco de dados SQLite é criado automaticamente em `back/database/database.sqlite` se não existir.
- Para criar um usuário administrador, você pode usar o Tinker (`php artisan tinker`) ou modificar o `DatabaseSeeder`.
  - Exemplo Tinker:
    ```php
    User::factory()->create(['email' => 'admin@example.com', 'is_admin' => true, 'password' => 'password']);
    User::factory()->create(['email' => 'user@example.com', 'is_admin' => false, 'password' => 'password']);
    ```
