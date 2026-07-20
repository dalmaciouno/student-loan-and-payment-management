<?php

include "db.php";


$method = $_SERVER['REQUEST_METHOD'];


switch($method){


case "GET":

    $stmt = $pdo->query(
        "SELECT * FROM loans"
    );

    echo json_encode(
        $stmt->fetchAll(PDO::FETCH_ASSOC)
    );

break;



case "POST":

    $data = json_decode(
        file_get_contents("php://input"),
        true
    );


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
        "message"=>"Loan added successfully"
    ]);

break;


}

?>