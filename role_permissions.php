<?php
// role_permissions.php
require 'db.php';
ob_start();
include 'header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$role_id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM roles WHERE id = ?");
$stmt->execute([$role_id]);
$role = $stmt->fetch();

if (!$role) {
    header("Location: roles.php");
    exit();
}

// Handle Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Delete existing permissions for this role
    $pdo->prepare("DELETE FROM roles_permission WHERE role_id = ?")->execute([$role_id]);

    if (isset($_POST['permissions'])) {
        foreach ($_POST['permissions'] as $perm_id) {
            $pdo->prepare("INSERT INTO roles_permission (role_id, permission_id) VALUES (?, ?)")->execute([$role_id, $perm_id]);
        }
    }
    $success = "Permissions updated successfully!";
}

// Fetch all features with their permissions
$stmt = $pdo->query("SELECT features.name as feature_name, permissions.id as perm_id, permissions.name as perm_name 
                    FROM permissions 
                    JOIN features ON permissions.feature_id = features.id 
                    ORDER BY features.id, permissions.id");
$all_perms = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $all_perms[$row['feature_name']][] = $row;
}

// Fetch currently assigned permissions for this role
$stmt = $pdo->prepare("SELECT permission_id FROM roles_permission WHERE role_id = ?");
$stmt->execute([$role_id]);
$current_perms = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="row mt-4">
    <div class="col-md-12">
        <h2>Permissions for Role:
            <?php echo htmlspecialchars($role['name']); ?>
        </h2>

        <?php if (isset($success)): ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
        </div>
        <?php
endif; ?>

        <form method="POST" action="">
            <div class="card mb-4">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Feature</th>
                                <th>Permissions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_perms as $feature => $perms): ?>
                            <tr>
                                <td class="fw-bold text-capitalize">
                                    <?php echo $feature; ?>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap">
                                        <?php foreach ($perms as $p): ?>
                                        <div class="form-check me-4">
                                            <input class="form-check-input" type="checkbox" name="permissions[]"
                                                value="<?php echo $p['perm_id']; ?>"
                                                id="perm_<?php echo $p['perm_id']; ?>" <?php echo
                                                in_array($p['perm_id'], $current_perms) ? 'checked' : '' ; ?>
                                            <?php echo ($role['name'] === 'admin') ? 'disabled' : ''; // Admin permissions are locked ?>>
                                            <label class="form-check-label text-capitalize"
                                                for="perm_<?php echo $p['perm_id']; ?>">
                                                <?php echo $p['perm_name']; ?>
                                            </label>
                                        </div>
                                        <?php
    endforeach; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php
endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" <?php echo ($role['name']==='admin' ) ? 'disabled' : '' ; ?>>
                Save Permissions
            </button>
            <a href="roles.php" class="btn btn-secondary">Back to Roles</a>
        </form>
    </div>
</div>

<?php
include 'footer.php';
ob_end_flush();
?>