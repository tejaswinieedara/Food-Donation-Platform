<?php
// Database connection
$servername = "localhost";
$username = "root"; // Your MySQL username (default is 'root')
$password = ""; // Your MySQL password (default is empty)
$dbname = "userAuth";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'];
$email = $data['email'];
$password = $data['password'];
$otpCode = $data['otpCode'];

// Check if username or email already exists
$sqlCheck = "SELECT * FROM users WHERE username='$username' OR email='$email'";
$resultCheck = $conn->query($sqlCheck);

if ($resultCheck->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Username or Email already exists.']);
} else {
    // Hash the password
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    // Insert into database
    $sqlInsert = "INSERT INTO users (username, email, passwordHash, otpCode) VALUES ('$username', '$email', '$passwordHash', '$otpCode')";
    
    if ($conn->query($sqlInsert) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error creating user.']);
    }
}

$conn->close();
?>