<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

include "db.php";

/* Loan and Payment API */

if (isset($_GET['endpoint'])) {

    if ($_GET['endpoint'] == "loans") {
        require "loans.php";
        exit;
    }

    if ($_GET['endpoint'] == "payments") {
        require "payments.php";
        exit;
    }
}

/* Student CRUD */

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    /* ==========================
       GET ALL STUDENTS
    ========================== */

    case "GET":

        $stmt = $pdo->query(
            "SELECT * FROM students ORDER BY id DESC"
        );

        echo json_encode(
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );

    break;


    /* ==========================
       ADD STUDENT
    ========================== */

    case "POST":

        $data = json_decode(
            file_get_contents("php://input"),
            true
        );

        if (
            empty($data['name']) ||
            empty($data['email']) ||
            empty($data['course'])
        ) {

            http_response_code(400);

            echo json_encode([
                "success" => false,
                "message" => "All fields are required."
            ]);

            exit;
        }

        $stmt = $pdo->prepare(
            "INSERT INTO students(name,email,course)
             VALUES(?,?,?)"
        );

        $stmt->execute([

            $data['name'],
            $data['email'],
            $data['course']

        ]);

        echo json_encode([
            "success" => true,
            "message" => "Student added successfully."
        ]);

    break;


    /* ==========================
       UPDATE STUDENT
    ========================== */

    case "PUT":

        $data = json_decode(
            file_get_contents("php://input"),
            true
        );

        if (
            empty($data['id']) ||
            empty($data['name']) ||
            empty($data['email']) ||
            empty($data['course'])
        ) {

            http_response_code(400);

            echo json_encode([
                "success" => false,
                "message" => "Invalid data."
            ]);

            exit;
        }

        $stmt = $pdo->prepare(
            "UPDATE students
             SET
             name=?,
             email=?,
             course=?
             WHERE id=?"
        );

        $stmt->execute([

            $data['name'],
            $data['email'],
            $data['course'],
            $data['id']

        ]);

        echo json_encode([
            "success" => true,
            "message" => "Student updated successfully."
        ]);

    break;


    /* ==========================
       DELETE STUDENT
    ========================== */

    case "DELETE":

        $data = json_decode(
            file_get_contents("php://input"),
            true
        );

        if (empty($data['id'])) {

            http_response_code(400);

            echo json_encode([
                "success" => false,
                "message" => "Student ID is required."
            ]);

            exit;
        }

        $stmt = $pdo->prepare(
            "DELETE FROM students
             WHERE id=?"
        );

        $stmt->execute([
            $data['id']
        ]);

        echo json_encode([
            "success" => true,
            "message" => "Student deleted successfully."
        ]);

    break;


    default:

        http_response_code(405);

        echo json_encode([
            "success" => false,
            "message" => "Method not allowed."
        ]);

    break;
}

?>
