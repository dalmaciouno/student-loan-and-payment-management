<?php

include "db.php";


$method = $_SERVER['REQUEST_METHOD'];


switch($method){


case "GET":

    $stmt = $pdo->query(
        "SELECT * FROM payments"
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
"message"=>"Payment added successfully"
]);


break;


}

?>