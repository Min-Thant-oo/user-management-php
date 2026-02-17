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
    $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="row mt-4">
    <div class="col-md-12">
        <h2>Dashboard</h2>
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
                                    class="badge bg-<?php echo ($user['role'] === 'admin') ? 'danger' : 'primary'; ?>">
                                    <?php echo htmlspecialchars($user['role']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit.php?id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <!-- Prevent deleting self -->
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <a href="delete.php?id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                <?php
        else: ?>
                                <button class="btn btn-secondary btn-sm" disabled>Delete</button>
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