# Image Vault

Aplikacja do przechowywania obrazów: REST API w Laravelu (DDD) i SPA w React (Feature-Sliced Design).

## Uruchomienie

### Docker

```bash
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env
docker compose up --build
docker compose exec backend php artisan db:seed   # opcjonalnie: 25 przykładowych obrazów
```

- API: http://localhost:8000/api/v1
- Frontend: http://localhost:5173

Przy starcie backend sam instaluje zależności, generuje `APP_KEY` (jeśli go brak) i uruchamia migracje. Obraz PHP ma `imagick` (TIFF i WebP), `exif` i limity uploadu powyżej 5 MB. Na Linuksie pliki tworzone w kontenerze (`vendor/`, `storage/`) należą do roota; w razie problemów z uprawnieniami: `docker compose exec backend chown -R 1000:1000 storage bootstrap/cache`. `APP_DEBUG` jest domyślnie wyłączone, więc błędy API nie pokazują stack trace'ów.

### Lokalnie

Wymagania: PHP 8.4 z rozszerzeniami `imagick`, `exif`, `pdo_mysql`; Composer; Node 20.19+; MySQL 8 (np. sam kontener: `docker compose up -d mysql`).

```bash
# backend (http://localhost:8000)
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php -d upload_max_filesize=10M -d post_max_size=12M -S localhost:8000 -t public

# frontend (http://localhost:5173), w drugim terminalu
cd frontend
npm ci
cp .env.example .env
npm run dev
```

Domyślne `upload_max_filesize` w PHP to 2 MB, stąd flagi `-d` przy serwerze: bez nich większe pliki nie dotrą do walidacji.

### Pliki w S3

Obrazy i miniatury trafiają na domyślny dysk Laravela (`FILESYSTEM_DISK`, w `.env.example` jest `local`). Żeby trzymać je w S3:

```bash
composer require league/flysystem-aws-s3-v3
```

i w `backend/.env` ustaw `FILESYSTEM_DISK=s3` oraz `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`. Kod się nie zmienia, bo `LaravelImageStorage` pracuje na Flysystem.

## Architektura

### Backend

Laravel podzielony na moduły w stylu DDD:

- `app/Core`, `app/Shared`: wspólny szkielet z moich innych projektów (CommandBus, bazowy model i repozytorium, obsługa błędów, bazowe klasy HTTP),
- `app/Modules/MediaLibrary/Image`: obrazy, czyli rdzeń aplikacji (upload, miniatury WebP, EXIF/IPTC, lista, pobieranie, usuwanie),
- `app/Modules/Weather`: temperatura w Katowicach z Open-Meteo.

Każdy moduł dzieli się na katalogi `UI`, `Application`, `Domain` i `Infrastructure`. Domena trzyma reguły biznesowe (Value Objects, DTO, wyjątki) i nie zależy od żadnej innej warstwy. Infrastructure robi I/O: baza, dysk, Imagick, zewnętrzne API. Odwrócenia zależności (interfejs w domenie, implementacja w Infrastructure) użyłem tylko przy pogodzie: moduł Image pobiera ją przez `WeatherProviderInterface`, więc nie zależy od konkretnego dostawcy. Poza tym handlery, serwisy aplikacyjne i kontroler używają konkretnych klas z Infrastructure (`ImageRepository`, `LaravelImageStorage`, `PhpExifIptcExtractor`) wstrzykiwanych przez autowiring. To świadomy kompromis, opisany niżej w „Decyzjach”. Zapisy idą przez `CommandBus` (komendy zmieniające kilka rzeczy w bazie w transakcji), odczyty przez klasy `*Query` wstrzykiwane do kontrolera.

Upload krok po kroku: `ImageStoreRequest` (walidacja) → `UploadImageCommand` → handler: `ImageInspectionService` czyta format, rozmiar i wymiary z samego pliku i składa `NewImageDTO` z Value Objects → odczyt EXIF/IPTC i temperatury z Open-Meteo → `ImageFileService` zapisuje oryginał i miniaturę WebP (300 px) → repozytorium zapisuje rekord jednym INSERT-em. Gdy zapis rekordu się nie uda, handler usuwa zapisane pliki. Przy usuwaniu obrazu pliki znikają dopiero po commicie transakcji.

### Frontend

React + TypeScript (strict) w układzie Feature-Sliced Design: `app → pages → widgets → features → entities → shared`, import tylko w dół i tylko przez publiczne `index.ts` slice'a. Kierunek importów pilnuje ESLint (`eslint-plugin-boundaries`).

- `entities/image`: typy zgodne z API, pobieranie listy (`useInfiniteImages`) i szczegółów (`useImageDetails`), karta obrazu,
- `features/upload-image`: wysyłka wielu plików, każdy osobnym `POST` po kolei, ze statusem przy każdym pliku; zły plik nie blokuje pozostałych,
- `features/view-image-details`: okno szczegółów z temperaturą, e-mailem oraz rozwijanym EXIF i IPTC,
- `features/delete-image`, `features/download-image`: usuwanie z potwierdzeniem, pobieranie,
- `widgets/image-list`: galeria z infinite scrollem (`IntersectionObserver`),
- `shared`: klient HTTP (axios), konfiguracja (`VITE_API_URL` walidowany przez zod), komponenty UI.

