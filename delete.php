<?php
// delete.php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prevent deleting self
    if ($id == $_SESSION['user_id']) {
        header("Location: index.php?error=Cannot delete yourself");
        exit();
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php");
exit();
?>