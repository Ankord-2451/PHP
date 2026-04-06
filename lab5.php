<?php

declare(strict_types=1);

/**
 * Интерфейс хранилища транзакций
 */
interface TransactionStorageInterface
{
    public function addTransaction(Transaction $transaction): void;

    public function removeTransactionById(int $id): void;

    /**
     * @return Transaction[]
     */
    public function getAllTransactions(): array;

    public function findById(int $id): ?Transaction;
}

/**
 * Класс банковской транзакции
 */
class Transaction
{
    private int $id;
    private DateTime $date;
    private float $amount;
    private string $description;
    private string $merchant;

    public function __construct(
        int $id,
        string $date,
        float $amount,
        string $description,
        string $merchant
    ) {
        $this->id = $id;
        $this->date = new DateTime($date);
        $this->amount = $amount;
        $this->description = $description;
        $this->merchant = $merchant;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getMerchant(): string
    {
        return $this->merchant;
    }

    /**
     * Возвращает количество дней с момента транзакции
     */
    public function getDaysSinceTransaction(): int
    {
        $now = new DateTime();
        return (int)$this->date->diff($now)->format('%a');
    }
}

/**
 * Репозиторий транзакций
 */
class TransactionRepository implements TransactionStorageInterface
{
    /**
     * @var Transaction[]
     */
    private array $transactions = [];

    public function addTransaction(Transaction $transaction): void
    {
        $this->transactions[] = $transaction;
    }

    public function removeTransactionById(int $id): void
    {
        foreach ($this->transactions as $key => $transaction) {
            if ($transaction->getId() === $id) {
                unset($this->transactions[$key]);
            }
        }
    }

    public function getAllTransactions(): array
    {
        return array_values($this->transactions);
    }

    public function findById(int $id): ?Transaction
    {
        foreach ($this->transactions as $transaction) {
            if ($transaction->getId() === $id) {
                return $transaction;
            }
        }
        return null;
    }
}

/**
 * Класс бизнес-логики
 */
class TransactionManager
{
    public function __construct(
        private TransactionStorageInterface $repository
    ) {
    }

    public function calculateTotalAmount(): float
    {
        $sum = 0;
        foreach ($this->repository->getAllTransactions() as $transaction) {
            $sum += $transaction->getAmount();
        }
        return $sum;
    }

    public function calculateTotalAmountByDateRange(string $startDate, string $endDate): float
    {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);

        $sum = 0;
        foreach ($this->repository->getAllTransactions() as $transaction) {
            if ($transaction->getDate() >= $start && $transaction->getDate() <= $end) {
                $sum += $transaction->getAmount();
            }
        }
        return $sum;
    }

    public function countTransactionsByMerchant(string $merchant): int
    {
        $count = 0;
        foreach ($this->repository->getAllTransactions() as $transaction) {
            if ($transaction->getMerchant() === $merchant) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * @return Transaction[]
     */
    public function sortTransactionsByDate(): array
    {
        $transactions = $this->repository->getAllTransactions();

        usort($transactions, function ($a, $b) {
            return $a->getDate() <=> $b->getDate();
        });

        return $transactions;
    }

    /**
     * @return Transaction[]
     */
    public function sortTransactionsByAmountDesc(): array
    {
        $transactions = $this->repository->getAllTransactions();

        usort($transactions, function ($a, $b) {
            return $b->getAmount() <=> $a->getAmount();
        });

        return $transactions;
    }
}

/**
 * Класс для рендеринга HTML-таблицы
 */
final class TransactionTableRenderer
{
    /**
     * @param Transaction[] $transactions
     */
    public function render(array $transactions): string
    {
        $html = "<table border='1' cellpadding='8'>";
        $html .= "<tr>
                    <th>ID</th>
                    <th>Дата</th>
                    <th>Сумма</th>
                    <th>Описание</th>
                    <th>Получатель</th>
                    <th>Категория</th>
                    <th>Дней назад</th>
                  </tr>";

        foreach ($transactions as $t) {
            $html .= "<tr>";
            $html .= "<td>{$t->getId()}</td>";
            $html .= "<td>{$t->getDate()->format('Y-m-d')}</td>";
            $html .= "<td>{$t->getAmount()}</td>";
            $html .= "<td>{$t->getDescription()}</td>";
            $html .= "<td>{$t->getMerchant()}</td>";
            $html .= "<td>" . $this->getCategory($t->getMerchant()) . "</td>";
            $html .= "<td>{$t->getDaysSinceTransaction()}</td>";
            $html .= "</tr>";
        }

        $html .= "</table>";
        return $html;
    }

    private function getCategory(string $merchant): string
    {
        return match ($merchant) {
            'Amazon' => 'Покупки',
            'Uber' => 'Транспорт',
            'McDonalds' => 'Еда',
            default => 'Другое',
        };
    }
}

/* ================== ДАННЫЕ ================== */

$repository = new TransactionRepository();

$transactions = [
    new Transaction(1, '2026-03-01', 120.5, 'Покупка товаров', 'Amazon'),
    new Transaction(2, '2026-03-02', 50, 'Поездка', 'Uber'),
    new Transaction(3, '2026-03-03', 15.75, 'Обед', 'McDonalds'),
    new Transaction(4, '2026-03-04', 200, 'Техника', 'Amazon'),
    new Transaction(5, '2026-03-05', 30, 'Такси', 'Uber'),
    new Transaction(6, '2026-03-06', 12.5, 'Кофе', 'Starbucks'),
    new Transaction(7, '2026-03-07', 500, 'Ноутбук', 'Amazon'),
    new Transaction(8, '2026-03-08', 8.9, 'Снэк', 'McDonalds'),
    new Transaction(9, '2026-03-09', 60, 'Транспорт', 'Uber'),
    new Transaction(10, '2026-03-10', 100, 'Одежда', 'Zara'),
];

foreach ($transactions as $t) {
    $repository->addTransaction($t);
}

/* ================== ЛОГИКА ================== */

$manager = new TransactionManager($repository);
$renderer = new TransactionTableRenderer();

echo "<h2>Все транзакции</h2>";
echo $renderer->render($repository->getAllTransactions());

echo "<h3>Общая сумма: " . $manager->calculateTotalAmount() . "</h3>";

echo "<h3>Сумма за период (2026-03-01 — 2026-03-05): "
    . $manager->calculateTotalAmountByDateRange('2026-03-01', '2026-03-05')
    . "</h3>";

echo "<h3>Транзакции Amazon: "
    . $manager->countTransactionsByMerchant('Amazon')
    . "</h3>";

echo "<h2>Сортировка по сумме (убывание)</h2>";
echo $renderer->render($manager->sortTransactionsByAmountDesc());