Stan serwera trzyma TanStack Query, formularz react-hook-form ze schematem zod, a style to klasy Tailwind 4 w JSX.

## Endpointy

Odpowiedzi są w JSON-ie (poza miniaturą i pobieraniem pliku); błędy mają postać `{status, message, errors?}`.

| Metoda | Ścieżka | Wejście | Odpowiedź |
|---|---|---|---|
| `GET` | `/api/v1/images` | `?cursor=&per_page=` (domyślnie 10, max 50) | 200: `data[]` + `meta.next_cursor` |
| `POST` | `/api/v1/images` | multipart: `file`, `uploader_name`, `uploader_email` | 201 / 422 |
| `GET` | `/api/v1/images/{id}` | — | 200: szczegóły z e-mailem, temperaturą, EXIF i IPTC / 404 |
| `GET` | `/api/v1/images/{id}/thumbnail` | — | 200: `image/webp` |
| `GET` | `/api/v1/images/{id}/download` | — | 200: oryginał z oryginalną nazwą |
| `DELETE` | `/api/v1/images/{id}` | — | 204 / 404 |

Upload przyjmuje JPG, PNG, WebP, TIFF i BMP do 5 MB, minimum 500×500 px.

## Decyzje i kompromisy

- **Eloquent w `Domain/Models`.** Czysta encja z mapperem to dużo kodu bez zysku przy tej skali. Reguły biznesowe i tak siedzą w Value Objects, a I/O tylko w Infrastructure.
- **Uploader jako Value Object w kolumnach obrazu**, nie osobna tabela: zadanie wymaga tylko zapisania imienia i e-maila przy uploadzie.
- **Walidacja w dwóch miejscach.** FormRequest daje czytelne komunikaty 422, a Value Objects bronią reguł niezależnie od źródła danych. Limity w requeście biorę ze stałych VO, więc się nie rozjadą. Frontend powtarza te same reguły (bez rozdzielczości) dla szybszej odpowiedzi, ale źródłem prawdy jest API.
- **Interfejsy tylko tam, gdzie mają sens.** Repozytoria i usługi to konkretne klasy wstrzykiwane przez autowiring. Interfejs ma tylko pogoda (`WeatherProviderInterface`), bo dostawcę zewnętrznego API realnie można zmienić. Koszt: Application i UI importują klasy z Infrastructure. Storage i tak nie potrzebuje interfejsu, żeby przejść na S3, bo abstrakcję daje Flysystem (`FILESYSTEM_DISK=s3`). Gdy pojawi się druga implementacja, wydzielenie interfejsu to mała zmiana.
- **Upload bez transakcji bazy.** Handler robi wolne I/O (Open-Meteo do 3 s, Imagick, zapis plików), a do bazy trafia jeden INSERT, który sam jest atomowy. Transakcja trzymałaby połączenie otwarte przez całe I/O bez żadnego zysku. Pliki i tak nie podlegają rollbackowi, więc sprząta je `try/catch` w handlerze. Usuwanie obrazu zostaje transakcyjne, a pliki znikają w `afterCommit()`.
- **CQRS-lite.** Zapis przez `CommandBus` (transakcja przez `TransactionPipe` dla komend, które jej potrzebują), odczyt przez klasy `*Query` z metodą `execute()`. Szkielet Core ma też QueryBus, ale proste odczyty nie potrzebują transakcji ani pipeline'u, więc moduły wołają klasy `*Query` bezpośrednio.
- **Pogoda synchronicznie z fallbackiem.** Open-Meteo zwraca 24 godziny (`hourly`) w strefie `Europe/Warsaw`; biorę wartość dla bieżącej pełnej godziny. Przy błędzie albo timeoucie (3 s) obraz zapisuje się z `temperature_celsius = null`, więc upload nie zależy od zewnętrznego API. Kolejka byłaby lepsza przy większym ruchu, ale tu dokłada infrastrukturę bez potrzeby.
- **EXIF tylko dla JPEG i TIFF**, bo tylko te formaty go przenoszą w sposób czytany przez `exif_read_data()`. IPTC czytam z bloku APP13, czyli w praktyce z JPEG. Pola binarne (np. MakerNote) zapisuję jako `base64:...`, bo psują JSON.
- **Imagick zamiast GD**, bo GD nie czyta TIFF.
- **Pliki nazwane własnym UUID**, nie oryginalną nazwą: brak kolizji i `../` w ścieżce. Oryginalna nazwa wraca przy pobieraniu w `Content-Disposition`.
- **Paginacja kursorowa** (`created_at DESC, id DESC`): dodawanie i usuwanie obrazów w trakcie przewijania nie dubluje ani nie gubi elementów.
- **Optymistyczne usuwanie** na froncie z rollbackiem przy błędzie; pobieranie to zwykły link, bo backend wysyła `Content-Disposition: attachment`.

## Weryfikacja

```bash
cd backend && composer ci      # larastan (level 8) + pint + phpunit
cd frontend && npm run check   # eslint + prettier + tsc + vitest + build
```

Lokalnie testy backendu chodzą na SQLite w pamięci. GitHub Actions (`.github/workflows/ci.yml`) uruchamia oba zestawy przy pushu do `main` i przy każdym PR, a testy backendu na MySQL 8.4.
