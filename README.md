# Image Vault

Aplikacja do przechowywania obrazów: REST API w Laravelu (DDD) i SPA w React (Feature-Sliced Design).

## Uruchomienie

### Docker

```bash
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env
docker compose up --build
docker compose exec backend php artisan key:generate
docker compose exec backend php artisan db:seed   # opcjonalnie: 25 przykładowych obrazów
```

- API: http://localhost:8000/api/v1
- Frontend: http://localhost:5173

### Lokalnie

TODO (E10)

## Architektura

### Backend

Laravel podzielony na moduły w stylu DDD:

- `app/Core`, `app/Shared`: wspólny szkielet z moich innych projektów (CommandBus, bazowy model i repozytorium, obsługa błędów, bazowe klasy HTTP),
- `app/Modules/MediaLibrary/Image`: obrazy, czyli rdzeń aplikacji (upload, miniatury WebP, EXIF/IPTC, lista, pobieranie, usuwanie),
- `app/Modules/Weather`: temperatura w Katowicach z Open-Meteo.

Każdy moduł ma warstwy `UI → Application → Domain ← Infrastructure`. Domena trzyma reguły biznesowe (Value Objects, DTO, wyjątki), a Infrastructure robi I/O: baza, dysk, Imagick, zewnętrzne API. Zapisy idą przez `CommandBus` w transakcji, odczyty przez klasy `*Query` wstrzykiwane do kontrolera. Moduł Image pobiera pogodę przez `WeatherProviderInterface`, więc nie zależy od konkretnego dostawcy.

### Frontend

TODO (E10)

## Endpointy

Odpowiedzi są w JSON-ie (poza miniaturą i pobieraniem pliku); błędy mają postać `{status, message, errors?}`.

| Metoda | Ścieżka | Wejście | Odpowiedź |
|---|---|---|---|
| `GET` | `/api/v1/images` | `?cursor=&per_page=` (domyślnie 10, max 50) | 200: `data[]` + `meta.next_cursor` |
| `POST` | `/api/v1/images` | multipart: `file`, `uploader_name`, `uploader_email` | 201 / 422 |
| `GET` | `/api/v1/images/{id}/thumbnail` | — | 200: `image/webp` |
| `GET` | `/api/v1/images/{id}/download` | — | 200: oryginał z oryginalną nazwą |
| `DELETE` | `/api/v1/images/{id}` | — | 204 / 404 |

Upload przyjmuje JPG, PNG, WebP, TIFF i BMP do 5 MB, minimum 500×500 px.

## Decyzje i kompromisy

TODO (E10)

## Weryfikacja

```bash
cd backend && composer ci      # larastan + pint + testy
cd frontend && npm run check   # eslint + prettier + tsc + vitest + build
```
