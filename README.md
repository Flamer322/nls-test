# NLS test

## Project Description

This is a test assigment for NLS.

OpenAPI specification for API is described in [openapi.yaml](./backend/storage/api-docs/api-docs.yaml) file.

Swagger UI is available at http://localhost:8080/api/documentation or https://localhost:8443/api/documentation.

Application routes are available at http://localhost:8080/api or https://localhost:8443/api.

## Project structure

- **.github/** - directory with configuration files for GitHub actions
- **backend/** - PHP (Laravel) application with REST API
- **docker/** - application code
- **docker-compose.yaml** - compose file describing project environment
- **Makefile** - file helping automate routine actions

## Setup

Run init command with make:

- `make init`

Or execute commands manually:

- `docker compose down -v --remove-orphans`
- `docker compose build`
- `docker compose up -d`
- `docker compose run --rm php-cli cp .env.example .env`
- `docker compose run --rm php-cli composer install`
- `docker compose run --rm php-cli php artisan key:generate`
- `docker compose run --rm php-cli php artisan migrate`
- `docker compose run --rm php-cli php artisan jwt:secret`
