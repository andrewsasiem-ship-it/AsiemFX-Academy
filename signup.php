<?php
// signup.php - Handles user registration and inserts into MySQL database

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "asiemfx_academy";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $gender = trim($_POST['gender']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $number = trim($_POST['number']);

    // Basic validation
    if (!$firstName || !$lastName || !$gender || !$email || !$password || !$number) {
        echo "<script>alert('Please fill in all fields!'); window.history.back();</script>";
        exit();
    }
    if (!preg_match('/^[0-9]{10}$/', $number)) {
        echo "<script>alert('Phone number must be exactly 10 digits.'); window.history.back();</script>";
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Please enter a valid email address.'); window.history.back();</script>";
        exit();
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "<script>alert('An account with this email already exists!'); window.history.back();</script>";
        exit();
    }
    $stmt->close();

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (firstName, lastName, gender, email, password, number) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $firstName, $lastName, $gender, $email, $password, $number);
    if ($stmt->execute()) {
        echo "<script>alert('Account created successfully!'); window.location.href='login.html';</script>";
    } else {
        echo "<script>alert('Error: Could not create account.'); window.history.back();</script>";
    }
    $stmt->close();
}
$conn->close();
?>
