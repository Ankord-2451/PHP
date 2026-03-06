# Лабораторная работа №4. Массивы и Функции

## Цель работы

Освоить работу с массивами в PHP, применяя различные операции: создание, добавление, удаление, сортировка и поиск. Закрепить навыки работы с функциями, включая передачу аргументов, возвращаемые значения и анонимные функции.

---

## Задание 1. Работа с массивами

### Задание 1.1. Подготовка среды

Убедитесь, что у вас установлен PHP 8+. Создайте новый PHP-файл `index.php` и включите строгую типизацию:

```php
<?php
declare(strict_types=1);
```

---

### Задание 1.2. Создание массива транзакций

Создайте массив `$transactions`, содержащий информацию о банковских транзакциях. Каждая транзакция представлена ассоциативным массивом с полями:

- `id` – уникальный идентификатор транзакции
- `date` – дата совершения транзакции (YYYY-MM-DD)
- `amount` – сумма транзакции
- `description` – описание назначения платежа
- `merchant` – название организации, получившей платеж

```php
<?php
declare(strict_types=1);

/**
 * Массив банковских транзакций.
 * Каждая транзакция содержит id, дату, сумму, описание и получателя.
 *
 * @var array<int, array{id: int, date: string, amount: float, description: string, merchant: string}>
 */
$transactions = [
    [
        "id"          => 1,
        "date"        => "2019-01-01",
        "amount"      => 100.00,
        "description" => "Payment for groceries",
        "merchant"    => "SuperMart",
    ],
    [
        "id"          => 2,
        "date"        => "2020-02-15",
        "amount"      => 75.50,
        "description" => "Dinner with friends",
        "merchant"    => "Local Restaurant",
    ],
    [
        "id"          => 3,
        "date"        => "2021-05-10",
        "amount"      => 200.00,
        "description" => "Online course subscription",
        "merchant"    => "Udemy",
    ],
    [
        "id"          => 4,
        "date"        => "2022-11-23",
        "amount"      => 49.99,
        "description" => "Monthly gym membership",
        "merchant"    => "FitLife",
    ],
    [
        "id"          => 5,
        "date"        => "2023-07-04",
        "amount"      => 320.00,
        "description" => "Flight ticket purchase",
        "merchant"    => "AirBooking",
    ],
];
```

---

### Задание 1.3. Вывод списка транзакций

Используйте `foreach`, чтобы вывести список транзакций в HTML-таблице:

```php
<?php
/**
 * Выводим таблицу транзакций.
 * Для каждой транзакции также выводим количество дней с момента её совершения.
 */
?>
<table border='1'>
    <thead>
        <tr>
            <th>ID</th>
            <th>Дата</th>
            <th>Сумма</th>
            <th>Описание</th>
            <th>Получатель</th>
            <th>Дней назад</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($transactions as $transaction): ?>
        <tr>
            <td><?= $transaction['id'] ?></td>
            <td><?= $transaction['date'] ?></td>
            <td><?= number_format($transaction['amount'], 2) ?></td>
            <td><?= $transaction['description'] ?></td>
            <td><?= $transaction['merchant'] ?></td>
            <td><?= daysSinceTransaction($transaction['date']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2"><strong>Итого:</strong></td>
            <td colspan="4"><strong><?= number_format(calculateTotalAmount($transactions), 2) ?></strong></td>
        </tr>
    </tfoot>
</table>
```

*[Скриншот таблицы транзакций]*

---

### Задание 1.4. Реализация функций

#### `calculateTotalAmount` — общая сумма транзакций

```php
<?php
/**
 * Вычисляет общую сумму всех транзакций.
 *
 * @param array $transactions Массив транзакций.
 * @return float Общая сумма.
 */
function calculateTotalAmount(array $transactions): float
{
    $total = 0.0;
    foreach ($transactions as $transaction) {
        $total += $transaction['amount'];
    }
    return $total;
}

echo "Общая сумма транзакций: " . number_format(calculateTotalAmount($transactions), 2) . " руб.";
```

