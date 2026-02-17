<?php
// create.php
require 'db.php';
// Start buffering specific to this page if needed, but header.php starts session
ob_start();
include 'header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    }
    else {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Username already exists.";
        }
        else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $hashed_password, $role])) {
                $success = "User created successfully! <a href='index.php'>Go back</a>";
            }
            else {
                $error = "Something went wrong.";
            }
        }
    }
}
?>

<div class="row mt-4 justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Create New User</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
                <?php
endif; ?>
                <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
                <?php
endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Create User</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include 'footer.php';
ob_end_flush();
?>