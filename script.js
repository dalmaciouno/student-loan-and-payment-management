console.log("script.js is running");
const API = "http://localhost:8082/api.php";

let currentStudent = null;
let currentLoan = null;
let currentLoanAmount = 0;


// ==========================
// LOAD STUDENTS
// ==========================

async function loadStudents() {

    try {

        let response = await fetch(API);

        let students = await response.json();

        let table = document.getElementById("studentTable");

        table.innerHTML = "";


        students.forEach(student => {

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


    document.getElementById("loanSection")
    .style.display = "block";


    document.getElementById("paymentSection")
    .style.display = "none";


    loadLoans(studentID);

}






// ==========================
// GET LOANS BY STUDENT
// ==========================

async function loadLoans(studentID) {


    try {


        let response = await fetch(
            `${API}?endpoint=loans`
        );


        let loans = await response.json();


        let table =
        document.getElementById("loanTable");


        table.innerHTML = "";



        loans
        .filter(loan => loan.student_id == studentID)

        .forEach(loan => {


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


    } catch(error) {

        console.error("Loan loading error:", error);

    }


}






// ==========================
// ADD LOAN
// ==========================


document
.getElementById("loanForm")
.addEventListener(
"submit",

async function(e){


    e.preventDefault();



    let loanData = {


        student_id: currentStudent,


        loan_amount:
        document.getElementById("loanAmount").value,


        loan_type:
        document.getElementById("loanType").value,


        status:
        document.getElementById("loanStatus").value


    };



    try {


        let response = await fetch(

            `${API}?endpoint=loans`,

            {

                method:"POST",

                headers:{

                    "Content-Type":"application/json"

                },

                body:
                JSON.stringify(loanData)

            }

        );


        let result =
        await response.json();


        alert(result.message);


        loadLoans(currentStudent);



    }

    catch(error){

        console.error("Add loan error:",error);

    }


});








// ==========================
// OPEN PAYMENT WORKSPACE
// ==========================


function openPayments(loanID, amount){


    currentLoan = loanID;

    currentLoanAmount = amount;



    document
    .getElementById("paymentSection")
    .style.display = "block";



    loadPayments(
        loanID,
        amount
    );


}







// ==========================
// GET PAYMENTS
// ==========================


async function loadPayments(
loanID,
loanAmount
){


    try{


        let response =
        await fetch(

        `${API}?endpoint=payments&loan_id=${loanID}`

        );


        let payments =
        await response.json();



        let table =
        document.getElementById("paymentTable");


        table.innerHTML="";



        let totalPaidAmount = 0;



        payments.forEach(payment=>{


            totalPaidAmount +=
            Number(payment.payment_amount);



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



        document.getElementById("totalPaid")
        .innerHTML = totalPaidAmount;



        document.getElementById("remaining")
        .innerHTML =
        loanAmount - totalPaidAmount;



    }


    catch(error){

        console.error(
        "Payment loading error:",
        error
        );

    }


}







// ==========================
// ADD PAYMENT
// ==========================


document
.getElementById("paymentForm")
.addEventListener(
"submit",

async function(e){


    e.preventDefault();



    let paymentData = {


        loan_id:
        currentLoan,


        payment_amount:
        document.getElementById("paymentAmount").value,


        payment_date:
        document.getElementById("paymentDate").value,


        payment_method:
        document.getElementById("paymentMethod").value


    };



    try{


        let response =
        await fetch(

        `${API}?endpoint=payments`,

        {

            method:"POST",

            headers:{

                "Content-Type":
                "application/json"

            },

            body:
            JSON.stringify(paymentData)

        }

        );



        let result =
        await response.json();



        alert(result.message);



        loadPayments(
            currentLoan,
            currentLoanAmount
        );


    }


    catch(error){

        console.error(
        "Add payment error:",
        error
        );

    }


});
