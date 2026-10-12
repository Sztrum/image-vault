# Image Vault

Aplikacja do przechowywania obrazów: REST API w Laravelu (DDD) i SPA w React (Feature-Sliced Design).

## Uruchomienie

### Docker

```bash
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env
docker compose up --build
docker compose exec backend php artisan key:generate
```

- API: http://localhost:8000/api/v1
- Frontend: http://localhost:5173

### Lokalnie

TODO (E10)

## Architektura

### Backend

Laravel podzielony na moduły w stylu DDD:

- `app/Core`, `app/Shared`: wspólny szkielet z moich innych projektów (CommandBus, QueryBus, bazowy model, obsługa błędów, bazowe klasy HTTP),
- `app/Modules/MediaLibrary/Image`: obrazy, czyli rdzeń aplikacji,
- `app/Modules/Weather`: adapter do Open-Meteo.

Każdy moduł ma warstwy `UI → Application → Domain ← Infrastructure`. Domena zawiera reguły biznesowe i interfejsy, a Infrastructure ich implementacje (baza, pliki, zewnętrzne API). Zapisy idą przez `CommandBus`, odczyty przez `QueryBus`. Moduły komunikują się ze sobą tylko przez interfejsy.

### Frontend

TODO (E10)

## Endpointy

TODO (E10)

## Decyzje i kompromisy

TODO (E10)

## Weryfikacja

```bash
cd backend && composer ci      # larastan + pint + testy
cd frontend && npm run check   # eslint + prettier + tsc + vitest + build
```
