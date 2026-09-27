# Magazine Shop

Online magazine shop: a Laravel 11 REST API with JWT authentication (`backend/`) and a Vue 3 SPA (`web/`), each in its own folder with its own Docker image.

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
