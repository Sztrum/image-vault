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

#### Moduł `MediaLibrary/Image`

Rdzeń aplikacji. Na razie zawiera warstwę domeny: reguły biznesowe obrazu i kontrakty, bez I/O i bez HTTP. Upload, miniatury, metadane i endpointy dochodzą w kolejnych etapach.

Moduł `MediaLibrary` jest nadrzędny i rejestruje podmoduł `Image` (`ImageModuleServiceProvider`). Kod jest podzielony na warstwy `UI → Application → Domain ← Infrastructure`.

**Reguły (Value Objects)**: każdy VO sprawdza swoje dane w konstruktorze i rzuca wyjątek domenowy, więc obiekt z błędnymi danymi nie może powstać:

| VO | Reguła | Wyjątek |
|---|---|---|
| `ImageDimensions` | szerokość i wysokość co najmniej 500 px | `ImageTooSmallException` |
| `FileSize` | od 1 B do 5 MB | `EmptyImageException`, `ImageTooLargeException` |
| `ImageName` | nazwa pliku 1–255 znaków (po `trim`) | `InvalidImageNameException` |
| `Uploader` | imię 1–100 znaków + `Email` z `Shared` | `InvalidUploaderNameException` |

**Format**: `ImageFormatEnum` (JPG, PNG, WebP, TIFF, BMP) mapuje typ MIME na format i z powrotem. Nieobsługiwany MIME kończy się wyjątkiem `UnsupportedImageFormatException`.

**DTO**:
- `NewImageDTO` przyjmuje wyłącznie VO i enum, więc do zapisu trafiają tylko sprawdzone dane.
- `ImageMetadataDTO` przenosi surowe tablice EXIF i IPTC.

**Kontrakty** (`Domain/Contracts`): porty, które implementuje warstwa Infrastructure:
- `ImageRepositoryInterface`: zapis, odczyt po id, usuwanie i paginacja kursorowa (najnowsze pierwsze),
- `ImageStorageInterface`: zapis oryginału i miniatury, ścieżka absolutna, usuwanie plików,
- `ThumbnailGeneratorInterface`: generuje miniaturę WebP,
- `ImageMetadataExtractorInterface`: wyciąga EXIF i IPTC z pliku.

**Model i tabela**: `Image` to model Eloquent z kluczem UUID v7. Tabela `images` przechowuje dane pliku (nazwa, format, rozmiar, wymiary, ścieżki oryginału i miniatury), dane uploadera, EXIF i IPTC jako JSON oraz opcjonalną temperaturę z chwili uploadu. Indeks `(created_at, id)` obsługuje paginację kursorową.

**Błędy**: wyjątki dziedziczą po `Core\Domain\Exceptions\DomainException` (HTTP 422). Mają tylko klucz tłumaczenia, a komunikaty po polsku są w `Application/Translations/pl/exceptions.php`.

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
