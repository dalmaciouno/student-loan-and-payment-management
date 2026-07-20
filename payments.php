<?php
require_once 'config/db_connect.php';

$loan_id = isset($_GET['loan_id']) ? (int) $_GET['loan_id'] : 0;
$message = '';
$messageType = '';

if ($loan_id <= 0) {
    die("Invalid loan.");
}

// Fetch loan + student info
$stmt = $pdo->prepare("
    SELECT loans.*, students.name AS student_name
    FROM loans
    JOIN students ON students.id = loans.student_id
    WHERE loans.id = ?
");
$stmt->execute([$loan_id]);
$loan = $stmt->fetch();

if (!$loan) {
    die("Loan not found.");
}

// Handle new payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_payment'])) {
    $amount   = trim($_POST['payment_amount'] ?? '');
    $date     = $_POST['payment_date'] ?? '';
    $method   = $_POST['payment_method'] ?? '';

    $validMethods = ['Cash', 'Bank Transfer', 'Online Payment'];

    if ($amount === '' || !is_numeric($amount) || $amount <= 0) {
        $message = "Please enter a valid payment amount.";
        $messageType = 'error';
    } elseif ($date === '') {
        $message = "Please select a payment date.";
        $messageType = 'error';
    } elseif (!in_array($method, $validMethods, true)) {
        $message = "Please select a valid payment method.";
        $messageType = 'error';
    } else {
        $insert = $pdo->prepare("INSERT INTO payments (loan_id, payment_amount, payment_date, payment_method) VALUES (?, ?, ?, ?)");
        $insert->execute([$loan_id, $amount, $date, $method]);
        $message = "Payment recorded successfully.";
        $messageType = 'success';
    }
}

// Fetch payments belonging ONLY to this loan
$payStmt = $pdo->prepare("SELECT * FROM payments WHERE loan_id = ? ORDER BY payment_date DESC");
$payStmt->execute([$loan_id]);
$payments = $payStmt->fetchAll();

// Compute Total Paid and Remaining Balance
$totalPaid = 0;
foreach ($payments as $p) {
    $totalPaid += (float) $p['payment_amount'];
}
$remainingBalance = (float) $loan['amount'] - $totalPaid;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payments - Loan #<?= $loan['id'] ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="breadcrumb">
        <a href="students_index.php">Students</a> &rarr;
        <a href="loans.php?student_id=<?= $loan['student_id'] ?>"><?= htmlspecialchars($loan['student_name']) ?>'s Loans</a> &rarr;
        Payments
    </div>

    <h1>Payment Workspace: Loan #<?= $loan['id'] ?> (<?= htmlspecialchars($loan['loan_type']) ?>)</h1>
    <p>Student: <strong><?= htmlspecialchars($loan['student_name']) ?></strong> &nbsp;|&nbsp;
       Loan Amount: <strong>&#8369;<?= number_format($loan['amount'], 2) ?></strong> &nbsp;|&nbsp;
       Status: <strong><?= htmlspecialchars($loan['status']) ?></strong></p>

    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="summary-box">
        <div class="summary-card">
            <div class="label">Loan Amount</div>
            <div class="value">&#8369;<?= number_format($loan['amount'], 2) ?></div>
        </div>
        <div class="summary-card">
            <div class="label">Total Paid</div>
            <div class="value">&#8369;<?= number_format($totalPaid, 2) ?></div>
        </div>
        <div class="summary-card">
            <div class="label">Remaining Balance</div>
            <div class="value">&#8369;<?= number_format($remainingBalance, 2) ?></div>
        </div>
    </div>

    <h2>Record New Payment</h2>
    <form class="inline-form" method="POST" action="payments.php?loan_id=<?= $loan_id ?>">
        <div>
            <label for="payment_amount">Payment Amount (PHP)</label>
            <input type="number" step="0.01" min="0.01" name="payment_amount" id="payment_amount" required>
        </div>
        <div>
            <label for="payment_date">Payment Date</label>
            <input type="date" name="payment_date" id="payment_date" required>
        </div>
        <div>
            <label for="payment_method">Payment Method</label>
            <select name="payment_method" id="payment_method" required>
                <option value="">-- Select --</option>
                <option value="Cash">Cash</option>
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="Online Payment">Online Payment</option>
            </select>
        </div>
        <div class="form-actions">
            <button type="submit" name="add_payment" class="btn btn-success">Save Payment</button>
        </div>
    </form>

    <h2>Payment History</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Method</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($payments) === 0): ?>
            <tr><td colspan="4">No payments recorded yet for this loan.</td></tr>
        <?php else: ?>
            <?php foreach ($payments as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td>&#8369;<?= number_format($p['payment_amount'], 2) ?></td>
                    <td><?= htmlspecialchars($p['payment_date']) ?></td>
                    <td><?= htmlspecialchars($p['payment_method']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
