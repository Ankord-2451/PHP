<?php
$day = date("N"); // 1 (Пн) - 7 (Вс)

// John Styles
if ($day == 1 || $day == 3 || $day == 5) {
    $johnSchedule = "8:00-12:00";
} else {
    $johnSchedule = "Нерабочий день";
}

// Jane Doe
if ($day == 2 || $day == 4 || $day == 6) {
    $janeSchedule = "12:00-16:00";
} else {
    $janeSchedule = "Нерабочий день";
}
?>

<table border="1">
    <tr>
        <th>№</th>
        <th>Фамилия Имя</th>
        <th>График работы</th>
    </tr>
    <tr>
        <td>1</td>
        <td>John Styles</td>
        <td><?php echo $johnSchedule; ?></td>
    </tr>
    <tr>
        <td>2</td>
        <td>Jane Doe</td>
        <td><?php echo $janeSchedule; ?></td>
    </tr>
</table>
