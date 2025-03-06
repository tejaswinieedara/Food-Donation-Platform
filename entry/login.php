<?php
session_start();

$servername = "localhost";
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "userAuth";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);
$usernameInput = $data['username'];
$passwordInput = $data['password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $usernameInput);
$stmt->execute();
$resultFetchUser = $stmt->get_result();

if ($resultFetchUser->num_rows > 0) {
    $userData = $resultFetchUser->fetch_assoc();
    
    if (password_verify($passwordInput, $userData['passwordHash'])) {
        $_SESSION['username'] = $userData['username'];
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials.']);
}

$stmt->close();
$conn->close();
?>