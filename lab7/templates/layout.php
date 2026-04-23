<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($pageTitle ?? 'Рецепты') ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include 'list.php'; ?>
</body>
</html>
