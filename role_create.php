<?php
// role_create.php
require 'db.php';
ob_start();
include 'header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    if (empty($name)) {
        $error = "Role name is required.";
    }
    else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM roles WHERE name = ?");
        $stmt->execute([$name]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Role already exists.";
        }
        else {
            $stmt = $pdo->prepare("INSERT INTO roles (name) VALUES (?)");
            if ($stmt->execute([$name])) {
                $role_id = $pdo->lastInsertId();
                header("Location: role_permissions.php?id=" . $role_id);
                exit();
            }
            else {
                $error = "Failed to create role.";
            }
        }
    }
}
?>

<div class="row mt-4 justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Create New Role</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
                <?php
endif; ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Manager" required
                            autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Role & Setup Permissions</button>
                    <a href="roles.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include 'footer.php';
ob_end_flush();
?>