---

#### `findTransactionByDescription` — поиск по описанию

```php
<?php
/**
 * Ищет транзакции, в описании которых содержится указанная подстрока.
 *
 * @param string $descriptionPart Часть описания для поиска.
 * @return array Массив найденных транзакций.
 */
function findTransactionByDescription(string $descriptionPart): array
{
    global $transactions;

    $result = [];
    foreach ($transactions as $transaction) {
        if (stripos($transaction['description'], $descriptionPart) !== false) {
            $result[] = $transaction;
        }
    }
    return $result;
}

// Пример использования:
$found = findTransactionByDescription("groceries");
print_r($found);
```

---

#### `findTransactionById` — поиск по ID (два варианта)

**Вариант с `foreach`:**

```php
<?php
/**
 * Ищет транзакцию по идентификатору с помощью цикла foreach.
 *
 * @param int $id Идентификатор транзакции.
 * @return array|null Найденная транзакция или null.
 */
function findTransactionById(int $id): ?array
{
    global $transactions;

    foreach ($transactions as $transaction) {
        if ($transaction['id'] === $id) {
            return $transaction;
        }
    }
    return null;
}
```

**Вариант с `array_filter` (на высшую оценку):**

```php
<?php
/**
 * Ищет транзакцию по идентификатору с помощью array_filter.
 *
 * @param int $id Идентификатор транзакции.
 * @return array|null Найденная транзакция или null.
 */
function findTransactionByIdFilter(int $id): ?array
{
    global $transactions;

    $result = array_filter(
        $transactions,
        fn(array $transaction): bool => $transaction['id'] === $id
    );

    return !empty($result) ? array_values($result)[0] : null;
}

// Пример использования:
$transaction = findTransactionByIdFilter(3);
print_r($transaction);
```

---

#### `daysSinceTransaction` — дней с момента транзакции

```php
<?php
/**
 * Возвращает количество дней между датой транзакции и текущим днём.
 *
 * @param string $date Дата транзакции в формате YYYY-MM-DD.
 * @return int Количество дней.
 */
function daysSinceTransaction(string $date): int
{
    $transactionDate = new DateTime($date);
    $today           = new DateTime('today');
    $diff            = $today->diff($transactionDate);
    return (int) $diff->days;
}

// Пример использования:
echo daysSinceTransaction("2020-02-15") . " дней назад";
```

---

#### `addTransaction` — добавление транзакции

```php
<?php
/**
 * Добавляет новую транзакцию в глобальный массив $transactions.
 *
 * @param int    $id          Уникальный идентификатор транзакции.
 * @param string $date        Дата транзакции (YYYY-MM-DD).
 * @param float  $amount      Сумма транзакции.
 * @param string $description Описание платежа.
 * @param string $merchant    Название получателя платежа.
 * @return void
 */
function addTransaction(
    int $id,
    string $date,
    float $amount,
    string $description,
    string $merchant
): void {
    global $transactions;

    $transactions[] = [
        'id'          => $id,
        'date'        => $date,
        'amount'      => $amount,
        'description' => $description,
        'merchant'    => $merchant,
    ];
}

// Пример использования:
addTransaction(6, "2024-03-01", 150.00, "Book purchase", "Amazon");
```

---

### Задание 1.5. Сортировка транзакций

#### Сортировка по дате

```php
<?php
/**
 * Сортируем транзакции по дате (по возрастанию) с помощью usort.
 * Используем класс DateTime для корректного сравнения дат.
 */
usort($transactions, function (array $a, array $b): int {
    $dateA = new DateTime($a['date']);
    $dateB = new DateTime($b['date']);
    return $dateA <=> $dateB;
});

echo "<h3>Транзакции, отсортированные по дате:</h3>";
foreach ($transactions as $t) {
    echo "{$t['date']} — {$t['description']}<br>";
}
```

*[Скриншот отсортированного по дате вывода]*

#### Сортировка по сумме (по убыванию)

