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

Warstwy `app/Core` i `app/Shared` to mój szkielet, którego używam w innych projektach (m.in. [reportseller-api](https://github.com/Sztrum/reportseller-api)). Przeniosłem go bez segmentu `V1` w namespace (wersja API jest tylko w URL: `/api/v1`) i usunąłem z niego to, czego API nie potrzebuje (Sanctum, Telescope, Vite, widoki, e-maile). Dodałem `QueryBus` jako odpowiednik `CommandBus` dla odczytów. Część elementów szkieletu (np. obsługa relacji w zasobach API czy `dispatchManyWithTransaction()`) nie jest w tym zadaniu wykorzystywana. Zostawiłem je świadomie, żeby zachować jeden standard między projektami.

TODO (E10)

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
