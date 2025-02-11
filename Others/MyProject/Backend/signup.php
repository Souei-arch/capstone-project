<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = ""; // Use your MySQL password
$dbname = "project";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $cpassword = trim($_POST['cpassword']);

    // Validate inputs
    if (empty($fullname) || empty($email) || empty($username) || empty($password) || empty($cpassword)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email format."]);
        exit;
    }

    if (!preg_match("/^[a-zA-Z0-9_-]{3,16}$/", $username)) {
        echo json_encode(["status" => "error", "message" => "Invalid username format."]);
        exit;
    }

    if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/", $password)) {
        echo json_encode(["status" => "error", "message" => "Password must be at least 8 characters long and include one letter and one number."]);
        exit;
    }

    if ($password !== $cpassword) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match."]);
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email or username already exists."]);
        exit;
    }
    $stmt->close();

    // Insert user into the database
    $stmt = $conn->prepare("INSERT INTO users (fullname, email, username, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $fullname, $email, $username, $hashedPassword);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Sign-Up successful."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }
    $stmt->close();
}

$conn->close();
?>
