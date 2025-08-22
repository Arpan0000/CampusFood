<?php
require_once "db_connect.php"; // gives $conn (MySQLi)

// Check if POST data exists
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['user'] ?? '';
    $email    = $_POST['mail'] ?? '';
    $password = $_POST['passwd'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    $role1    = $_POST['role1'] ?? '';
    $subrole   = $_POST['subrole'] ?? '';

    // Check password match
    if ($password !== $confirm) {
        die("❌ Passwords do not match!");
    }


    // Insert query
    $sql = "INSERT INTO users (username, email, password, role,subrole) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("❌ Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssss", $username, $email, $password, $role1, $subrole);

    if ($stmt->execute()) {
        echo "✅ Signup successful! <a href='../login.html'>Login here</a>";
    } else {
        echo "❌ Insert failed: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "No POST data received!";
}
?>
