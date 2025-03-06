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

// Get JSON input for verification process.
$data = json_decode(file_get_contents("php://input"), true); 

if (!isset($data['email']) || !isset($data['otp_code'])) {
    echo json_encode(['success' => false, 'message' => 'Email or OTP code not provided.']);
    exit;
}

$emailInput = $data['email']; 
$otpInput = $data['otp_code']; 

// Fetch user from database by email and check OTP code.
$sqlFetchUserOtp = "SELECT * FROM users WHERE email='$emailInput' AND otpCode='$otpInput'";
$resultFetchOtpUser = $conn->query($sqlFetchUserOtp);

if ($resultFetchOtpUser->num_rows > 0) { 
    // Set user email in session
    $_SESSION['userEmail'] = $emailInput; 
    echo json_encode(['success' => true]); 
} else { 
    echo json_encode(['success' => false, 'message' => 'Invalid OTP or Email.']); 
} 

$conn->close(); 
?>