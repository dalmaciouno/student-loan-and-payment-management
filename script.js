const API = "http://localhost:8082/api.php";

let currentStudent = null;

let currentLoan = null;

let currentLoanAmount = 0;

// LOAD STUDENTS

async function loadStudents() {
  let response = await fetch(API);

  let students = await response.json();

  let table = document.getElementById("studentTable");

  table.innerHTML = "";

  students.forEach((student) => {
    table.innerHTML += `


<tr>

<td>${student.id}</td>

<td>${student.name}</td>

<td>${student.email}</td>

<td>${student.course}</td>


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
}

loadStudents();

// OPEN LOANS

function openLoans(id) {
  currentStudent = id;

  document.getElementById("loanSection").style.display = "block";

  document.getElementById("paymentSection").style.display = "none";

  loadLoans(id);
}

// GET LOANS

async function loadLoans(id) {
  let response = await fetch(`${API}?endpoint=loans`);

  let loans = await response.json();

  let table = document.getElementById("loanTable");

  table.innerHTML = "";

  loans
    .filter((loan) => loan.student_id == id)

    .forEach((loan) => {
      table.innerHTML += `


<tr>

<td>
₱${loan.loan_amount}
</td>

<td>
${loan.loan_type}
</td>

<td>
${loan.status}
</td>


<td>

<button

class="btn-payment"

onclick="openPayments(${loan.loan_id},${loan.loan_amount})">

Payments

</button>


</td>


</tr>


`;
    });
}

// ADD LOAN

document.getElementById("loanForm").addEventListener(
  "submit",

  async function (e) {
    e.preventDefault();

    let data = {
      student_id: currentStudent,

      loan_amount: document.getElementById("loanAmount").value,

      loan_type: document.getElementById("loanType").value,

      status: document.getElementById("loanStatus").value,
    };

    await fetch(
      `${API}?endpoint=loans`,

      {
        method: "POST",

        headers: {
          "Content-Type": "application/json",
        },

        body: JSON.stringify(data),
      }
    );

    alert("Loan Added");

    loadLoans(currentStudent);
  }
);

// PAYMENTS

function openPayments(id, amount) {
  currentLoan = id;

  currentLoanAmount = amount;

  document.getElementById("paymentSection").style.display = "block";

  loadPayments(id, amount);
}

async function loadPayments(id, amount) {
  let response = await fetch(`${API}?endpoint=payments&loan_id=${id}`);

  let payments = await response.json();

  let table = document.getElementById("paymentTable");

  table.innerHTML = "";

  let total = 0;

  payments.forEach((payment) => {
    total += Number(payment.payment_amount);

    table.innerHTML += `


<tr>

<td>
₱${payment.payment_amount}
</td>


<td>
${payment.payment_date}
</td>


<td>
${payment.payment_method}
</td>


</tr>


`;
  });

  totalPaid.innerHTML = total;

  remaining.innerHTML = amount - total;
}

// ADD PAYMENT

document.getElementById("paymentForm").addEventListener(
  "submit",

  async function (e) {
    e.preventDefault();

    let data = {
      loan_id: currentLoan,

      payment_amount: paymentAmount.value,

      payment_date: paymentDate.value,

      payment_method: paymentMethod.value,
    };

    await fetch(
      `${API}?endpoint=payments`,

      {
        method: "POST",

        headers: {
          "Content-Type": "application/json",
        },

        body: JSON.stringify(data),
      }
    );

    alert("Payment Added");

    loadPayments(currentLoan, currentLoanAmount);
  }
);
