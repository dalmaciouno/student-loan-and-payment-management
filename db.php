<?php
/**
 * Shared database connection.
 * Adjust host/dbname/user/pass to match your existing app's config.
 * If you already have a db connection file, just replace the include
 * path in the other files to point to yours instead of this one.
 */

$host   = "localhost";
$dbname = "student_system"; // <-- change to your actual DB name
$user   = "root";           // <-- change to your actual DB user
$pass   = "";                // <-- change to your actual DB password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
