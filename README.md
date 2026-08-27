# NG Test — тестове завдання PHP Developer

## Стек

- PHP 8.2 (без фреймворку)
- MySQL 8
- nginx + php-fpm
- Docker / docker-compose

## Запуск

1. Скопіювати файл оточення:

   ```
   cp .env.example .env
   ```

2. Зібрати та підняти контейнери:

   ```
   docker compose up -d --build
   ```

3. Застосувати міграції бази даних вручну. Спочатку підвантажити `.env` у shell — інакше `$DB_USER`/`$DB_PASSWORD` будуть порожні, і mysql-клієнт впаде з помилкою `Access denied for user '-p'@'localhost'`:

   ```
   set -a && source .env && set +a

   docker compose exec -T mysql mysql -u"$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" \
       < database/migrations/20260826203217_create_registrations_table.sql

   docker compose exec -T mysql mysql -u"$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" \
       < database/migrations/20260826203218_create_game_results_table.sql
   ```

4. Встановити автозавантаження composer:

   ```
   docker compose exec app composer install
   ```

5. Відкрити застосунок у браузері:

   ```
   http://localhost:8080
   ```

## Adminer (опційно)

Веб-інтерфейс до бази даних доступний на `http://localhost:8081` для ручної перевірки під час розробки. Дані для входу:

- Система: MySQL
- Сервер: `mysql`
- Користувач / Пароль / База даних: значення з `.env`

## Функціонал

- На головній сторінці — форма реєстрації (Username, Phone number).
- Після успішної реєстрації видається унікальне посилання на окрему сторінку ("сторінка А"), дійсне 7 днів. Після цього посилання перестає працювати.
- На сторінці А доступно:
  - перегенерувати поточне посилання (видає новий токен і скидає термін дії на 7 днів наново);
  - деактивувати поточне посилання;
  - натиснути "Imfeelinglucky" — отримати випадкове число (1–1000), результат Win/Lose (парне → Win, непарне → Lose) і суму виграшу (0 при Lose; інакше 10%/30%/50%/70% від числа залежно від діапазону);
  - натиснути "History" — побачити останні 3 результати "Imfeelinglucky".

## Структура проєкту

- `src/Controllers` — обробка HTTP-запитів, по одному класу на функціонал (реєстрація, дії сторінки А).
- `src/Repositories` — весь SQL, по одному класу на таблицю (`registrations`, `game_results`).
- `src/Services` — ігрова логіка (`GameService`), незалежна від HTTP та бази даних.
- `src/Views` — прості PHP/HTML-шаблони, без логіки окрім виводу.
- `src/Support` — допоміжні класи (генерація/перевірка CSRF-токена).
- `public/index.php` — фронт-контролер і роутер; єдиний файл, доступний веб-серверу ззовні.
- `database/migrations` — SQL-файли міграцій, застосовуються вручну (див. розділ "Запуск" вище).
