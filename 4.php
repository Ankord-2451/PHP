<?php

declare(strict_types=1);

$transactions = [
    [
        "id" => 1,
        "date" => "2019-01-01",
        "amount" => 100.00,
        "description" => "Payment for groceries",
        "merchant" => "SuperMart",
    ],
    [
        "id" => 2,
        "date" => "2020-02-15",
        "amount" => 75.50,
        "description" => "Dinner with friends",
        "merchant" => "Local Restaurant",
    ],
    [
        "id" => 3,
        "date" => "2023-05-10",
        "amount" => 200.00,
        "description" => "Electronics purchase",
        "merchant" => "TechStore",
    ]
];
 
function calculateTotalAmount(array $transactions): float
{
    $total = 0;

    foreach ($transactions as $transaction) {
        $total += $transaction["amount"];
    }

    return $total;
}


function findTransactionByDescription(string $descriptionPart): array
{
    global $transactions;

    $result = [];

    foreach ($transactions as $transaction) {
        if (stripos($transaction["description"], $descriptionPart) !== false) {
            $result[] = $transaction;
        }
    }

    return $result;
}

function findTransactionById(int $id): ?array
{
    global $transactions;

    foreach ($transactions as $transaction) {
        if ($transaction["id"] === $id) {
            return $transaction;
        }
    }

    return null;
}


function findTransactionByIdFilter(int $id): ?array
{
    global $transactions;

    $result = array_filter($transactions, function ($transaction) use ($id) {
        return $transaction["id"] === $id;
    });

    return $result ? array_values($result)[0] : null;
}


function daysSinceTransaction(string $date): int
{
    $transactionDate = new DateTime($date);
    $currentDate = new DateTime();

    $difference = $currentDate->diff($transactionDate);

    return $difference->days;
}


function addTransaction(int $id, string $date, float $amount, string $description, string $merchant): void
{
    global $transactions;

    $transactions[] = [
        "id" => $id,
        "date" => $date,
        "amount" => $amount,
        "description" => $description,
        "merchant" => $merchant
    ];
}


usort($transactions, function ($a, $b) {
    return strtotime($a["date"]) <=> strtotime($b["date"]);
});


usort($transactions, function ($a, $b) {
    return $b["amount"] <=> $a["amount"];
});

$totalAmount = calculateTotalAmount($transactions);

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Bank Transactions</title>
</head>

<body>

<h2>Список транзакций</h2>

<table border="1">

<thead>

<tr>
<th>ID</th>
<th>Date</th>
<th>Amount</th>
<th>Description</th>
<th>Merchant</th>
<th>Days since transaction</th>
</tr>

</thead>

<tbody>

<?php foreach ($transactions as $transaction): ?>

<tr>

<td><?= $transaction["id"] ?></td>
<td><?= $transaction["date"] ?></td>
<td><?= $transaction["amount"] ?></td>
<td><?= $transaction["description"] ?></td>
<td><?= $transaction["merchant"] ?></td>
<td><?= daysSinceTransaction($transaction["date"]) ?></td>

</tr>

<?php endforeach; ?>

</tbody>

<tfoot>

<tr>
<td colspan="2"><b>Total</b></td>
<td colspan="4"><?= $totalAmount ?></td>
</tr>

</tfoot>

</table>

</body>
</html>