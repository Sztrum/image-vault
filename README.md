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

**Szkielet (`app/Core`, `app/Shared`)**: wspólne fundamenty, które przeniosłem z moich innych projektów, żeby trzymać jeden standard:
- `CommandBus` z transakcjami dla zapisów i dodany `QueryBus` dla odczytów (CQRS-lite),
- bazowy model z UUID v7, `ExceptionHandler` zwracający każdy błąd jako JSON, bazowe FormRequesty i zasoby API.

Usunąłem to, czego to API nie potrzebuje (Sanctum, Telescope, Vite, widoki, e-maile). Wersja API jest tylko w URL (`/api/v1`), nie w namespace. Kilku elementów (np. `dispatchManyWithTransaction()`) tu nie używam, ale je zostawiłem, żeby szkielet był taki sam jak w innych projektach.

**Moduł `MediaLibrary/Image`**: rdzeń aplikacji, podzielony na warstwy `UI → Application → Domain ← Infrastructure`.
- Reguły biznesowe są w Value Objects. Obiekt z błędnymi danymi nie może powstać, a błąd kończy się odpowiedzią 422 z komunikatem po polsku. Walidacja w FormRequest służy tylko wygodzie użytkownika, bo domena broni się sama.
- Domena zna tylko interfejsy (`Domain/Contracts`): repozytorium, zapis plików, miniatury i odczyt EXIF/IPTC. Implementacje są w `Infrastructure`, więc można je podmienić, np. w testach.

| Reguła | Gdzie |
|---|---|
| obraz co najmniej 500×500 px | `ImageDimensions` |
| plik od 1 B do 5 MB | `FileSize` |
| formaty JPG, PNG, WebP, TIFF, BMP | `ImageFormatEnum` |
| imię uploadera 1–100 znaków i poprawny e-mail | `Uploader` |

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