```php
<?php
/**
 * Сортируем транзакции по сумме (по убыванию) с помощью usort.
 */
usort($transactions, function (array $a, array $b): int {
    return $b['amount'] <=> $a['amount'];
});

echo "<h3>Транзакции, отсортированные по сумме (убывание):</h3>";
foreach ($transactions as $t) {
    echo "{$t['amount']} — {$t['description']}<br>";
}
```

*[Скриншот отсортированного по сумме вывода]*

---

## Задание 2. Работа с файловой системой

Создайте директорию `image/` и поместите в неё не менее 20–30 изображений с расширением `.jpg`. Затем создайте файл `index.php` со следующей структурой:

```php
<?php
declare(strict_types=1);

/**
 * Задаём путь к папке с изображениями.
 *
 * @var string $dir Путь к директории с изображениями.
 */
$dir = 'image/';

/**
 * Сканируем содержимое директории.
 * scandir() возвращает массив имён файлов и каталогов по указанному пути.
 *
 * @var array|false $files Список файлов или false при ошибке.
 */
$files = scandir($dir);

if ($files === false) {
    return;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>#cats</title>
    <style>
        body { font-family: monospace; margin: 0; padding: 0; }

        header {
            border: 1px solid #000;
            padding: 10px 20px;
        }
        nav a {
            margin-right: 20px;
            text-decoration: none;
            color: #000;
            font-weight: bold;
        }
        main { padding: 20px; }
        h1 { font-size: 1.4em; margin-bottom: 4px; }
        .subtitle { color: #aaa; margin-bottom: 20px; }

        .gallery {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        .gallery img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        footer {
            text-align: center;
            padding: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<header>
    <nav>
        <a href="#">About Cats</a> |
        <a href="#">News</a> |
        <a href="#">Contacts</a>
    </nav>
</header>

<main>
    <h1>#cats</h1>
    <p class="subtitle">Explore a world of cats</p>

    <div class="gallery">
        <?php
        for ($i = 0; $i < count($files); $i++) {
            // Пропускаем текущий и родительский каталог
            if ($files[$i] !== '.' && $files[$i] !== '..') {
                $path = $dir . $files[$i];
                ?>
                <img src="<?= htmlspecialchars($path) ?>" alt="cat photo">
                <?php
            }
        }
        ?>
    </div>
</main>

<footer>
    USM &copy; 2024
</footer>

</body>
</html>
```

*[Скриншот галереи изображений]*

---

## Контрольные вопросы

### 1. Что такое массивы в PHP?

Массив в PHP — это упорядоченная структура данных, которая хранит набор значений под одним именем переменной. Каждый элемент массива имеет ключ и значение. PHP поддерживает три вида массивов:

- **Индексированные** — ключи являются числами (0, 1, 2, …).
- **Ассоциативные** — ключи задаются строками (`"name"`, `"date"` и т.д.).
- **Многомерные** — массивы, элементами которых являются другие массивы.

### 2. Каким образом можно создать массив в PHP?

Массив можно создать несколькими способами:

**С помощью конструкции `[]` (рекомендуемый способ):**

```php
$arr = [1, 2, 3];
$assoc = ["name" => "Alice", "age" => 25];
```

**С помощью функции `array()`:**

```php
$arr = array(1, 2, 3);
$assoc = array("name" => "Alice", "age" => 25);
```

**Добавлением элементов по одному:**

```php
$arr = [];
$arr[] = "first";
$arr[] = "second";
```

### 3. Для чего используется цикл `foreach`?

Цикл `foreach` предназначен для перебора всех элементов массива или объекта. Он автоматически проходит по каждому элементу, не требуя ручного управления счётчиком или индексом.

**Синтаксис:**

```php
// Только значения
foreach ($array as $value) {
    echo $value;
}

// Ключи и значения
foreach ($array as $key => $value) {
    echo "$key: $value";
}
```

Цикл `foreach` особенно удобен при работе с ассоциативными и многомерными массивами, как в данной лабораторной работе при переборе транзакций.
