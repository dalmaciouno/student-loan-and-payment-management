<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "school_db";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$database",
        $user,
        $password
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch(PDOException $e){

    echo json_encode([
        "error" => $e->getMessage()
    ]);

    exit;
}

?>