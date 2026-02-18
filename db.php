<?php
// db.php
// Connect to SQLite database
try {
    // Create (connect to) SQLite database in file
    $pdo = new PDO('sqlite:database.sqlite');
    // Set errormode to exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create features table
    $pdo->exec("CREATE TABLE IF NOT EXISTS features (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL UNIQUE
              )");

    // Create permissions table
    $pdo->exec("CREATE TABLE IF NOT EXISTS permissions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                feature_id INTEGER,
                FOREIGN KEY(feature_id) REFERENCES features(id)
              )");

    // Create roles table
    $pdo->exec("CREATE TABLE IF NOT EXISTS roles (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL UNIQUE
              )");

    // Create roles_permission table
    $pdo->exec("CREATE TABLE IF NOT EXISTS roles_permission (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                role_id INTEGER,
                permission_id INTEGER,
                FOREIGN KEY(role_id) REFERENCES roles(id),
                FOREIGN KEY(permission_id) REFERENCES permissions(id)
              )");

    // Create users table if not exists (Updated with role_id)
    $query = "CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                role_id INTEGER,
                FOREIGN KEY(role_id) REFERENCES roles(id)
              )";
    $pdo->exec($query);

    // SEED DATA
    // 1. Features
    $stmt = $pdo->query("SELECT COUNT(*) FROM features");
    if ($stmt->fetchColumn() == 0) {
        $features = ['user', 'roles', 'product'];
        foreach ($features as $f) {
            $pdo->prepare("INSERT INTO features (name) VALUES (?)")->execute([$f]);
        }

        // 2. Permissions for each feature
        $perms = ['create', 'read', 'update', 'delete'];
        $stmt_f = $pdo->query("SELECT id FROM features");
        while ($f = $stmt_f->fetch()) {
            foreach ($perms as $p) {
                $pdo->prepare("INSERT INTO permissions (name, feature_id) VALUES (?, ?)")->execute([$p, $f['id']]);
            }
        }
    }

    // 3. Roles
    $stmt = $pdo->query("SELECT COUNT(*) FROM roles");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO roles (name) VALUES ('admin')"); // ID 1
        $pdo->exec("INSERT INTO roles (name) VALUES ('operator')"); // ID 2

        // Give admin all permissions
        $all_perms = $pdo->query("SELECT id FROM permissions")->fetchAll();
        foreach ($all_perms as $p) {
            $pdo->prepare("INSERT INTO roles_permission (role_id, permission_id) VALUES (1, ?)")->execute([$p['id']]);
        }
    }

    // 4. Default Admin User
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $password = password_hash('admin', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role_id) VALUES ('admin', :pass, 1)");
        $stmt->execute([':pass' => $password]);
    }

}
catch (PDOException $e) {
    // Print error message
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>
