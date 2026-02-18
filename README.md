# Laravel blog
Platforma blogowa zbudowana w oparciu o framework Laravel, zaprojektowana z myślą o wydajności, bezpieczeństwie i komforcie użytkownika. Projekt łączy nowoczesny backend z interaktywnym frontendem, oferując kompletny system zarządzania treścią i społecznością.

## Kluczowe Funkcjonalności
### Zarządzanie Treścią (CRUD)
- system Postów: pełna obsługa tworzenia, edycji i usuwania wpisów przez autorów oraz administratorów,
- snteligentne Kategorie: dynamiczne tworzenie nowych kategorii oraz mechanizm automatycznego usuwania tych, które po usunięciu lub edycji posta pozostały puste,
- interaktywne Komentarze: system dodawania i edycji komentarzy "w locie" przy użyciu Alpine.js, bez konieczności przeładowywania strony.

### Zaawansowane Filtrowanie i UX
- wyszukiwarka: filtrowanie postów według fraz kluczowych, kategorii oraz precyzyjnego zakresu dat publikacji,
- sortowanie: możliwość wyboru kolejności wyświetlania postów i komentarzy (najnowsze/najstarsze) przy zachowaniu aktywnych filtrów w adresie URL,
- prywatność (Content Locking): wymaganie posiadania konta poprzez rozmycie treści postów (efekt Blur) dla niezalogowanych użytkowników.

### Profile Użytkowników i Administracja
- widok profilu: rozbudowany widok użytkownika z zakładkami aktywności (Posty / Komentarze) i statystykami najczęściej wybieranych kategorii,
- niezależna Paginacja: możliwość niezależnego przeglądania stron postów i komentarzy na profilu dzięki dedykowanym parametrom paginacji,
- panel Administratora: chroniony system zarządzania rolami użytkowników oraz uprawnieniami do usuwania treści.

### Stack Technologiczny
- backend: PHP & Laravel,
- frontend: Blade Templates & Tailwind CSS (Stylizacja utility-first),
- interaktywność: Alpine.js (Lekka reaktywność po stronie klienta),
- uwierzytelnianie: Laravel Breeze (dostosowane przekierowania na stronę główną po logowaniu/rejestracji).

### Wydajność i Bezpieczeństwo
- Eager Loading: wyeliminowanie problemu zapytań N+1 poprzez optymalne pobieranie relacji (`with()`, `withCount()`),
- Middleware: wielopoziomowa ochrona tras i weryfikacja uprawnień autora przy każdej próbie edycji lub usunięcia danych,
- SEO & UX: przyjazne adresy URL generowane automatycznie z tytułów (slugs).

## Instalacja
- sklonuj repozytorium za pomocą polecenia `git clone`,
- zainstaluj zależności: `composer install` oraz `npm install && npm run dev`,
- skonfiguruj plik `.env` (połączenie z bazą danych MySQL),
- utwórz ręcznie pustą bazę danych,
- uruchom migracje bazy danych: `php artisan migrate`,
- uruchom aplikację: `php artisan serve`.
