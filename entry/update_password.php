<?php
session_start(); // Start session to access user email

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection details.
$servername = "localhost";
$username = "root"; // Default MySQL username.
$password = ""; // Default MySQL password.
$dbname = "userAuth"; 

$conn = new mysqli($servername, $username, $password, $dbname); 

// Check connection status.
if ($conn->connect_error) { 
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
} 

// Get JSON input for updating the password.
$data = json_decode(file_get_contents("php://input"), true); 

if (!isset($data['newPassword'])) {
    echo json_encode(['success' => false, 'message' => 'No password provided.']);
    exit;
}

$newPassword = $data['newPassword']; 

// Hash the new password.
$newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT); 

// Update user record with new hashed password.
// Ensure that userEmail is set in session after OTP verification.




if (!isset($_SESSION['userEmail'])) {
    echo json_encode(['success' => false, 'message' => 'password changed successfully.']);
    exit;
}

$emailInput = $_SESSION['userEmail']; // Retrieve user's email from session.

$sqlUpdate = "UPDATE users SET passwordHash='$newPasswordHash', otpCode=NULL WHERE email='$emailInput'";
if ($conn->query($sqlUpdate) === TRUE) { 
    echo json_encode(['success' => true]); 
} else { 
    echo json_encode(['success' => false, 'message' => 'Error updating password.']); 
} 

$conn->close(); 
?>