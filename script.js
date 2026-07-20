const API = "api.php";

let currentStudent = null;
let currentLoan = null;
let currentLoanAmount = 0;


// ==========================
// SMALL HELPER: escape text before inserting into HTML
// ==========================

function escapeHTML(value) {
    if (value === null || value === undefined) return "";
    return String(value)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}


// ==========================
// LOAD STUDENTS
// ==========================

async function loadStudents() {

    try {

        let response = await fetch(API);
        let result = await response.json();

        if (!response.ok) {
            alert(result.message || "Failed to load students.");
            return;
        }

        let students = result;
        let table = document.getElementById("studentTable");

        table.innerHTML = "";

        students.forEach(student => {

            table.innerHTML += `
            <tr>
                <td>${escapeHTML(student.id)}</td>
                <td>${escapeHTML(student.name)}</td>
                <td>${escapeHTML(student.email)}</td>
                <td>${escapeHTML(student.course)}</td>
                <td>
                    <button
                    class="btn-loans"
                    onclick="openLoans(${student.id})">
                    Loans
                    </button>
                </td>
            </tr>
            `;
        });

    } catch(error) {
        console.error("Student loading error:", error);
        alert("Cannot connect to backend API");
    }
}


loadStudents();


// ==========================
// OPEN LOANS WORKSPACE
// ==========================

function openLoans(studentID) {

    currentStudent = studentID;
    currentLoan = null;
    currentLoanAmount = 0;

    document.getElementById("loanSection").style.display = "block";
    document.getElementById("paymentSection").style.display = "none";

    // clear any stale payment data from a previously viewed loan
    document.getElementById("paymentTable").innerHTML = "";
    document.getElementById("totalPaid").innerHTML = "0";
    document.getElementById("remaining").innerHTML = "0";

    loadLoans(studentID);
}


// ==========================
// GET LOANS BY STUDENT
// ==========================

async function loadLoans(studentID) {

    try {

        // filter server-side instead of downloading every loan in the table
        let response = await fetch(
            `${API}?endpoint=loans&student_id=${studentID}`
        );

        let loans = await response.json();

        if (!response.ok) {
            alert(loans.message || "Failed to load loans.");
            return;
        }

        let table = document.getElementById("loanTable");
        table.innerHTML = "";

        loans.forEach(loan => {

            table.innerHTML += `
            <tr>
                <td>₱${escapeHTML(loan.loan_amount)}</td>
                <td>${escapeHTML(loan.loan_type)}</td>
                <td>${escapeHTML(loan.status)}</td>
                <td>
                <button
                class="btn-payment"
                onclick="openPayments(${loan.loan_id}, ${loan.loan_amount})">
                Payments
                </button>
                </td>
            </tr>
            `;
        });

    } catch(error) {
        console.error("Loan loading error:", error);
        alert("Cannot load loans for this student.");
    }
}


// ==========================
// ADD LOAN
// ==========================

document.getElementById("loanForm").addEventListener("submit", async function(e) {

    e.preventDefault();

    let loanData = {
        student_id: currentStudent,
        loan_amount: document.getElementById("loanAmount").value,
        loan_type: document.getElementById("loanType").value,
        status: document.getElementById("loanStatus").value
    };

    try {

        let response = await fetch(`${API}?endpoint=loans`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(loanData)
        });

        let result = await response.json();

        alert(result.message);

        if (!response.ok || result.success === false) {
            return; // don't reset the form or refresh on a failed submit
        }

        e.target.reset();
        loadLoans(currentStudent);

    } catch(error) {
        console.error("Add loan error:", error);
        alert("Could not add loan. Check your connection and try again.");
    }
});


// ==========================
// OPEN PAYMENT WORKSPACE
// ==========================

function openPayments(loanID, amount) {

    currentLoan = loanID;
    currentLoanAmount = Number(amount) || 0;

    document.getElementById("paymentSection").style.display = "block";

    loadPayments(loanID, currentLoanAmount);
}


// ==========================
// GET PAYMENTS
// ==========================

async function loadPayments(loanID, loanAmount) {

    try {

        let response = await fetch(
            `${API}?endpoint=payments&loan_id=${loanID}`
        );

        let payments = await response.json();

        if (!response.ok) {
            alert(payments.message || "Failed to load payments.");
            return;
        }

        let table = document.getElementById("paymentTable");
        table.innerHTML = "";

        let totalPaidAmount = 0;

        payments.forEach(payment => {

            // guard against null/missing amounts poisoning the total with NaN
            totalPaidAmount += Number(payment.payment_amount) || 0;

            table.innerHTML += `
            <tr>
                <td>₱${escapeHTML(payment.payment_amount)}</td>
                <td>${escapeHTML(payment.payment_date)}</td>
                <td>${escapeHTML(payment.payment_method)}</td>
            </tr>
            `;
        });

        document.getElementById("totalPaid").innerHTML = totalPaidAmount;
        document.getElementById("remaining").innerHTML = loanAmount - totalPaidAmount;

    } catch(error) {
        console.error("Payment loading error:", error);
        alert("Cannot load payments for this loan.");
    }
}


// ==========================
// ADD PAYMENT
// ==========================

document.getElementById("paymentForm").addEventListener("submit", async function(e) {

    e.preventDefault();

    let paymentData = {
        loan_id: currentLoan,
        payment_amount: document.getElementById("paymentAmount").value,
        payment_date: document.getElementById("paymentDate").value,
        payment_method: document.getElementById("paymentMethod").value
    };

    try {

        let response = await fetch(`${API}?endpoint=payments`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(paymentData)
        });

        let result = await response.json();

        alert(result.message);

        if (!response.ok || result.success === false) {
            return;
        }

        e.target.reset();
        loadPayments(currentLoan, currentLoanAmount);

    } catch(error) {
        console.error("Add payment error:", error);
        alert("Could not add payment. Check your connection and try again.");
    }
});
