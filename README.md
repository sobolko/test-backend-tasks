# Laravel Test API

## Описание

RESTful API на Laravel для управления задачами с аутентификацией, ролевой системой и пагинацией. 

## Технологический стек

- **Laravel 12** - PHP фреймворк
- **PHP 8.2+** - серверный язык
- **PostgreSQL** - база данных
- **Laravel Sanctum** - аутентификация API
- **Spatie Laravel Permission** - управление ролями и разрешениями
- **Laravel Sail** - Docker окружение для разработки
- **Vite** - сборщик ресурсов

## Возможности

### Аутентификация
- Регистрация и авторизация пользователей
- Токены через Laravel Sanctum
- Ролевая система (admin, user)

### API Endpoints

**Аутентификация:**
- `POST /api/login` - вход
- `POST /api/logout` - выход

**Задачи (требует авторизации):**
- `GET /api/tasks` - список задач с пагинацией и сортировкой
- `POST /api/tasks` - создание задачи
- `GET /api/tasks/{id}` - просмотр задачи
- `PUT /api/tasks/{id}` - обновление задачи
- `DELETE /api/tasks/{id}` - удаление задачи
- `GET /api/tasks-stats` - статистика по задачам

**Пользователи (только admin):**
- `GET /api/users` - список пользователей

**Внешнее API:**
- `GET /api/external-data` - случайный факт о кошках (с кешированием)

### Функции
- **Пагинация** - `?page=1&per_page=15`
- **Сортировка** - `?sort_by=created_at&sort_direction=desc`
- **Кеширование** внешних API запросов
- **Политики доступа** для CRUD операций
- **Resource классы** для форматирования API ответов

## Архитектурные решения

### Сервисный слой (Service Layer)
- **TaskStatsService** - бизнес-логика для расчета статистики задач
- Выделение сложной логики из контроллеров для повышения читаемости и тестируемости
- Инъекция зависимостей через конструктор контроллера

### Enum классы
- **TaskStatus** - типизированные статусы задач (New, InProgress, Completed)  
- Замена "магических строк" на типобезопасные значения
- Методы для получения читаемых названий статусов

### Observer паттерн
- **TaskObserver** - автоматическая обработка событий модели Task
- Логирование изменений, уведомления, очистка связанных данных
- Регистрация в `AppServiceProvider`

### API Resources
- **TaskResource** - унифицированное форматирование данных задач
- **UserResource** - контроль отображения пользовательских данных
- Скрытие внутренних полей, добавление вычисляемых атрибутов

### Database Seeding
- **TestUserSeeder** - создание тестовых пользователей с ролями
- **TestTaskSeeder** - наполнение базы примерами задач
- Быстрое развертывание с демо-данными для разработки

### Policy классы
- **TaskPolicy** - авторизация доступа к операциям с задачами
- Проверка прав владельца на просмотр/редактирование
- Интеграция с middleware для автоматической проверки доступа

## Быстрый старт

### Требования
- Docker и Docker Compose
- Git

### Установка

1. **Клонирование репозитория**
   ```bash
   git clone <repository-url>
   cd backend
   ```

2. **Настройка окружения**
   ```bash
   cp .env.example .env
   ```

3. **Запуск через Laravel Sail**
   ```bash
   # Первый запуск - установка зависимостей
   docker run --rm \
       -u "$(id -u):$(id -g)" \
       -v "$(pwd):/var/www/html" \
       -w /var/www/html \
       laravelsail/php85-composer:latest \
       composer install --ignore-platform-reqs

   # Запуск приложения
   ./vendor/bin/sail up -d
   ```

4. **Настройка приложения**
   ```bash
   # Генерация ключа
   ./vendor/bin/sail artisan key:generate

   # Миграции и сиды
   ./vendor/bin/sail artisan migrate --seed

   # Публикация настроек ролей
   ./vendor/bin/sail artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
   ```

5. **Установка фронтенд зависимостей**
   ```bash
   ./vendor/bin/sail npm install
   ./vendor/bin/sail npm run dev
   ```

### Быстрая настройка (альтернатива)
```bash
cp .env.example .env
composer run setup
./vendor/bin/sail up -d
```

## Использование

### Тестирование API

Приложение будет доступно по адресу: `http://localhost`

**Пользователь по умолчанию:**
```bash
test@example.com
password
```

**Авторизация:**
```bash
curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'
```

**Получение задач:**
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
     "http://localhost/api/tasks?page=1&per_page=10&sort_by=created_at&sort_direction=desc"
```

### Управление проектом

**Основные команды:**
```bash
# Запуск приложения
./vendor/bin/sail up -d

# Остановка
./vendor/bin/sail down

# Просмотр логов
./vendor/bin/sail logs

# Выполнение команд artisan
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan tinker

# Запуск тестов
./vendor/bin/sail test

# Очистка кеша
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
```

## Структура проекта

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php      # Аутентификация
│   │   ├── TaskController.php      # CRUD задач
│   │   ├── UserController.php      # Управление пользователями
│   │   └── ExternalController.php  # Внешние API
│   ├── Resources/
│   │   ├── TaskResource.php        # Форматирование задач
│   │   └── UserResource.php        # Форматирование пользователей
│   └── Requests/
│       └── TaskRequest.php         # Валидация задач
├── Models/
│   ├── Task.php                    # Модель задачи
│   └── User.php                    # Модель пользователя
├── Policies/
│   └── TaskPolicy.php              # Политики доступа
└── Services/
    └── TaskStatsService.php        # Сервис статистики
```

## Разработка

### Добавление новых функций

1. **Создание миграции:**
   ```bash
   ./vendor/bin/sail artisan make:migration create_new_table
   ```

2. **Создание контроллера с ресурсами:**
   ```bash
   ./vendor/bin/sail artisan make:controller NewController --api --resource
   ```

3. **Создание модели с миграцией:**
   ```bash
   ./vendor/bin/sail artisan make:model NewModel -m
   ```

### Отладка

- **Логи:** `./vendor/bin/sail logs`
- **Tinker:** `./vendor/bin/sail artisan tinker`
- **Telescope:** (при установке) `http://localhost/telescope`

## Производство

Для развертывания на продакшене:

1. Настроить переменные окружения в `.env`
2. Установить зависимости: `composer install --no-dev --optimize-autoloader`
3. Настроить сервер (nginx/apache) на папку `public/`
4. Запустить миграции: `php artisan migrate --force`
5. Оптимизировать: `php artisan optimize`

## Лицензия

MIT License

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
