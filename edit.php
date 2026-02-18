<?php
// edit.php
require 'db.php';
ob_start();
include 'header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<div class='alert alert-danger'>User not found.</div>";
    include 'footer.php';
    exit();
}

$roles = $pdo->query("SELECT * FROM roles ORDER BY name ASC")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $role_id = $_POST['role_id'];
    $password = $_POST['password'];

    if (empty($username)) {
        $error = "Username cannot be empty.";
    }
    else {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET username = ?, password = ?, role_id = ? WHERE id = ?");
            $result = $stmt->execute([$username, $hashed_password, $role_id, $id]);
        }
        else {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, role_id = ? WHERE id = ?");
            $result = $stmt->execute([$username, $role_id, $id]);
        }

        if ($result) {
            $success = "User updated successfully! <a href='index.php'>Go back</a>";
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        else {
            $error = "Something went wrong.";
        }
    }
}
?>

<div class="row mt-4 justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Edit User</h4>
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
                        <input type="text" name="username" class="form-control"
                            value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control"
                            placeholder="Leave blank to keep current password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role_id" class="form-select">
                            <?php foreach ($roles as $role): ?>
                            <option value="<?php echo $role['id']; ?>" <?php echo ($user['role_id']==$role['id'])
                                ? 'selected' : '' ; ?>>
                                <?php echo htmlspecialchars($role['name']); ?>
                            </option>
                            <?php
endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update User</button>
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