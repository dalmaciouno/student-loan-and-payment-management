<?php
require_once 'config/db_connect.php';

$student_id = isset($_GET['student_id']) ? (int) $_GET['student_id'] : 0;
$message = '';
$messageType = '';

if ($student_id <= 0) {
    die("Invalid student.");
}

// Fetch student info
$stmt = $pdo->prepare("SELECT id, name FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    die("Student not found.");
}

// Handle new loan submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_loan'])) {
    $amount   = trim($_POST['amount'] ?? '');
    $loanType = $_POST['loan_type'] ?? '';
    $status   = $_POST['status'] ?? '';

    $validTypes   = ['Tuition', 'Books', 'Living Expenses'];
    $validStatus  = ['Pending', 'Approved', 'Disbursed'];

    if ($amount === '' || !is_numeric($amount) || $amount <= 0) {
        $message = "Please enter a valid loan amount.";
        $messageType = 'error';
    } elseif (!in_array($loanType, $validTypes, true)) {
        $message = "Please select a valid loan type.";
        $messageType = 'error';
    } elseif (!in_array($status, $validStatus, true)) {
        $message = "Please select a valid status.";
        $messageType = 'error';
    } else {
        $insert = $pdo->prepare("INSERT INTO loans (student_id, amount, loan_type, status) VALUES (?, ?, ?, ?)");
        $insert->execute([$student_id, $amount, $loanType, $status]);
        $message = "Loan added successfully.";
        $messageType = 'success';
    }
}

// Fetch loans belonging ONLY to this student
$loanStmt = $pdo->prepare("SELECT * FROM loans WHERE student_id = ? ORDER BY created_at DESC");
$loanStmt->execute([$student_id]);
$loans = $loanStmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Loans - <?= htmlspecialchars($student['name']) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="breadcrumb">
        <a href="students_index.php">&larr; Back to Students</a>
    </div>

    <h1>Loan Workspace: <?= htmlspecialchars($student['name']) ?></h1>

    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <h2>Add New Loan</h2>
    <form class="inline-form" method="POST" action="loans.php?student_id=<?= $student_id ?>">
        <div>
            <label for="amount">Loan Amount (PHP)</label>
            <input type="number" step="0.01" min="0.01" name="amount" id="amount" required>
        </div>
        <div>
            <label for="loan_type">Loan Type</label>
            <select name="loan_type" id="loan_type" required>
                <option value="">-- Select --</option>
                <option value="Tuition">Tuition</option>
                <option value="Books">Books</option>
                <option value="Living Expenses">Living Expenses</option>
            </select>
        </div>
        <div>
            <label for="status">Status</label>
            <select name="status" id="status" required>
                <option value="">-- Select --</option>
                <option value="Pending">Pending</option>
                <option value="Approved">Approved</option>
                <option value="Disbursed">Disbursed</option>
            </select>
        </div>
        <div class="form-actions">
            <button type="submit" name="add_loan" class="btn btn-success">Save Loan</button>
        </div>
    </form>

    <h2>Existing Loans</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Amount</th>
                <th>Loan Type</th>
                <th>Status</th>
                <th>Date Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($loans) === 0): ?>
            <tr><td colspan="6">No loans recorded yet for this student.</td></tr>
        <?php else: ?>
            <?php foreach ($loans as $loan): ?>
                <tr>
                    <td><?= $loan['id'] ?></td>
                    <td>&#8369;<?= number_format($loan['amount'], 2) ?></td>
                    <td><?= htmlspecialchars($loan['loan_type']) ?></td>
                    <td><?= htmlspecialchars($loan['status']) ?></td>
                    <td><?= htmlspecialchars($loan['created_at']) ?></td>
                    <td>
                        <a class="btn btn-primary" href="payments.php?loan_id=<?= $loan['id'] ?>">Payments</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
