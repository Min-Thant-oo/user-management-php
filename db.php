<?php
// db.php
// Connect to SQLite database
try {
    // Create (connect to) SQLite database in file
    $pdo = new PDO('sqlite:database.sqlite');
    // Set errormode to exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create users table if not exists
    $query = "CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                role TEXT NOT NULL DEFAULT 'user'
              )";
    $pdo->exec($query);

    // Check if admin exists, if not create one
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        // Default admin: username=admin, password=admin
        $password = password_hash('admin', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES ('admin', :pass, 'admin')");
        $stmt->execute([':pass' => $password]);
    }

}
catch (PDOException $e) {
    // Print error message
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>
