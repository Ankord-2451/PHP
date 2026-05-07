# BookEase — Платформа для аренды и бронирования недвижимости

> **Лабораторная работа №8** — Работа с реляционными базами данных в PHP с использованием PDO.

---

## 📋 Содержание

1. [Инструкции по запуску проекта](#-инструкции-по-запуску-проекта)
2. [Описание лабораторной работы](#-описание-лабораторной-работы)
3. [Документация проекта](#-документация-проекта)
   - [Функциональные возможности](#функциональные-возможности)
   - [Сценарии взаимодействия пользователей](#сценарии-взаимодействия-пользователей-с-приложением)
   - [Структура базы данных](#структура-базы-данных)
4. [Примеры использования](#-примеры-использования)
5. [Ответы на контрольные вопросы](#-ответы-на-контрольные-вопросы)
6. [Список использованных источников](#-список-использованных-источников)

---

## 🚀 Инструкции по запуску проекта

### Предварительные требования

Перед запуском установите следующее программное обеспечение:

| Инструмент | Версия | Ссылка |
|---|---|---|
| PHP | 8.2+ | https://www.php.net/downloads |
| Composer | latest | https://getcomposer.org/download/ |
| Node.js | 20+ | https://nodejs.org/ |
| PostgreSQL | 15 | https://www.postgresql.org/download/ |
| Redis | latest | https://redis.io/download |

### Шаг 1. Настройка базы данных

```bash
# Подключитесь к PostgreSQL
psql -U postgres

# Создайте базу данных
CREATE DATABASE bookease;
\q

# Инициализируйте схему базы данных
psql -U postgres -d bookease -f backend/database/migrations.sql

# Проверьте создание таблиц
psql -U postgres -d bookease -c "\dt"
```

### Шаг 2. Настройка бэкенда

```bash
# Установите PHP-зависимости
cd backend
composer install
```

Создайте файл `.env` в директории `backend/`:

```env
DB_HOST=localhost
DB_PORT=5432
DB_NAME=bookease
DB_USER=postgres
DB_PASS=postgres
REDIS_HOST=localhost
REDIS_PORT=6379
JWT_SECRET=your-secret-key-change-in-production
JWT_TTL=86400
APP_ENV=development
```

```bash
# Запустите PHP-сервер разработки
php -S localhost:8080 -t public
```

Бэкенд будет доступен по адресу: `http://localhost:8080`

### Шаг 3. Настройка фронтенда

```bash
# Установите Node.js-зависимости
cd frontend
npm install
```

Создайте файл `.env` в директории `frontend/`:

```env
VITE_API_URL=http://localhost:8080/api
```

```bash
# Запустите сервер разработки
npm run dev
```

Фронтенд будет доступен по адресу: `http://localhost:5173`

### Шаг 4. Запуск Redis

**Windows (WSL):**
```bash
wsl redis-server
```

**macOS (Homebrew):**
```bash
brew services start redis
```

**Linux:**
```bash
sudo systemctl start redis-server
```

### Запуск всего приложения

Откройте **4 отдельных терминала** и выполните в каждом:

```bash
# Терминал 1: Redis
redis-server

# Терминал 2: Бэкенд
cd backend && php -S localhost:8080 -t public

# Терминал 3: Фронтенд
cd frontend && npm run dev
```

Откройте `http://localhost:5173` в браузере.

### Устранение неполадок

**PHP-расширение pdo_pgsql отсутствует (Windows):**
- Откройте `php.ini` и раскомментируйте `;extension=pdo_pgsql` и `;extension=pdo`
- Перезапустите PHP-сервер

**Ошибка подключения к PostgreSQL:**
```bash
psql -U postgres -d bookease -c "SELECT 1"
```

**Ошибка подключения к Redis:**
```bash
redis-cli ping   # Должен вернуть PONG
```

**Порт уже занят:**
- Бэкенд: `php -S localhost:9000 -t public`
- Фронтенд: Vite автоматически переключится на порт 5174

---

## 📚 Описание лабораторной работы

### Цель

Освоить работу с реляционными базами данных в PHP с использованием PDO. Перенести хранение данных из файлов в базу данных, реализовать полный цикл операций CRUD: создание, чтение, обновление и удаление записей.

### Задание

Продолжение разработки проекта из лабораторной работы №7. Замена хранения данных в файле `data.json` на реляционную базу данных PostgreSQL.

### Выполненные шаги

| Шаг | Описание | Статус |
|---|---|---|
| 1 | Создание базы данных и таблиц (минимум 2 таблицы, 1 связь «1 ко многим») | ✅ |
| 2 | Подключение к БД через PDO, класс `Database` | ✅ |
| 3 | Реализация CRUD-операций через Repository-классы | ✅ |
| 4 | Обновление интерфейса: кнопки редактирования и удаления | ✅ |
| 5 | Безопасность: подготовленные выражения против SQL-инъекций | ✅ |
| 6 | Доп. задание: ООП, SOLID-принципы, PHPDoc-документация | ✅ |

### Реализованные компоненты

В проекте реализована следующая OOP-архитектура согласно требованиям лабораторной работы:

```
Core/Database.php         ← PDO-обёртка, управление подключением
Models/UserModel.php      ← Repository для таблицы users
Models/PropertyModel.php  ← Repository для таблицы properties
Models/BookingModel.php   ← Repository для таблицы bookings
Services/AuthService.php  ← Бизнес-логика аутентификации
Services/BookingService.php ← Бизнес-логика бронирований
```

---

## 📖 Документация проекта

**BookEase** — это веб-приложение для управления арендой и бронированием недвижимости. Пользователи могут просматривать объекты, создавать бронирования и управлять своими резервациями. Администраторы управляют всей платформой через отдельный дашборд.

### Технологический стек

**Бэкенд:**
- PHP 8.2+, собственный MVC-фреймворк
- PostgreSQL 15 (основное хранилище данных)
- Redis (кэширование, управление сессиями)
- JWT (JSON Web Tokens) — аутентификация
- PHPMailer — отправка писем для сброса пароля
- Composer-зависимости: `firebase/php-jwt`, `predis/predis`, `vlucas/phpdotenv`, `phpmailer/phpmailer`

**Фронтенд:**
- React 18.3.0 + Vite 5.4.0
- React Router DOM 6.18.0
- Tailwind CSS 3.4.5
- Axios 1.5.0
- Lucide React (иконки)

### Структура проекта

```
LI/
├── backend/
│   ├── app/
│   │   ├── Controllers/      # Обработка HTTP-запросов
│   │   │   ├── AdminController.php
│   │   │   ├── AuthController.php
│   │   │   ├── BookingController.php
│   │   │   ├── PropertyController.php
│   │   │   └── StatsController.php
│   │   ├── Core/             # Ядро фреймворка
│   │   │   ├── Database.php      ← PDO-подключение
│   │   │   ├── RedisClient.php
│   │   │   ├── Request.php
│   │   │   ├── Response.php
│   │   │   ├── Router.php
│   │   │   └── Validator.php
│   │   ├── Helpers/
│   │   │   └── JwtHelper.php
│   │   ├── Middleware/
│   │   │   └── AuthMiddleware.php
│   │   ├── Models/           # Доступ к данным (Repository)
│   │   │   ├── BookingModel.php
│   │   │   ├── PropertyModel.php
│   │   │   └── UserModel.php
│   │   └── Services/         # Бизнес-логика
│   │       ├── AdminService.php
│   │       ├── AuthService.php
│   │       ├── BookingService.php
│   │       ├── PropertyService.php
│   │       └── StatsService.php
│   ├── database/
│   │   └── migrations.sql    ← Схема базы данных
│   ├── public/
│   │   └── index.php         ← Точка входа
│   └── composer.json
├── frontend/
│   ├── src/
│   │   ├── components/       # Переиспользуемые UI-компоненты
│   │   ├── pages/            # Страницы приложения
│   │   ├── context/          # AuthContext
│   │   └── api/              # Axios-клиент
│   └── package.json
└── SETUP_LOCAL.md
```

### Архитектура бэкенда

Приложение использует слоистую архитектуру (Layered Architecture):

```
HTTP-запрос
    ↓
Router           (маршрутизация запросов)
    ↓
Middleware       (проверка JWT-токена, RBAC)
    ↓
Controllers      (обработка входных данных)
    ↓
Services         (бизнес-логика)
    ↓
Models           (запросы к БД через PDO)
    ↓
PostgreSQL / Redis
```

---

### Функциональные возможности

#### Для обычных пользователей

**Аутентификация:**
- Регистрация с валидацией email и пароля
- Вход в систему с получением JWT-токена
- Выход из системы
- Восстановление пароля через email (дополнительное задание)

**Просмотр недвижимости (публичный раздел):**
- Список всех активных объектов с пагинацией
- Фильтрация по местоположению и типу объекта
- Поиск по ключевым словам
- Детальная страница объекта с описанием, ценой, вместимостью, удобствами

**Управление бронированиями (защищённый раздел):**
- Создание нового бронирования с выбором дат, количества гостей, контактных данных
- Просмотр истории своих бронирований
- Отмена существующих бронирований
- Отслеживание статуса: `pending` → `confirmed` → `cancelled`

#### Для администраторов

**Управление объектами:**
- Создание новых объектов (название, описание, местоположение, цена, вместимость, тип, удобства)
- Редактирование существующих объектов
- Удаление объектов
- Активация/деактивация объектов

**Управление пользователями:**
- Просмотр всех зарегистрированных пользователей
- Просмотр статистики по пользователям

**Управление бронированиями:**
- Просмотр всех бронирований платформы
- Изменение статуса бронирования
- Отмена бронирований

**Статистика и аналитика:**
- Общая статистика платформы
- Метрики вовлечённости пользователей
- Динамика бронирований и доходов

---

### Сценарии взаимодействия пользователей с приложением

#### Сценарий 1: Регистрация и первый вход

```
1. Пользователь открывает http://localhost:5173
2. Нажимает "Зарегистрироваться"
3. Заполняет форму: имя, email, пароль, подтверждение пароля
4. Фронтенд выполняет валидацию полей (обязательность, формат email, сложность пароля)
5. POST /api/auth/register → AuthController → AuthService → UserModel
6. Пароль хешируется через password_hash() (bcrypt)
7. Запись создаётся в таблице users
8. Возвращается JWT-токен
9. Пользователь перенаправляется на главную страницу
```

#### Сценарий 2: Просмотр и поиск объектов (без авторизации)

```
1. Пользователь открывает главную страницу
2. GET /api/properties → PropertyController → PropertyService → PropertyModel
3. Данные извлекаются из таблицы properties (только активные записи)
4. Отображается сетка карточек с названием, локацией, ценой, вместимостью
5. Пользователь вводит местоположение в SearchBar
6. GET /api/properties?location=Beach&type=villa
7. Список фильтруется на стороне сервера через SQL WHERE-условие
8. Клик по карточке → GET /api/properties/:id → детальная страница
```

#### Сценарий 3: Создание бронирования

```
1. Авторизованный пользователь открывает страницу объекта
2. Заполняет форму бронирования:
   - Дата заезда (date_from)
   - Дата выезда (date_to)
   - Количество гостей (guests)
   - Контактный телефон (contact_phone)
   - Примечания (notes)
3. Клиентская валидация: дата выезда > дата заезда, guests ≤ capacity
4. POST /api/bookings с JWT-токеном в заголовке Authorization
5. AuthMiddleware проверяет токен
6. BookingService вычисляет total_price = price_per_day × кол-во дней
7. BookingModel сохраняет запись в таблицу bookings (статус: pending)
8. Пользователь перенаправляется на страницу "Мои бронирования"
```

#### Сценарий 4: Администратор управляет объектами

```
1. Администратор входит в систему (role = 'admin' в JWT-токене)
2. Открывает /admin → AdminPage
3. AuthMiddleware проверяет роль пользователя
4. Вкладка "Объекты" → GET /api/admin/properties (все записи, включая неактивные)
5. Нажимает "Добавить объект" → AdminPropertyForm
6. Заполняет все поля формы (7+ полей разных типов)
7. Серверная валидация в Validator.php
8. POST /api/properties → PropertyModel::create()
9. Запись добавляется в таблицу properties
10. Список обновляется автоматически
```

#### Сценарий 5: Восстановление пароля

```
1. Пользователь нажимает "Забыли пароль?" на странице входа
2. Вводит email-адрес
3. POST /api/auth/forgot-password
4. AuthService генерирует уникальный токен сброса
5. Токен сохраняется в поле reset_token таблицы users с временем истечения
6. PHPMailer отправляет письмо со ссылкой на сброс
7. Пользователь переходит по ссылке и вводит новый пароль
8. POST /api/auth/reset-password с токеном и новым паролем
9. Токен проверяется и удаляется, пароль обновляется
```

---

### Структура базы данных

База данных содержит **3 таблицы** с **2 связями** типа «один ко многим».

#### Схема отношений

```
users (1) ──────── (∞) bookings (∞) ────────── (1) properties
```

- Один пользователь может иметь много бронирований
- Один объект может быть забронирован много раз

#### Таблица `users`

```sql
CREATE TABLE users (
    id                  SERIAL PRIMARY KEY,
    name                VARCHAR(100) NOT NULL,
    email               VARCHAR(255) UNIQUE NOT NULL,
    password            VARCHAR(255) NOT NULL,          -- bcrypt-хэш
    role                VARCHAR(20)  NOT NULL DEFAULT 'user',
    reset_token         VARCHAR(255),                   -- токен сброса пароля
    reset_token_expires TIMESTAMP,
    created_at          TIMESTAMP NOT NULL DEFAULT NOW()
);
```

| Поле | Тип | Описание |
|---|---|---|
| `id` | SERIAL PK | Первичный ключ, автоинкремент |
| `name` | VARCHAR(100) | Полное имя пользователя |
| `email` | VARCHAR(255) UNIQUE | Уникальный email |
| `password` | VARCHAR(255) | Хэш пароля (bcrypt) |
| `role` | VARCHAR(20) | Роль: `user` или `admin` |
| `reset_token` | VARCHAR(255) | Токен сброса пароля |
| `reset_token_expires` | TIMESTAMP | Время истечения токена |
| `created_at` | TIMESTAMP | Дата регистрации |

#### Таблица `properties`

```sql
CREATE TABLE properties (
    id            SERIAL PRIMARY KEY,
    name          VARCHAR(200) NOT NULL,
    description   TEXT,
    location      VARCHAR(200),
    price_per_day NUMERIC(10,2) NOT NULL,
    capacity      INTEGER NOT NULL DEFAULT 1,
    property_type VARCHAR(50) NOT NULL,
    amenities     TEXT,
    is_active     BOOLEAN NOT NULL DEFAULT TRUE,
    created_at    TIMESTAMP NOT NULL DEFAULT NOW()
);
```

| Поле | Тип | Описание |
|---|---|---|
| `id` | SERIAL PK | Первичный ключ |
| `name` | VARCHAR(200) | Название объекта |
| `description` | TEXT | Подробное описание |
| `location` | VARCHAR(200) | Местоположение |
| `price_per_day` | NUMERIC(10,2) | Цена за сутки |
| `capacity` | INTEGER | Максимальное кол-во гостей |
| `property_type` | VARCHAR(50) | Тип: `apartment`, `house`, `villa` |
| `amenities` | TEXT | Список удобств |
| `is_active` | BOOLEAN | Доступность объекта |
| `created_at` | TIMESTAMP | Дата добавления |

#### Таблица `bookings`

```sql
CREATE TABLE bookings (
    id            SERIAL PRIMARY KEY,
    user_id       INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    property_id   INTEGER NOT NULL REFERENCES properties(id) ON DELETE CASCADE,
    date_from     DATE NOT NULL,
    date_to       DATE NOT NULL,
    guests        INTEGER NOT NULL DEFAULT 1,
    notes         TEXT,
    contact_phone VARCHAR(20),
    total_price   NUMERIC(10,2) NOT NULL,
    status        VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at    TIMESTAMP NOT NULL DEFAULT NOW()
);
```

| Поле | Тип | Описание |
|---|---|---|
| `id` | SERIAL PK | Первичный ключ |
| `user_id` | INTEGER FK | Ссылка на `users.id` |
| `property_id` | INTEGER FK | Ссылка на `properties.id` |
| `date_from` | DATE | Дата заезда |
| `date_to` | DATE | Дата выезда |
| `guests` | INTEGER | Количество гостей |
| `notes` | TEXT | Особые пожелания |
| `contact_phone` | VARCHAR(20) | Контактный телефон |
| `total_price` | NUMERIC(10,2) | Итоговая стоимость |
| `status` | VARCHAR(20) | Статус: `pending`, `confirmed`, `cancelled` |
| `created_at` | TIMESTAMP | Дата создания бронирования |

#### Индексы

```sql
CREATE INDEX idx_bookings_user_id       ON bookings(user_id);
CREATE INDEX idx_bookings_property_id   ON bookings(property_id);
CREATE INDEX idx_properties_location    ON properties(location);
CREATE INDEX idx_properties_type        ON properties(property_type);
```

---

## 💻 Примеры использования

### Регистрация пользователя (API)

```bash
curl -X POST http://localhost:8080/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Иван Петров",
    "email": "ivan@example.com",
    "password": "SecurePass123"
  }'
```

**Ответ:**
```json
{
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "name": "Иван Петров",
        "email": "ivan@example.com",
        "role": "user"
    },
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

### Получение списка объектов с фильтрацией

```bash
curl "http://localhost:8080/api/properties?location=Downtown&type=apartment&page=1"
```

**Ответ:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Modern Apartment",
            "location": "Downtown",
            "price_per_day": 150.00,
            "capacity": 4,
            "property_type": "apartment",
            "amenities": "WiFi, AC, Kitchen, Pool",
            "is_active": true
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 10,
        "total": 50
    }
}
```

### Создание бронирования (защищённый маршрут)

```bash
curl -X POST http://localhost:8080/api/bookings \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..." \
  -d '{
    "property_id": 1,
    "date_from": "2025-07-01",
    "date_to": "2025-07-07",
    "guests": 2,
    "contact_phone": "+37369000000",
    "notes": "Ранний заезд, пожалуйста"
  }'
```

**Ответ:**
```json
{
    "id": 42,
    "user_id": 1,
    "property_id": 1,
    "date_from": "2025-07-01",
    "date_to": "2025-07-07",
    "guests": 2,
    "total_price": 900.00,
    "status": "pending"
}
```

### Пример PDO-запроса с подготовленными выражениями

Ключевой паттерн, используемый во всех Model-классах проекта:

```php
<?php

/**
 * Получение объекта недвижимости по ID.
 *
 * @param int $id Идентификатор объекта
 * @return array|null Данные объекта или null, если не найден
 */
public function findById(int $id): ?array
{
    $stmt = $this->db->prepare(
        'SELECT * FROM properties WHERE id = :id AND is_active = TRUE'
    );
    $stmt->execute([':id' => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result ?: null;
}

/**
 * Создание нового бронирования.
 *
 * @param array $data Данные бронирования
 * @return int ID созданной записи
 */
public function create(array $data): int
{
    $stmt = $this->db->prepare(
        'INSERT INTO bookings
            (user_id, property_id, date_from, date_to, guests, notes, contact_phone, total_price)
         VALUES
            (:user_id, :property_id, :date_from, :date_to, :guests, :notes, :contact_phone, :total_price)
         RETURNING id'
    );
    $stmt->execute($data);

    return (int) $stmt->fetchColumn();
}
```

### Пример React-компонента для поиска объектов

```jsx
// frontend/src/components/SearchBar.jsx
import { useState } from "react";
import api from "../api/axios";

export default function SearchBar({ onResults }) {
    const [location, setLocation] = useState("");
    const [type, setType] = useState("");

    const handleSearch = async () => {
        const response = await api.get("/properties", {
            params: { location, type }
        });
        onResults(response.data.data);
    };

    return (
        <div className="flex gap-2 p-4 bg-white rounded-lg shadow">
            <input
                type="text"
                placeholder="Местоположение..."
                value={location}
                onChange={e => setLocation(e.target.value)}
                className="border rounded px-3 py-2 flex-1"
            />
            <select
                value={type}
                onChange={e => setType(e.target.value)}
                className="border rounded px-3 py-2"
            >
                <option value="">Все типы</option>
                <option value="apartment">Апартаменты</option>
                <option value="house">Дом</option>
                <option value="villa">Вилла</option>
            </select>
            <button
                onClick={handleSearch}
                className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
            >
                Найти
            </button>
        </div>
    );
}
```

---

## ❓ Ответы на контрольные вопросы

### 1. Что такое PDO и чем он отличается от устаревших расширений `mysqli_*`?

**PDO (PHP Data Objects)** — это унифицированный интерфейс для работы с базами данных в PHP. Он предоставляет единый набор методов независимо от используемой СУБД (PostgreSQL, MySQL, SQLite и др.).

Главные отличия от `mysqli_*`:

| Характеристика | PDO | mysqli_* |
|---|---|---|
| **Поддерживаемые СУБД** | 12+ (PostgreSQL, MySQL, SQLite, Oracle…) | Только MySQL/MariaDB |
| **Переносимость** | Высокая — смена СУБД требует минимальных изменений | Низкая — привязан к MySQL |
| **Подготовленные выражения** | Встроены, именованные параметры (`:name`) | Только позиционные (`?`) |
| **ООП-интерфейс** | Всегда объектно-ориентированный | Процедурный и ООП |
| **Обработка ошибок** | Исключения `PDOException` | Ошибки через `mysqli_error()` |
| **Режимы выборки** | `FETCH_ASSOC`, `FETCH_OBJ`, `FETCH_CLASS` и др. | Ограниченные варианты |

В данном проекте PDO используется в классе `Core/Database.php` для подключения к PostgreSQL:

```php
$this->pdo = new PDO(
    "pgsql:host={$host};port={$port};dbname={$dbname}",
    $user,
    $password,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
```

---

### 2. Что такое подготовленные выражения и зачем они нужны? Как они защищают от SQL-инъекций?

**Подготовленное выражение (prepared statement)** — это SQL-запрос, в котором пользовательские данные отделены от самого запроса с помощью параметров-заполнителей.

**Принцип работы:**

```php
// БЕЗ подготовленных выражений — УЯЗВИМО к SQL-инъекциям!
$email = $_POST['email']; // Например: "' OR '1'='1"
$stmt = $pdo->query("SELECT * FROM users WHERE email = '$email'");
// Итоговый запрос: SELECT * FROM users WHERE email = '' OR '1'='1'
// Результат: возвращаются ВСЕ пользователи!

// С подготовленными выражениями — БЕЗОПАСНО
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
$stmt->execute([':email' => $email]);
// PDO экранирует значение и передаёт его как данные, а не как часть SQL
```

**Как это защищает от инъекций:**

1. СУБД получает запрос и параметры **раздельно** — сначала компилирует структуру запроса, затем подставляет данные.
2. Параметры всегда интерпретируются как **данные**, а не как SQL-код — невозможно «сломать» логику запроса через пользовательский ввод.
3. Специальные символы (`'`, `"`, `;`, `--`) в параметрах автоматически **экранируются**.

В проекте все SQL-запросы в `Models/` используют только подготовленные выражения.

---

### 3. Что такое транзакция в базе данных? В каких ситуациях её стоит использовать?

**Транзакция** — это группа SQL-операций, которые выполняются как единое целое. Если хотя бы одна операция в группе завершилась с ошибкой, все изменения откатываются (`ROLLBACK`), как будто ни одна из операций не выполнялась.

Транзакции гарантируют свойства **ACID**:
- **Atomicity** (атомарность): все или ничего
- **Consistency** (согласованность): БД переходит из одного корректного состояния в другое
- **Isolation** (изолированность): параллельные транзакции не мешают друг другу
- **Durability** (долговечность): зафиксированные данные не теряются

**Когда использовать:**

Транзакции необходимы, когда одна бизнес-операция требует нескольких связанных SQL-запросов, и частичное выполнение недопустимо. Например, при бронировании в BookEase:

```php
try {
    $this->db->beginTransaction();

    // 1. Создать запись бронирования
    $bookingId = $this->bookingModel->create($bookingData);

    // 2. Обновить статистику объекта
    $this->propertyModel->incrementBookingCount($propertyId);

    $this->db->commit();
    return $bookingId;

} catch (Exception $e) {
    $this->db->rollBack();
    // Бронирование НЕ создано, статистика НЕ изменена
    throw $e;
}
```

Без транзакции: бронирование могло бы создаться, а обновление статистики — нет, что привело бы к несогласованности данных.

---

### 4. Чем отличается `fetch()` от `fetchAll()` в PDO?

| Метод | Описание | Когда использовать |
|---|---|---|
| `fetch()` | Возвращает **одну строку** из результирующего набора и перемещает внутренний указатель вперёд. При достижении конца возвращает `false`. | Когда ожидается одна запись: поиск по ID, проверка существования пользователя. |
| `fetchAll()` | Возвращает **все строки** сразу в виде массива. | Когда нужно получить весь список: все объекты, все бронирования. |

```php
// fetch() — получение одного пользователя по email
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
$stmt->execute([':email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
// $user = ['id' => 1, 'name' => 'John', ...] или false

// fetchAll() — получение всех объектов
$stmt = $pdo->prepare('SELECT * FROM properties WHERE is_active = TRUE');
$stmt->execute();
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
// $properties = [['id' => 1, ...], ['id' => 2, ...], ...]
```

**Важный нюанс:** `fetchAll()` загружает все строки в память сразу. При больших объёмах данных предпочтительнее итерировать через `fetch()` в цикле, чтобы не перегружать оперативную память.

---