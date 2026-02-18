<?php
// index.php
require 'db.php';
// Include header last because it starts session and output
ob_start(); // Buffer output to prevent header issues if any logic comes before
include 'header.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$isAdmin = ($_SESSION['role'] === 'admin');

// Fetch users if admin
$users = [];
if ($isAdmin) {
    $stmt = $pdo->query("SELECT users.*, roles.name as role_name FROM users LEFT JOIN roles ON users.role_id = roles.id ORDER BY users.id DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Dashboard</h2>
            <?php if ($isAdmin): ?>
            <a href="roles.php" class="btn btn-outline-primary">Manage Roles</a>
            <?php
endif; ?>
        </div>
        <p class="lead">Hello,
            <?php echo htmlspecialchars($_SESSION['username']); ?>!
        </p>

        <?php if ($isAdmin): ?>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">User Management</h5>
                <a href="create.php" class="btn btn-success btn-sm">Add New User</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <?php echo $user['id']; ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($user['username']); ?>
                            </td>
                            <td>
                                <span
                                    class="badge bg-<?php echo (strtolower($user['role_name'] ?? '') === 'admin') ? 'danger' : 'primary'; ?>">
                                    <?php echo htmlspecialchars($user['role_name'] ?? 'Unknown'); ?>
                                </span>
                            </td>
                            <td class="text-nowrap">
                                <a href="edit.php?id=<?php echo $user['id']; ?>"
                                    class="btn btn-sm btn-outline-warning me-1">Edit</a>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <a href="delete.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Are you sure you want to delete <?php echo htmlspecialchars($user['username']); ?>?');">Delete</a>
                                <?php
        else: ?>
                                <button class="btn btn-sm btn-outline-secondary" disabled>Delete</button>
                                <?php
        endif; ?>
                            </td>
                        </tr>
                        <?php
    endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
else: ?>
        <div class="alert alert-info">
            You are logged in as a <strong>Normal User</strong>.
            You do not have permission to manage other users.
        </div>
        <?php
endif; ?>
    </div>
</div>

<?php
include 'footer.php';
ob_end_flush();
?>