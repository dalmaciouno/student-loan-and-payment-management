# Student Loan and Payment Management System

## Overview

The **Student Loan and Payment Management System** is an extension of the Student CRUD application that allows administrators to manage student loans and record loan payments.
The system uses a relational database where each student can have multiple loans, and each loan can have multiple payment records.

## Features

### Student Management

* Add new students
* Edit student information
* Delete student records
* View the list of registered students
* Access the student's Loan Workspace

### Loan Management

* Create multiple loans for each student
* Select loan type:

  * Tuition
  * Books
  * Living Expenses
* Set loan status:

  * Pending
  * Approved
  * Disbursed
* View all loans belonging to a selected student

### Payment Management

* Record payments for each loan
* Enter:

  * Payment Amount
  * Payment Date
  * Payment Method (Cash, Bank Transfer, or Online Payment)
* View payment history
* Automatically calculate:

  * Total Amount Paid
  * Remaining Loan Balance

---

# Database Structure

## Students Table

Stores student information.

### Primary Key

* `id`

---

## Loans Table

Stores loan records for each student.

### Fields

* `id`
* `student_id` (Foreign Key)
* `loan_amount`
* `loan_type`
* `status`
* `created_at`

### Relationship

One Student → Many Loans

---

## Payments Table

Stores payment records for each loan.

### Fields

* `id`
* `loan_id` (Foreign Key)
* `payment_amount`
* `payment_date`
* `payment_method`
* `created_at`

### Relationship

One Loan → Many Payments

---

# How to Use

## Step 1: Open the Student List

After launching the Program, the Student Management page displays all registered students.

Available actions include:

* Add Student
* Edit Student
* Delete Student
* Loans

---

## Step 2: Manage Student Loans

Click the **Loans** button beside a student.

This opens the **Loan Workspace**, where all loans associated with the selected student are displayed.

### Add a Loan

Complete the loan form with the following information:

| Field       | Description                        |
| ----------- | ---------------------------------- |
| Loan Amount | Total amount of the loan           |
| Loan Type   | Tuition, Books, or Living Expenses |
| Status      | Pending, Approved, or Disbursed    |

Click **Save** to create the loan.

---

## Step 3: View Existing Loans

Click the **View** button to display all loans for the selected student.

The loan table displays:

* Loan ID
* Loan Amount
* Loan Type
* Status
* Date Created

Each loan includes a **Payments** button.

---

## Step 4: Manage Loan Payments

Click the **Payments** button to open the Payment Workspace.

### Record a Payment

Fill in the payment form.

| Field          | Description                            |
| -------------- | -------------------------------------- |
| Payment Amount | Amount paid                            |
| Payment Date   | Date of payment                        |
| Payment Method | Cash, Bank Transfer, or Online Payment |

Click **Save** to record the payment.

---

## Step 5: View Payment History

Click the **View** button.

The system displays all payments made for the selected loan.

Information shown includes:

* Payment Amount
* Payment Date
* Payment Method

The system also computes:

* **Total Paid** – Total of all recorded payments.
* **Remaining Balance** – Loan Amount minus Total Paid.

---

# Application Workflow

```text
Student
   │
   ├── Loans
   │      │
   │      ├── Loan #1
   │      │      ├── Payment 1
   │      │      ├── Payment 2
   │      │      └── Payment 3
   │      │
   │      └── Loan #2
   │             ├── Payment 1
   │             └── Payment 2
```

---

# Technologies Used

* HTML 
* CSS
* PHP
* MYSQL
* Git & GitHub

---

# Key Features

* Student CRUD operations
* One-to-Many relationship between Students and Loans
* One-to-Many relationship between Loans and Payments
* Foreign key constraints for data integrity
* Automatic loan balance calculation
* Payment history tracking
* Organized loan and payment workspaces

---

# Team Collaboration

This project was developed using collaborative software development practices.

The team utilized:

* GitHub Repository
* Git Branches
* Pull Requests
* Code Reviews
* Individual role assignments
* Collaborative version control throughout development

---

# Future Improvements

* User authentication and role management
* Search and filter functionality
* Payment receipt generation
* Loan approval workflow
* Dashboard with loan statistics
* Export reports to PDF or Excel
* Email notifications for payment reminders

---

## License

This project was developed for academic purposes as part of the Student Loan Management System project.
