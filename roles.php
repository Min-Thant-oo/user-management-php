<?php
// roles.php
require 'db.php';
ob_start();
include 'header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM roles ORDER BY id ASC");
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Role Management</h2>
            <a href="role_create.php" class="btn btn-success btn-sm">Add New Role</a>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Role Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $role): ?>
                        <tr>
                            <td>
                                <?php echo $role['id']; ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($role['name']); ?>
                            </td>
                            <td>
                                <a href="role_permissions.php?id=<?php echo $role['id']; ?>"
                                    class="btn btn-sm btn-outline-info me-1">Permissions</a>
                                <?php if ($role['name'] !== 'admin'): ?>
                                <a href="role_delete.php?id=<?php echo $role['id']; ?>"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Delete role: <?php echo htmlspecialchars($role['name']); ?>?');">Delete</a>
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
        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</div>

<?php
include 'footer.php';
ob_end_flush();
?>