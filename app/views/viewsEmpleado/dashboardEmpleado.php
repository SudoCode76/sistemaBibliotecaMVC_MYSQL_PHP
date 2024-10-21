<?php
require_once __DIR__ . '/../../config/checkSessionEmpleado.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <header class="min-h-screen bg-base-100 text-base-content">
    <div class="container mx-auto p-4">
    <?php include "../viewsEmpleado/menuEmpleado.php"; ?>
    </div>
    </header>
</body>
</html>