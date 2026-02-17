<?php
// header.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">User Management</a>
            <div class="d-flex">
                <?php if (isset($_SESSION['user_id'])): ?>
                <span class="navbar-text me-3">
                    Welcome,
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                    (
                    <?php echo htmlspecialchars($_SESSION['role']); ?>)
                </span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
                <?php
endif; ?>
            </div>
        </div>
    </nav>
    <div class="container">