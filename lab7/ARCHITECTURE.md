# Архитектура проекта Recipe Manager

## Диаграмма потока данных

```
┌─────────────────────────────────────────────────────────────┐
│                     RECIPE MANAGER                          │
└─────────────────────────────────────────────────────────────┘

                         USER INTERFACE (HTML/CSS)
                                 │
                    ┌────────────┼────────────┐
                    │            │            │
                    ▼            ▼            ▼
              [Add Recipe]  [View List]  [Edit/Delete]
                    │            │            │
                    └────────────┼────────────┘
                                 │
                    ┌────────────┴────────────┐
                    │                         │
                    ▼                         ▼
                [save.php]              [edit.php]
                    │                   [update.php]
                    │                   [delete.php]
                    │                         │
                    └────────────┬────────────┘
                                 │
                          [VALIDATION LAYER]
                                 │
                    ┌────────────┴────────────┐
                    │                         │
              [RequiredValidator]      [LengthValidator]
                    │                         │
                    └────────────┬────────────┘
                                 │
                          [Recipe CLASS]
                                 │
                          [RecipeStorage]
                                 │
                          [data.json]

```

## Компоненты

### Frontend (HTML + CSS)
- `header.php` - заголовок HTML
- `recipe_form.php` - форма добавления/редактирования
- `recipes_table.php` - таблица со списком
- `error_messages.php` - блок ошибок
- `success_message.php` - сообщение об успехе
- `footer.php` - закрывающий HTML

### Controllers
- `index.php` - главная страница (GET)
- `save.php` - добавление рецепта (POST)
- `edit.php` - форма редактирования (GET)
- `update.php` - обновление рецепта (POST)
- `delete.php` - удаление рецепта (GET)

### Models
- `Recipe.php` - модель рецепта
- `RecipeStorage.php` - работа с JSON

### Validators
- `ValidatorInterface.php` - интерфейс
- `RequiredValidator.php` - проверка пустоты
- `LengthValidator.php` - проверка длины

### Database
- `data.json` - JSON-хранилище

### Styling
- `style.css` - все стили приложения

## Поток добавления рецепта

```
1. User заполняет форму на index.php
   ↓
2. Форма отправляется на save.php (POST)
   ↓
3. save.php запускает валидаторы
   ├─→ RequiredValidator (обязательные поля)
   └─→ LengthValidator (title должен быть 3-100)
   ↓
4. Если ошибки - отображаются ошибки (error_messages.php)
   ↓
5. Если OK - создается объект Recipe
   ↓
6. RecipeStorage.save() добавляет в data.json
   ↓
7. Отображается сообщение об успехе (success_message.php)
   ↓
8. User может вернуться на index.php
```

## Поток редактирования

```
1. User нажимает ✏️ на рецепт
   ↓
2. Переход на edit.php?id=X (GET)
   ↓
3. edit.php загружает рецепт из data.json через RecipeStorage
   ↓
4. recipe_form.php отображается с заполненными данными ($isEdit=true)
   ↓
5. Форма отправляется на update.php (POST)
   ↓
6. Валидация данных
   ↓
7. RecipeStorage.update(id, newData) обновляет в JSON
   ↓
8. Сообщение об успехе
```

## Поток удаления

```
1. User нажимает ❌ на рецепт
   ↓
2. JavaScript подтверждение
   ↓
3. Переход на delete.php?id=X (GET)
   ↓
4. Проверка существования рецепта
   ↓
5. RecipeStorage.delete(id) удаляет из JSON
   ↓
6. Сообщение об успехе
```

## Безопасность

✓ htmlspecialchars() - защита от XSS
✓ Валидация всех входных данных
✓ Проверка ID рецепта перед удалением
✓ Type-hinted методы в классах
✓ Использование intval() для ID

## Производительность

- Все рецепты загружаются в памяти (OK для малых объемов)
- JSON сортируется в памяти перед отображением
- Нет индексирования (но JSON достаточен для учебного проекта)

## Возможные улучшения

- Добавить поиск по названию
- Добавить фильтры по категории
- Добавить постранивание (если много рецептов)
- Добавить базу данных (MySQL/SQLite)
- Добавить аутентификацию
- Добавить логирование операций
- Добавить AJAX для асинхронных операций
