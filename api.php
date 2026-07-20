
Api · PHP
<?php
/* ============================================
   Student Loan Management System - API Router
   Endpoints:
     GET  api.php                         -> list students
     GET  api.php?endpoint=loans&student_id=1  -> loans for a student
     GET  api.php?endpoint=loans          -> all loans (if no student_id)
     POST api.php?endpoint=loans          -> add loan
     GET  api.php?endpoint=payments&loan_id=1  -> payments for a loan
     POST api.php?endpoint=payments       -> add payment
   ============================================ */
 
header("Content-Type: application/json");
include "db.php";
 
$method   = $_SERVER['REQUEST_METHOD'];
$endpoint = $_GET['endpoint'] ?? 'students';
 
switch ($endpoint) {
 
    case "students":
        handleStudents($pdo, $method);
        break;
 
    case "loans":
        handleLoans($pdo, $method);
        break;
 
    case "payments":
        handlePayments($pdo, $method);
        break;
 
    default:
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Unknown endpoint: $endpoint"
        ]);
        break;
}
 
 
/* ============================================
   STUDENTS
   ============================================ */
function handleStudents($pdo, $method) {
 
    if ($method !== "GET") {
        http_response_code(405);
        echo json_encode([
            "success" => false,
            "message" => "Method not allowed for students endpoint."
        ]);
        return;
    }
 
    $stmt = $pdo->query("SELECT id, name, email, course FROM students");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
 
 
/* ============================================
   LOANS
   ============================================ */
function handleLoans($pdo, $method) {
 
    switch ($method) {
 
        case "GET":
            if (isset($_GET['student_id'])) {
                $stmt = $pdo->prepare(
                    "SELECT * FROM loans WHERE student_id = ?"
                );
                $stmt->execute([$_GET['student_id']]);
            } else {
                $stmt = $pdo->query("SELECT * FROM loans");
            }
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;
 
        case "POST":
            $data = json_decode(file_get_contents("php://input"), true);
 
            $error = validateLoanInput($data);
            if ($error) {
                http_response_code(400);
                echo json_encode(["success" => false, "message" => $error]);
                return;
            }
 
            $stmt = $pdo->prepare(
                "INSERT INTO loans
                (student_id, loan_amount, loan_type, status)
                VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([
                $data['student_id'],
                $data['loan_amount'],
                $data['loan_type'],
                $data['status']
            ]);
 
            echo json_encode([
                "success" => true,
                "message" => "Loan added successfully",
                "loan_id" => $pdo->lastInsertId()
            ]);
            break;
 
        default:
            http_response_code(405);
            echo json_encode([
                "success" => false,
                "message" => "Method not allowed for loans endpoint."
            ]);
    }
}
 
function validateLoanInput($data) {
    if (empty($data['student_id'])) {
        return "student_id is required.";
    }
    if (!isset($data['loan_amount']) || !is_numeric($data['loan_amount']) || $data['loan_amount'] <= 0) {
        return "loan_amount must be a positive number.";
    }
    if (empty($data['loan_type'])) {
        return "loan_type is required.";
    }
    if (empty($data['status'])) {
        return "status is required.";
    }
    return null;
}
 
 
/* ============================================
   PAYMENTS
   ============================================ */
function handlePayments($pdo, $method) {
 
    switch ($method) {
 
        case "GET":
            if (isset($_GET['loan_id'])) {
                $stmt = $pdo->prepare(
                    "SELECT * FROM payments WHERE loan_id = ?"
                );
                $stmt->execute([$_GET['loan_id']]);
            } else {
                $stmt = $pdo->query("SELECT * FROM payments");
            }
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;
 
        case "POST":
            $data = json_decode(file_get_contents("php://input"), true);
 
            $error = validatePaymentInput($data);
            if ($error) {
                http_response_code(400);
                echo json_encode(["success" => false, "message" => $error]);
                return;
            }
 
            $stmt = $pdo->prepare(
                "INSERT INTO payments
                (loan_id, payment_amount, payment_date, payment_method)
                VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([
                $data['loan_id'],
                $data['payment_amount'],
                $data['payment_date'],
                $data['payment_method']
            ]);
 
            echo json_encode([
                "success" => true,
                "message" => "Payment added successfully",
                "payment_id" => $pdo->lastInsertId()
            ]);
            break;
 
        default:
            http_response_code(405);
            echo json_encode([
                "success" => false,
                "message" => "Method not allowed for payments endpoint."
            ]);
    }
}
 
function validatePaymentInput($data) {
    if (empty($data['loan_id'])) {
        return "loan_id is required.";
    }
    if (!isset($data['payment_amount']) || !is_numeric($data['payment_amount']) || $data['payment_amount'] <= 0) {
        return "payment_amount must be a positive number.";
    }
    if (empty($data['payment_date'])) {
        return "payment_date is required.";
    }
    if (empty($data['payment_method'])) {
        return "payment_method is required.";
    }
    return null;
}
 
