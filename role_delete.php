<?php
// role_delete.php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prevent deleting admin role (ID 1)
    if ($id == 1) {
        header("Location: roles.php?error=Cannot delete admin role");
        exit();
    }

    // Optional: Check if role is in use
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role_id = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        header("Location: roles.php?error=Role is in use and cannot be deleted");
        exit();
    }

    // Delete role permissions first
    $pdo->prepare("DELETE FROM roles_permission WHERE role_id = ?")->execute([$id]);

    // Delete role
    $stmt = $pdo->prepare("DELETE FROM roles WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: roles.php");
exit();
?>