# Magazine Shop

Online magazine shop: a Laravel 11 REST API with JWT authentication (`backend/`) and a Vue 3 SPA (`web/`), each in its own folder with its own Docker image.

## Quick start (Docker)

```bash
cp .env.example .env     # set APP_KEY and JWT_SECRET (commands are in the file)
docker compose up --build
```

- Shop: http://localhost:8080
- API: http://localhost:8000/api (also proxied by the shop at `/api`)
- Swagger UI: http://localhost:8000/api/documentation

The SQLite database lives in the `backend_data` volume. On start the backend runs migrations, optionally seeds demo magazines (`SEED_DATABASE=true`) and creates the administrator from `ADMIN_EMAIL` / `ADMIN_PASSWORD`.

## Development with hot reload

```bash
docker compose -f docker-compose.yml -f docker-compose.dev.yml up --build
```

The Vue dev server runs on http://localhost:8081 and proxies `/api` to the backend.

Without Docker:

```bash
cd backend/backend && composer install && cp .env.example .env
php artisan key:generate && php artisan jwt:secret
touch database/database.sqlite && php artisan migrate --seed
php artisan shop:create-admin admin@example.com --password=ChangeMe123!
php artisan serve                      # http://localhost:8000

cd web/web && npm install
VUE_APP_PROXY_TARGET=http://localhost:8000 npm run serve
```

## Tests

```bash
cd backend/backend && php artisan test          # PHPUnit feature tests (SQLite in memory)
cd web/web && npm run lint                      # ESLint
cd web/web && npm run test:e2e                  # Cypress, needs the stack running on :8080
```

## Project structure

```
backend/
  backend/        Laravel app: app/{Console,Exceptions,Http,Jobs,Mail,Models,Providers,QueryBuilders,Services,States}
  config/         nginx, php and supervisor configs used by the Docker image
web/
  web/            Vue CLI app: src/{assets,components,config,helpers,layouts,pages,plugins,router,store,utils}
docker-compose.yml, docker-compose.dev.yml
```

## API overview

Money values are integer cents. Protected routes need `Authorization: Bearer <token>`; tokens last 60 minutes and can be exchanged at `POST /api/auth/refresh`. Full reference: Swagger UI.

| Area | Endpoints |
|---|---|
| Auth | `POST auth/register`, `auth/login`, `auth/refresh`, `auth/logout`; `GET/PUT auth/me`; `PUT auth/password` |
| Catalog | `GET categories`, `GET magazines` (q, category, format, sort, page), `GET magazines/{id or slug}` |
| Cart | `GET/POST/DELETE cart`, `PUT/DELETE cart/{magazine}` |
| Orders | `GET/POST orders`, `GET orders/{id}`, `POST orders/{id}/pay`, `POST orders/{id}/cancel` |
| Admin | `GET admin/stats`, magazines and categories CRUD, `GET admin/orders`, `PATCH admin/orders/{id}/status` |

Order states: pending → processing → shipped → delivered; pending and processing can be cancelled (stock is returned).

## Default credentials

Administrator: `ADMIN_EMAIL` / `ADMIN_PASSWORD` from `.env` (`admin@example.com` / `ChangeMe123!` in the example). Change them before deploying.
